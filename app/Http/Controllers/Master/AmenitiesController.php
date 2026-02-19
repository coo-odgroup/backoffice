<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AmenitiesController extends Controller
{
 public function amenities()
    {
        return view('master.amenities');
    }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';

            $dataQuery = DB::table('mst_amenities as a')
                ->leftJoin('mst_amenity_categories as ac', 'a.category_id', '=', 'ac.id')
                ->leftJoin('users as u', 'u.id', '=', 'a.created_by')
                ->select(
                    'a.id as amenity_id',
                    'a.category_id',
                    'a.amenity_name',
                    'ac.category_name',
                    'a.description',
                    'a.icon',
                    'a.is_paid',
                    'a.is_seat_specific',
                    'a.created_at',
                    'a.created_by',
                    'u.name as created_by_name',
                    'a.active_status'
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('a.amenity_name', 'like', "%{$txtSearch}%")
                        ->orWhere('a.description', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('a.active_status', $selStatus);
            }

            $count = $dataQuery->count('a.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'a.amenity_name', 3 => 'ac.category_name', 4 => 'a.description', 5 => 'a.icon', 6 => 'a.is_paid', 7 => 'a.is_seat_specific', 8 => 'a.created_by', 9 => 'a.active_status'];

                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 'a.amenity_name';
                $orderType     = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'a.amenity_name';
                $orderType   = 'asc';
            }

            $dataQuery = $dataQuery->orderBy($orderColumn, $orderType);

            // Pagination
            if ($length == -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery->limit($length)
                    ->offset($start)
                    ->get();
            }
            // Format Data
            if (count($arrRes) > 0) {

                foreach ($arrRes as $val) {
                    $val->created_date  = date('d-M-Y H:i:s', strtotime($val->created_at));
                    $val->is_active     = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_amenity_id   = Crypt::encryptString($val->amenity_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in AmenitiesController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'AmenitiesController',
                'Method'     => 'dataTableView',
                'Error'      => $errorMsg
            ]);

            $recordsTotal     = 0;
            $recordsFiltered  = 0;
            $data            = [];
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function add($encId = null)
    {

        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = "admin/amenitycategory/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = Amenity::select('id', 'category_name', 'description', 'display_order');

                $dataResQry = $dataResQry->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("amenitycategory");
                }
                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/amenitycategory";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'category_name' => 'bail|required'
                ], [
                    'category_name.required' => 'Amenity Category Name cannot be left blank.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();

                    $category_name = htmlEncode(request('category_name'));
                    $description = htmlEncode(request('description'));

                    $duplicate = Amenity::select('id')->where(['category_name' => $category_name]);

                    if ($id != 0) {
                        $duplicate->where('id', '!=', $id);
                    }

                    if ($duplicate->exists()) {
                        return back()->with([
                            'level'     => 'danger',
                            'message'   => 'Amenity Category already exist'
                        ])->withInput();
                    } else {
                        $obj = ($id != 0) ? Amenity::find($id) : new Amenity();
                        $obj->category_name = $category_name;
                        $obj->description = $description;
                        $obj->created_by = 1;
                        $obj->active_status = 1;

                        if ($id != 0) {
                            $obj->updated_by = 1;
                        }

                        $obj->save();

                        session()->flash('level', 'success');
                        session()->flash('message', 'Amenity Category ' . (($id != 0) ? 'updated' : 'created') . ' successfully.');
                    }

                    DB::commit();
                    return redirect($redirectPage);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'AmenityCategoryController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            DB::rollBack();

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            return back()->with([
                'level'     => 'danger',
                'message'   => $errorMsg
            ])->withInput();
        }
        return view('Master.addAmenities', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
