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

class AxleTypeController extends Controller
{
    public function axleType()
    {
        return view('master.axleType');
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

            $dataQuery = DB::table('mst_axle_type as m')
                ->leftJoin('users as u1', 'u1.id', '=', 'm.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'm.updated_by')
                ->select(
                    'm.id as axleTypeId',
                    'm.axle_type',
                    'm.created_at',
                    'm.updated_at',
                    'm.active_status',
                    'u1.name as created_by_name',
                    'u2.name as updated_by_name'
                );

            // Search filter
            if (!empty($txtSearch)) {
                $dataQuery->where('m.axle_type', 'like', "%{$txtSearch}%");
            }

            // Status filter
            if (!empty($selStatus)) {
                $dataQuery->where('m.active_status', $selStatus);
            }

            $count = $dataQuery->count('m.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            // Ordering
            if (!empty(request('order'))) {

                $columns = [
                    2 => 'm.axle_type',
                    3 => 'm.created_at',
                    4 => 'm.active_status'
                ];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'm.axle_type';
                $orderType = $orderBy[0]['dir'];

            } else {
                $orderColumn = 'm.axle_type';
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

                $val->enc_axleTypeId = Crypt::encryptString($val->axleTypeId);
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;

        } catch (\Throwable $t) {

            Log::error("AxleTypeController@dataTableView Error", [
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

                $redirectPage = "admin/axleType/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = DB::table('mst_axle_type')
                    ->select('id', 'axle_type')
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect()->route('axleType.index');
                }

                $data['row'] = $dataResQry;

            } else {

                $id = 0;
                $redirectPage = route('axleType.index');
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'axleType' => 'bail|required',
                ], [
                    'axleType.required' => 'Axle Type cannot be blank.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $axleType = htmlEncode(request('axleType'));

                $duplicate = DB::table('mst_axle_type')
                    ->where('axle_type', $axleType);

                if ($id != 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Axle Type already exists'
                    ])->withInput();
                }

                if ($id != 0) {

                    $oldData = DB::table('mst_axle_type')
                        ->where('id', $id)
                        ->first();

                    $newData = [
                        'axle_type' => $axleType
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
                            'mst_axle_type',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    DB::table('mst_axle_type')
                        ->where('id', $id)
                        ->update([
                            'axle_type' => $axleType,
                            'updated_by' => auth()->id(),
                            'updated_at' => now()
                        ]);

                } else {

                    $row = [
                        'axle_type'    => $axleType,
                        'created_by'   => auth()->id(),
                        'active_status'=> 1,
                        'created_at'   => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_axle_type',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    DB::table('mst_axle_type')->insert($row);
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Axle Type ' . (($id != 0) ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'AxleTypeController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addAxleType', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
