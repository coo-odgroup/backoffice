<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DepartmentTypeController extends Controller
{
    public function departmentType()
    {
        return view('master.departmentType');
    }

    public function dataTableView()
    {
        
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '')
                ? (int)request('selStatus')
                : '';

            $dataQuery = DB::table('mst_department_type as m')
                ->select(
                    'm.id as department_id',
                    'm.department_name',
                    'm.department_code',
                    'm.description',
                    'm.created_at',
                    'm.updated_at',
                    'm.active_status',
                    DB::raw('(SELECT name FROM users WHERE id = m.created_by) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = m.updated_by) as updated_by_name')
                );

            if (!empty($txtSearch)) {

                $dataQuery->where(function ($query) use ($txtSearch) {

                    $query->where('m.department_name', 'like', "%{$txtSearch}%")
                        ->orWhere('m.department_code', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== '') {
                $dataQuery->where('m.active_status', $selStatus);
            }

            $count = $dataQuery->count('m.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            if (!empty(request('order'))) {

                $columns = [
                    2 => 'm.department_name',
                    3 => 'm.department_code',
                    4 => 'm.description',
                    5 => 'm.created_at',
                    6 => 'm.active_status'
                ];

                $orderBy = request('order');

                $orderColumn = $columns[$orderBy[0]['column']] ?? 'm.department_name';
                $orderType = $orderBy[0]['dir'];
            } else {

                $orderColumn = 'm.department_name';
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
                $val->enc_department_id = Crypt::encryptString($val->department_id);
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error("DepartmentController@dataTableView Error", [
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

                $redirectPage = route('department-type.edit', $encId);

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = DB::table('mst_department_type')
                    ->select(
                        'id',
                        'department_name',
                        'department_code',
                        'description'
                    )
                    ->where('id', $id)
                    ->first();

                if (!$dataResQry) {
                    return redirect()->route('department-type.index');
                }

                $data['row'] = $dataResQry;
            } else {

                $id = 0;
                $redirectPage = route('department-type.index');
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'department'       => 'required|max:100',
                    'department_code'  => 'required|max:100',
                    'department_desc'  => 'nullable|max:255'
                ], [
                    'department.required'      => 'Department Name cannot be blank.',
                    'department_code.required' => 'Department Code cannot be blank.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $departmentName = htmlEncode(request('department'));
                $departmentCode = strtoupper(htmlEncode(request('department_code')));
                $description    = htmlEncode(request('department_desc'));

                // Duplicate Department Name
                $duplicateName = DB::table('mst_department_type')
                    ->where('department_name', $departmentName);

                if ($id != 0) {
                    $duplicateName->where('id', '!=', $id);
                }

                if ($duplicateName->exists()) {

                    DB::rollBack();

                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Department Name already exists.'
                    ])->withInput();
                }

                // Duplicate Department Code
                $duplicateCode = DB::table('mst_department_type')
                    ->where('department_code', $departmentCode);

                if ($id != 0) {
                    $duplicateCode->where('id', '!=', $id);
                }

                if ($duplicateCode->exists()) {

                    DB::rollBack();

                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Department Code already exists.'
                    ])->withInput();
                }

                if ($id != 0) {

                    DB::table('mst_department_type')
                        ->where('id', $id)
                        ->update([

                            'department_name' => $departmentName,
                            'department_code' => $departmentCode,
                            'description'     => $description,
                            'updated_by'      => auth()->id(),
                            'updated_at'      => now()

                        ]);

                    // Audit Log
                    if (function_exists('auditLog')) {
                        auditLog(
                            'Department',
                            'Update',
                            $id,
                            [
                                'department_name' => $departmentName,
                                'department_code' => $departmentCode
                            ]
                        );
                    }
                } else {

                    $departmentId = DB::table('mst_department_type')->insertGetId([

                        'department_name' => $departmentName,
                        'department_code' => $departmentCode,
                        'description'     => $description,
                        'active_status'   => 1,
                        'created_by'      => auth()->id(),
                        'created_at'      => now()

                    ]);

                    // Audit Log
                    if (function_exists('auditLog')) {
                        auditLog(
                            'Department',
                            'Create',
                            $departmentId,
                            [
                                'department_name' => $departmentName,
                                'department_code' => $departmentCode
                            ]
                        );
                    }
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Department ' . ($id ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'DepartmentController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addDepartmentType', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
