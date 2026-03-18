<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;

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
            $amenityCategory = (request('amenityCategory') !== null && request('amenityCategory') !== '') ? (int)request('amenityCategory') : '';

            $dataQuery = DB::table('mst_amenities as a')
                ->select(
                    'a.id as amenity_id',
                    'a.category_id',
                    'a.amenity_name',
                    'a.description',
                    'a.icon',
                    'a.is_paid',
                    'a.is_seat_specific',
                    'a.sequence_no',
                    'a.created_at',
                    'a.created_by',
                    'a.updated_at',
                    'a.updated_by',
                    'a.active_status',
                    DB::raw('(SELECT category_name FROM mst_amenity_categories WHERE id = a.category_id LIMIT 1) as category_name'),
                    DB::raw('(SELECT name FROM users WHERE id = a.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = a.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('a.amenity_name', 'like', "%{$txtSearch}%")
                        ->orWhere('a.description', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($amenityCategory) && $amenityCategory != '') {
                $dataQuery->where('a.category_id', $amenityCategory);
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

                $columns = [2 => 'a.amenity_name', 3 => 'category_name', 4 => 'a.description', 8 => 'a.created_at', 9 => 'a.active_status'];

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
                    $val->updated_date  = ($val->updated_at != null) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : null;
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

                $redirectPage = "admin/amenities/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = Amenity::select(
                    'id',
                    'category_id',
                    'amenity_name',
                    'description',
                    'icon',
                    'is_paid',
                    'is_seat_specific'
                )->where('id', $id)->first();

                if (!$dataResQry) {
                    return redirect("amenities");
                }

                $data['row'] = $dataResQry;

            } else {
                $redirectPage = "admin/amenities";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'amenityCategory' => 'required',
                    'amenity_name'    => 'required',
                    'icon'            => 'required'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $category_id      = (int) request('amenityCategory');
                $amenity_name     = htmlEncode(request('amenity_name'));
                $icon             = htmlEncode(request('icon'));
                $is_paid          = (int) request('is_paid');
                $is_seat_specific = (int) request('is_seat_specific');
                $description      = htmlEncode(request('description'));

                $duplicate = Amenity::where('amenity_name', $amenity_name);

                if ($id != 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    DB::rollBack();

                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Amenity already exist'
                    ])->withInput();
                }

                if ($id != 0) {

                    $oldData = Amenity::find($id);

                    $newData = [
                        'category_id'      => $category_id,
                        'amenity_name'     => $amenity_name,
                        'icon'             => $icon,
                        'is_paid'          => $is_paid,
                        'is_seat_specific' => $is_seat_specific,
                        'description'      => $description,
                    ];

                    $oldChanged = [];
                    $newChanged = [];

                    foreach ($newData as $key => $value) {

                        $oldValue = $oldData->$key ?? null;

                        if (trim((string)$oldValue) !== trim((string)$value)) {
                            $oldChanged[$key] = $oldValue;
                            $newChanged[$key] = $value;
                        }
                    }

                    if (!empty($newChanged)) {
                        app(CommonController::class)->auditLog(
                            'mst_amenities',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    // Save
                    $oldData->fill($newData);
                    $oldData->updated_by = 1;
                    $oldData->save();
                }

                else {

                    $row = [
                        'category_id'      => $category_id,
                        'amenity_name'     => $amenity_name,
                        'icon'             => $icon,
                        'is_paid'          => $is_paid,
                        'is_seat_specific' => $is_seat_specific,
                        'description'      => $description,
                        'created_by'       => 1,
                        'active_status'    => 1,
                        'created_at'       => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_amenities',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    Amenity::create($row);
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash('message', 'Amenity ' . ($id ? 'updated' : 'created') . ' successfully.');

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'AmenitiesController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addAmenities', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
