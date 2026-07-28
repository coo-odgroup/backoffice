<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\OrganigationDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;

class DepartmentController extends Controller
{
    public function department()
    {
        return view('master.department');
    }

    public function dataTableView()
    {
        $recordsTotal    = 0;
        $recordsFiltered = 0;
        $data            = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $orgSearch = (request('orgSearch') !== null && request('orgSearch') !== '') ? (int)request('orgSearch') : '';

            $dataQuery = DB::table('mst_department as od')
                ->leftJoin('mst_department_type as d', 'd.id', '=', 'od.department_id')
                ->leftJoin('mst_department_type as pd', 'pd.id', '=', 'od.parent_department_id')
                ->leftJoin('mst_branches as b', 'b.id', '=', 'od.branch_id')
                ->leftJoin('mst_organization as o', 'o.id', '=', 'od.organization_id')
                ->leftJoin('users as u1', 'u1.id', '=', 'od.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'od.updated_by')
                ->select(
                    'od.id',
                    'od.active_status',
                    'od.created_at',
                    'od.updated_at',
                    'd.department_name',
                    DB::raw("IFNULL(pd.department_name,'--') as parent_department_name"),
                    'b.branch_name',
                    'o.organization_name',
                    'u1.name as created_by_name',
                    'u2.name as updated_by_name'
                );

            // Search
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('d.department_name', 'like', "%{$txtSearch}%")
                        ->orWhere('b.branch_name', 'like', "%{$txtSearch}%")
                        ->orWhere('o.organization_name', 'like', "%{$txtSearch}%");
                });
            }

            // Organization Filter
            if ($orgSearch != '') {
                $dataQuery->where('od.organization_id', $orgSearch);
            }

            // Status Filter
            if ($selStatus != '') {
                $dataQuery->where('od.active_status', $selStatus);
            }


            $count = $dataQuery->count('od.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Sorting
            if (!empty(request('order'))) {

                $columns = [

                    2 => 'd.department_name',
                    3 => 'pd.department_name',
                    4 => 'b.branch_name',
                    5 => 'o.organization_name',
                    6 => 'od.updated_at',
                    7 => 'od.active_status'

                ];

                $orderBy = request('order');

                $orderColumn = $columns[$orderBy[0]['column']] ?? 'bt.branch_type_name';
                $orderType   = $orderBy[0]['dir'];
            } else {

                $orderColumn = 'bt.display_order';
                $orderType   = 'asc';
            }

            $dataQuery->orderBy($orderColumn, $orderType);

            if ($length == -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery->offset($start)
                    ->limit($length)
                    ->get();
            }

            foreach ($arrRes as $row) {

                $row->created_date = date('d-M-Y H:i:s', strtotime($row->created_at));
                $row->updated_date = $row->updated_at ? date('d-M-Y H:i:s', strtotime($row->updated_at)) : null;
                $row->is_active = $row->active_status == 1 ? 'Active' : 'Inactive';
                $row->enc_id = Crypt::encryptString($row->id);
            }

            $recordsTotal    = $count;
            $recordsFiltered = $count;
            $data            = $arrRes;
        } catch (\Throwable $t) {

            Log::error('BranchTypeController@DataTableView', [
                'error' => $t->getMessage()
            ]);

            $recordsTotal = 0;
            $recordsFiltered = 0;
            $data = [];
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
        $data['strPage']   = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset']  = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = "admin/department/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = DB::table('mst_department')
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect()->route('department.index');
                }

                $data['row'] = $dataResQry;
            } else {

                $redirectPage = "admin/department";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [

                    'orgType'    => 'required',
                    'org'        => 'required',
                    'branch'     => 'nullable',
                    'dept'       => 'required',
                    'parentDept' => 'nullable'

                ], [

                    'orgType.required' => 'Organization Type is required.',
                    'org.required'     => 'Organization is required.',
                    'dept.required'    => 'Department is required.',

                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $organizationTypeId = request('orgType');
                $organizationId     = request('org');
                $branchId       = request('branch');
                $departmentId   = request('dept');
                $parentDepartmentId = request('parentDept') ?: 0;

                // Duplicate check
                $duplicate = DB::table('mst_department')
                    ->where('organization_type_id', $organizationTypeId)
                    ->where('organization_id', $organizationId)
                    ->where('branch_id', $branchId)
                    ->where('department_id', $departmentId)
                    ->where('parent_department_id', $parentDepartmentId);

                if ($id > 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {

                    return back()
                        ->with([
                            'level' => 'danger',
                            'message' => 'Organization Department already exists.'
                        ])
                        ->withInput();
                }

                if ($id > 0) {

                    $oldData = DB::table('mst_department')
                        ->where('id', $id)
                        ->first();

                    $newData = [
                        'organization_type_id'    => $organizationTypeId,
                        'organization_id'         => $organizationId,
                        'branch_id'            => $branchId,
                        'department_id'        => $departmentId,
                        'parent_department_id' => $parentDepartmentId,
                        'department_head_user_id' => null,
                    ];

                    $oldChanged = [];
                    $newChanged = [];

                    foreach ($newData as $key => $value) {

                        $oldValue = $oldData->$key ?? null;

                        if ((string)$oldValue !== (string)$value) {
                            $oldChanged[$key] = $oldValue;
                            $newChanged[$key] = $value;
                        }
                    }

                    if (!empty($newChanged)) {

                        app(CommonController::class)->auditLog(
                            'mst_department',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    DB::table('mst_department')
                        ->where('id', $id)
                        ->update([

                            'organization_type_id'    => $organizationTypeId,
                            'organization_id'         => $organizationId,
                            'branch_id'               => $branchId,
                            'department_id'           => $departmentId,
                            'parent_department_id'    => $parentDepartmentId,
                            'department_head_user_id' => null,
                            'updated_by'              => auth()->id(),
                            'updated_at'              => now()

                        ]);
                } else {

                    $row = [

                        'organization_type_id'    => $organizationTypeId,
                        'organization_id'         => $organizationId,
                        'branch_id'               => $branchId,
                        'department_id'           => $departmentId,
                        'parent_department_id'    => $parentDepartmentId,
                        'department_head_user_id' => null,
                        'active_status'           => 1,
                        'created_by'              => auth()->id(),
                        'created_at'              => now()

                    ];

                    app(CommonController::class)->auditLog(
                        'mst_department',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    DB::table('mst_department')->insert($row);
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Organization Department ' . ($id ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error('departmentTypeController', [

                'Method' => $method,
                'Error'  => $t->getMessage()

            ]);

            return back()
                ->with([
                    'level' => 'danger',
                    'message' => config('constants.SERVER_ERROR_MESSAGE')
                ])
                ->withInput();
        }

        return view('Master.addDepartment', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
