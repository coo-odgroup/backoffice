<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\States;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StateController extends Controller
{
    public function states()
    {
        return view('master.states');
    }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {
            
            $txtSearch= htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';

            $dataQuery = DB::table('mst_states as s')
                            ->leftJoin('users as u', 'u.id', '=', 's.created_by')
                            ->select(
                                's.id as state_id',
                                's.state_name',
                                's.created_at',
                                's.created_by',
                                'u.name as created_by_name',
                                's.active_status'
                            );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('s.state_name', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('s.active_status', $selStatus);
            }

            $count = $dataQuery->count('s.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 's.state_name', 3 => 's.created_at', 4 => 's.created_by', 4 => 's.active_status'];

                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 'c.city_name';
                $orderType     = $orderBy[0]['dir'];

            } else {
                $orderColumn = 's.state_name';
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
                    $val->enc_state_id   = Crypt::encryptString($val->state_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;


        } catch (\Throwable $t) {

            Log::info("Exception occurred in StateController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'StateController',
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

    public function add($encId = null) {

        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = "admin/states/edit/".$encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = States::select('id', 'state_name');

                $dataResQry = $dataResQry->where('id', $id)->first();

                if(empty($dataResQry)){
                    return redirect("states");
                }
                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/states";
            }

            if(request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'txtCity' => 'bail|required',
                    'txtCityAlias' => 'bail|required',
                    'selState' => 'required',
                ], [
                    'txtCity.required' => 'City Name cannot be left blank.',
                    'txtCityAlias.required' => 'City Alias cannot be left blank.',
                    'selState.required' => 'State cannot be left blank.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();

                    $selState  = (int)request('selState');
                    $selDistrict  = (int)request('selDistrict');
                    $txtCity  = htmlEncode(request('txtCity'));
                    $txtCityAlias = htmlEncode(request('txtCityAlias'));
                  

                    $duplicate = Cities::select('id')
                                        ->where(['city_name' => $txtCity,
                                                 'alias'     => $txtCityAlias]);

                    if ($id!=0) {
                        $duplicate->where('id', '!=', $id);
                    }

                    if ($duplicate->exists()) {
                        return back()->with([
                            'level'     => 'danger',
                            'message'   => 'City already exist'
                        ])->withInput();
                    }
                    else {
                        $obj = ($id!=0) ? Cities::find($id) : new Cities();

                        $obj->state_id       = $selState;
                        $obj->district_id       = $selDistrict ?? null;
                        $obj->city_name      = $txtCity;
                        $obj->alias       = $txtCityAlias;
                        $obj->created_by      = 1;     //session('admin_session.user_id');
                        $obj->active_status      = 1;
                        if($id != 0){
                            $obj->updated_by      = 1; //session('admin_session.user_id');
                        }

                        $obj->save();
                        
                        request()->session()->flash('level', 'success');
                        request()->session()->flash('message', 'City '.(($id!=0) ?
                                                    'updated': 'created').' successfully.');
                    }
                
                    DB::commit();
                    return redirect($redirectPage);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'CitiesController',
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
        return view('Master.addStates',compact('data'));
    }
}
