<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Cities;
use App\Models\Master\Districts;
use App\Models\Master\States;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DistrictController extends Controller
{
    public function district()
    {
        return view('master.district');
    }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $selState = (request('selState') !== null && request('selState') !== '') ? (int)request('selState') : '';

            $dataQuery = DB::table('mst_districts as d')
                ->select(
                    'd.id as district_id',
                    'd.district_name',
                    'd.created_at',
                    'd.created_by',
                    'd.updated_at',
                    'd.updated_by',
                    'd.active_status',
                    DB::raw('(SELECT state_name FROM mst_states WHERE id = d.state_id LIMIT 1) as state_name'),
                    DB::raw('(SELECT name FROM users WHERE id = d.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = d.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('d.district_name', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($selState) && $selState != 0) {
                $dataQuery->where('d.state_id', $selState);
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('d.active_status', $selStatus);
            }

            $count = $dataQuery->count('d.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'd.district_name', 3 => 'd.created_at', 4 => 'd.created_by', 5 => 'd.active_status'];

                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 'd.district_name';
                $orderType     = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'd.district_name';
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
                    $val->enc_district_id   = Crypt::encryptString($val->district_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in DistrictController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'DistrictController',
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

                $redirectPage = "admin/district/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = Districts::select('id', 'district_name', 'state_id')->where('active_status', 1);

                $dataResQry = $dataResQry->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("district");
                }
                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/district";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'txtDistrict' => 'bail|required',
                    'selState' => 'required',
                ], [
                    'txtDistrict.required' => 'District Name cannot be left blank.',
                    'selState.required' => 'State cannot be left blank.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();

                    $selState  = (int)request('selState');
                    $txtDistrict  = htmlEncode(request('txtDistrict'));


                    $duplicate = Districts::select('id')
                        ->where([
                            'district_name' => $txtDistrict
                        ]);

                    if ($id != 0) {
                        $duplicate->where('id', '!=', $id);
                    }

                    if ($duplicate->exists()) {
                        return back()->with([
                            'level'     => 'danger',
                            'message'   => 'District already exist'
                        ])->withInput();
                    } else {
                        $obj = ($id != 0) ? Districts::find($id) : new Districts();

                        $obj->state_id = $selState;
                        $obj->district_name = $txtDistrict;
                        $obj->created_by = 1;
                        $obj->active_status = 1;
                        if ($id != 0) {
                            $obj->updated_by = 1;
                        }

                        $obj->save();

                        session()->flash('level', 'success');
                        session()->flash('message', 'District ' . (($id != 0) ?
                            'updated' : 'created') . ' successfully.');
                    }

                    DB::commit();
                    return redirect($redirectPage);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'DistrictsController',
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
        return view('Master.addDistricts', compact('data'));
    }

    public function edit($encId){
        return $this->add($encId);
    }
}
