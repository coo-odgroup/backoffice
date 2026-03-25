<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\ApiApps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Validator;

class ApiAppsController extends Controller
{
    public function apiApps()
    {
        return view('master.apiApps');
    }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';

            $dataQuery = DB::table('api_apps as aa')
                ->select(
                    'aa.id as api_app_id',
                    'aa.app_name',
                    'aa.app_code',
                    'aa.created_at',
                    'aa.created_by',
                    'aa.updated_at',
                    'aa.updated_by',
                    'aa.active_status',
                    DB::raw('(SELECT name FROM users WHERE id = aa.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = aa.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('aa.app_code', 'like', "%{$txtSearch}%")
                        ->orWhere('aa.app_code', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('aa.active_status', $selStatus);
            }

            $count = $dataQuery->count('aa.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'aa.app_name', 3 => 'aa.app_code', 4 => 'aa.created_at', 5 => 'aa.created_by', 6 => 'aa.active_status'];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'aa.app_name';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'aa.app_name';
                $orderType = 'asc';
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
                    $val->created_date = date('d-M-Y H:i:s', strtotime($val->created_at));
                    $val->updated_date = ($val->updated_at != null) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : null;
                    $val->is_active = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_api_app_id = Crypt::encryptString($val->api_app_id);
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in ApiAppsController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'ApiAppsController',
                'Method'     => 'dataTableView',
                'Error'      => $errorMsg
            ]);

            $recordsTotal = 0;
            $recordsFiltered = 0;
            $data = [];
        }

        return response()->json([
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
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

                $redirectPage = "admin/apiapps/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = ApiApps::select('id', 'app_name', 'app_code')
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect("apiapps");
                }

                $data['row'] = $dataResQry;

            } else {
                $id = 0;
                $redirectPage = "admin/apiapps";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'app_name' => 'bail|required|max:100',
                    'app_code' => 'bail|required|max:20'
                ], [
                    'app_name.required' => 'App Name cannot be left blank.',
                    'app_name.max' => 'App Name cannot exceed 100 characters.',
                    'app_code.required' => 'App Code cannot be left blank.',
                    'app_code.max' => 'App Code cannot exceed 20 characters.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $app_name = htmlEncode(request('app_name'));
                $app_code = htmlEncode(request('app_code'));

                $duplicate = ApiApps::select('id')->where(['app_name' => $app_name]);

                if ($id != 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    return back()->with([
                        'level' => 'danger',
                        'message' => 'App Name already exist'
                    ])->withInput();
                }

                if ($id != 0) {

                    $oldData = ApiApps::find($id);

                    $newData = [
                        'app_name' => $app_name,
                        'app_code' => $app_code
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
                            'mst_api_apps',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    $oldData->app_name = $app_name;
                    $oldData->app_code = $app_code;
                    $oldData->updated_by = 1;
                    $oldData->save();

                } else {

                    $row = [
                        'app_name'     => $app_name,
                        'app_code'     => $app_code,
                        'created_by'   => 1,
                        'active_status'=> 1,
                        'created_at'   => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_api_apps',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    $obj = new ApiApps();
                    $obj->fill($row);
                    $obj->save();
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'App Name and Code ' . (($id != 0) ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'ApiAppsController',
                'Method' => $method,
                'Error' => $t->getMessage()
            ]);

            return back()->with([
                'level' => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addApiApps', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
