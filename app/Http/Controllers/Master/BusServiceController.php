<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Validator;

class BusServiceController extends Controller
{
    public function busService()
    {
        return view('master.busService');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '')
                ? (int)request('selStatus') : '';

            $dataQuery = DB::table('mst_bus_service as m')
                ->leftJoin('users as u1', 'u1.id', '=', 'm.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'm.updated_by')
                ->select(
                    'm.id as busService_id',
                    'm.bus_service_name',
                    'm.description',
                    'm.created_at',
                    'm.updated_at',
                    'm.active_status',
                    'u1.name as created_by_name',
                    'u2.name as updated_by_name'
                );

            // Search filter
            if (!empty($txtSearch)) {
                $dataQuery->where('m.bus_service_name', 'like', "%{$txtSearch}%");
            }

            // Status filter
            if ($selStatus !== '') {
                $dataQuery->where('m.active_status', $selStatus);
            }
            $count = $dataQuery->count('m.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            // Ordering
            if (!empty(request('order'))) {

                $columns = [
                    2 => 'm.bus_service_name',
                    3 => 'm.description',
                    4 => 'm.created_at',
                    5 => 'm.active_status'
                ];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'm.bus_service_name';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'm.bus_service_name';
                $orderType = 'asc';
            }

            $dataQuery->orderBy($orderColumn, $orderType);

            if ($length == -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery->limit($length)
                    ->offset($start)
                    ->get();
            }

            foreach ($arrRes as $val) {
                $val->created_date = date('d-M-Y H:i:s', strtotime($val->created_at));
                $val->updated_date = $val->updated_at
                    ? date('d-M-Y H:i:s', strtotime($val->updated_at))
                    : null;

                $val->is_active = ($val->active_status == 1) ? 'Active' : 'Inactive';

                $val->enc_busService_id = Crypt::encryptString($val->busService_id);
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error("BusServiceController@dataTableView Error", [
                'message' => $t->getMessage()
            ]);
        }

        return response()->json([
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    public function add($encId = null)
    {
        $data = [];
        $data['strPage']   = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset']  = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = route('busService.edit', $encId);

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = DB::table('mst_bus_service')
                    ->select('id','bus_service_name','description')
                    ->where('id', $id)
                    ->first();

                if (!$dataResQry) {
                    return redirect()->route('busService.index');
                }

                $data['row'] = $dataResQry;

            } else {

                $id = 0;
                $redirectPage = route('busService.index');
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'busService' => 'bail|required|max:100'
                ], [
                    'busService.required' => 'Bus Service Name cannot be blank.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $busService  = htmlEncode(request('busService'));
                $description = htmlEncode(request('description'));

                $duplicate = DB::table('mst_bus_service')
                    ->where('bus_service_name', $busService);

                if ($id != 0) {
                    $duplicate->where('id','!=',$id);
                }

                if ($duplicate->exists()) {
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Bus Service already exists'
                    ])->withInput();
                }

                if ($id != 0) {

                    $oldData = DB::table('mst_bus_service')
                        ->where('id', $id)
                        ->first();

                    $newData = [
                        'bus_service_name' => $busService,
                        'description'      => $description
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
                            'mst_bus_service',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    DB::table('mst_bus_service')
                        ->where('id',$id)
                        ->update([
                            'bus_service_name' => $busService,
                            'description'      => $description,
                            'updated_by'       => auth()->id(),
                            'updated_at'       => now()
                        ]);

                } else {

                    $row = [
                        'bus_service_name' => $busService,
                        'description'      => $description,
                        'created_by'       => auth()->id(),
                        'active_status'    => 1,
                        'created_at'       => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_bus_service',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    DB::table('mst_bus_service')->insert($row);
                }

                DB::commit();

                session()->flash('level','success');
                session()->flash(
                    'message',
                    'Bus Service '.(($id != 0) ? 'updated' : 'created').' successfully.'
                );

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error",[
                'Controller' => 'BusServiceController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addBusService', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
