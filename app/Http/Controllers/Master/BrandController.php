<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function brand()
    {
        return view('master.brand');
    }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $countrySearch = (request('countrySearch') !== null && request('countrySearch') !== '') ? (int)request('countrySearch') : '';

            $dataQuery = DB::table('mst_brand as b')
                ->leftJoin('mst_countries as c', 'c.id', '=', 'b.country')
                ->select(
                    'b.id as brand_id',
                    'b.brand_name',
                    'c.name as country',
                    'b.created_at',
                    'b.created_by',
                    'b.updated_at',
                    'b.updated_by',
                    'b.active_status',
                    DB::raw('(SELECT name FROM users WHERE id = b.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = b.updated_by LIMIT 1) as updated_by_name')
                );
            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('b.brand_name', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($countrySearch) && $countrySearch != '') {
                $dataQuery->where('c.name', $countrySearch);
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('b.active_status', $selStatus);
            }

            $count = $dataQuery->count('b.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'b.brand_name', 3 => 'b.created_at', 4 => 'b.created_by', 5 => 'b.active_status'];

                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 'b.brand_name';
                $orderType     = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'b.brand_name';
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
                    $val->enc_bustype_id   = Crypt::encryptString($val->brand_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in BusTypeController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'BusTypeController',
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

                $redirectPage = "admin/bustype/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = BusType::select('id', 'class_id', 'brand_name');

                $dataResQry = $dataResQry->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("bustype");
                }
                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/bustype";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'classType' => 'required',
                    'brand' => 'bail|required'
                ], [
                    'classType.required' => 'Class Name cannot be left blank.',
                    'brand.required' => 'Bus Type Name cannot be left blank.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();

                    $classType = (int)request('classType');
                    $brand = htmlEncode(request('brand'));

                    $duplicate = BusType::select('id')->where(['brand_name' => $brand]);

                    if ($id != 0) {
                        $duplicate->where('id', '!=', $id);
                    }

                    if ($duplicate->exists()) {
                        return back()->with([
                            'level'     => 'danger',
                            'message'   => 'Bus Type already exist'
                        ])->withInput();
                    } else {
                        $obj = ($id != 0) ? BusType::find($id) : new BusType();
                        $obj->class_id = $classType;
                        $obj->brand_name = $brand;
                        $obj->created_by = 1;
                        $obj->active_status = 1;

                        if ($id != 0) {
                            $obj->updated_by = 1;
                        }

                        $obj->save();

                        session()->flash('level', 'success');
                        session()->flash('message', 'Bus Type ' . (($id != 0) ? 'updated' : 'created') . ' successfully.');
                    }

                    DB::commit();
                    return redirect($redirectPage);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'BusTypeController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            DB::rollBack();

            $errorMsg = config('constantbt.SERVER_ERROR_MESSAGE');

            return back()->with([
                'level'     => 'danger',
                'message'   => $errorMsg
            ])->withInput();
        }
        return view('Master.addBusType', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
