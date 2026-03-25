<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\BusType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Validator;

class BusTypeController extends Controller
{
    public function busType()
    {
        return view('master.busType');
    }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $classSearch = (request('classSearch') !== null && request('classSearch') !== '') ? (int)request('classSearch') : '';

            $dataQuery = DB::table('mst_bus_type as bt')
                ->select(
                    'bt.id as bustype_id',
                    'bt.class_id',
                    'bt.bus_type',
                    'bt.created_at',
                    'bt.created_by',
                    'bt.updated_at',
                    'bt.updated_by',
                    'bt.active_status',
                    DB::raw('(SELECT name FROM users WHERE id = bt.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = bt.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('bt.bus_type', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($classSearch) && $classSearch != '') {
                $dataQuery->where('bt.class_id', $classSearch);
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('bt.active_status', $selStatus);
            }

            $count = $dataQuery->count('bt.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'bt.bus_type', 3 => 'bt.created_at', 4 => 'bt.created_by', 5 => 'bt.active_status'];

                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 'bt.bus_type';
                $orderType     = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'bt.bus_type';
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
                    $val->enc_bustype_id   = Crypt::encryptString($val->bustype_id);
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

                $dataResQry = BusType::select('id', 'class_id', 'bus_type')
                    ->where('id', $id)
                    ->first();

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
                    'busType'   => 'bail|required'
                ], [
                    'classType.required' => 'Class Name cannot be left blank.',
                    'busType.required'   => 'Bus Type Name cannot be left blank.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $classType = (int) request('classType');
                $busType   = htmlEncode(request('busType'));

                $duplicate = BusType::select('id')->where(['bus_type' => $busType]);

                if ($id != 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Bus Type already exist'
                    ])->withInput();
                }

                if ($id != 0) {

                    $oldData = BusType::find($id);

                    $newData = [
                        'class_id' => $classType,
                        'bus_type' => $busType
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
                            'mst_bus_type',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    $oldData->class_id = $classType;
                    $oldData->bus_type = $busType;
                    $oldData->updated_by = 1;
                    $oldData->save();

                } else {

                    $row = [
                        'class_id'      => $classType,
                        'bus_type'      => $busType,
                        'created_by'    => 1,
                        'active_status' => 1,
                        'created_at'    => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_bus_type',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    $obj = new BusType();
                    $obj->fill($row);
                    $obj->save();
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Bus Type ' . (($id != 0) ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'BusTypeController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constantbt.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addBusType', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
