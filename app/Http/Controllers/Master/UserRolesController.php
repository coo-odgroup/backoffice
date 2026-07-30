<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\BranchType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;

class UserRolesController extends Controller
{
    public function userRoles()
    {
        return view('master.userRoles');
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
            $dataQuery = DB::table('user_roles as ur')
                ->leftJoin('users as u', 'u.id', '=', 'ur.user_id')
                ->leftJoin('mst_roles as r', 'r.id', '=', 'ur.role_id')
                ->leftJoin('mst_organization_types as ot', 'ot.id', '=', 'ur.organization_type_id')
                ->leftJoin('mst_organization as o', 'o.id', '=', 'ur.organization_id')
                ->leftJoin('mst_branches as b', 'b.id', '=', 'ur.branch_id')
                ->leftJoin('mst_department as md', 'md.id', '=', 'ur.department_id')
                ->leftJoin('mst_department_type as dt', 'dt.id', '=', 'md.department_id')
                ->leftJoin('users as cb', 'cb.id', '=', 'ur.created_by')
                ->leftJoin('users as ub', 'ub.id', '=', 'ur.updated_by')
                ->select(
                    DB::raw('MIN(ur.id) as id'),
                    'u.name as user_name',
                    DB::raw("GROUP_CONCAT(DISTINCT r.role_name ORDER BY r.role_name SEPARATOR ', ') as role_name"),
                    'ot.type_name as organization_type_name',
                    'o.organization_name',
                    'b.branch_name',
                    'dt.department_name',
                    DB::raw('MAX(ur.is_primary) as is_primary'),
                    DB::raw('MIN(ur.effective_from) as effective_from'),
                    DB::raw('MAX(ur.effective_to) as effective_to'),
                    DB::raw('MAX(ur.created_at) as created_at'),
                    DB::raw('MAX(ur.updated_at) as updated_at'),
                    DB::raw('MAX(ur.active_status) as active_status'),
                    'cb.name as created_by_name',
                    'ub.name as updated_by_name'
                )
                ->groupBy(
                    'ur.user_id',
                    'ur.organization_type_id',
                    'ur.organization_id',
                    'ur.branch_id',
                    'ur.department_id',
                    'u.name',
                    'ot.type_name',
                    'o.organization_name',
                    'b.branch_name',
                    'dt.department_name',
                    'cb.name',
                    'ub.name'
                );

            // Search
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('u.name', 'like', "%{$txtSearch}%")
                        ->orWhere('r.role_name', 'like', "%{$txtSearch}%")
                        ->orWhere('ot.type_name', 'like', "%{$txtSearch}%")
                        ->orWhere('o.organization_name', 'like', "%{$txtSearch}%")
                        ->orWhere('b.branch_name', 'like', "%{$txtSearch}%")
                        ->orWhere('dt.department_name', 'like', "%{$txtSearch}%");
                });
            }

            // Organization Filter
            if ($orgSearch !== '') {
                $dataQuery->where('ur.organization_type_id', $orgSearch);
            }

            // Status Filter
            if ($selStatus !== '') {
                $dataQuery->where('ur.active_status', $selStatus);
            }

            $count = $dataQuery->count('ur.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Sorting
            if (!empty(request('order'))) {

                $columns = [
                    2  => 'u.name',
                    3  => 'r.role_name',
                    4  => 'ot.type_name',
                    5  => 'o.organization_name',
                    6  => 'b.branch_name',
                    7  => 'dt.department_name',
                    8  => 'ur.is_primary',
                    9  => 'ur.effective_from',
                    10 => 'ur.effective_to',
                    11 => 'ur.updated_at',
                    12 => 'ur.active_status',
                ];

                $orderBy = request('order');

                $orderColumn = $columns[$orderBy[0]['column']] ?? 'u.name';
                $orderType   = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'u.name';
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

                $row->updated_date = $row->updated_at
                    ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                    : null;

                $row->is_active = $row->active_status == 1
                    ? 'Active'
                    : 'Inactive';

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

                $redirectPage = "admin/user-roles/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = DB::table('user_roles')
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect()->route('user-roles.index');
                }

                $data['selectedRoles'] = DB::table('user_roles')
                    ->where('organization_type_id', $dataResQry->organization_type_id)
                    ->where('organization_id', $dataResQry->organization_id)
                    ->where('user_id', $dataResQry->user_id)
                    ->pluck('role_id')
                    ->toArray();

                $data['row'] = $dataResQry;
            } else {

                $id = 0;
                $redirectPage = "admin/user-roles";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [

                    'org'            => 'required|integer',
                    'organization'   => 'required|integer',
                    'role_id'        => 'nullable|array|min:1',
                    'role_id.*'      => 'nullable|exists:mst_roles,id',
                    'user_id'        => 'nullable|integer',

                    'branch_id'      => 'nullable|integer',
                    'department_id'  => 'nullable|integer',
                    'assigned_by'    => 'nullable|integer',
                    'effectiveFrom'  => 'nullable|date',
                    'effectiveTo'    => 'nullable|date|after_or_equal:effectiveFrom',

                ], [

                    'org.required'          => 'Organization Type is required.',
                    'organization.required' => 'Organization is required.',
                    'role_id.required'      => 'Role is required.',
                    'user_id.required'      => 'User is required.',

                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $organizationType = request('org');
                $organizationTypeId = request('org');
                $organizationId     = request('organization');
                $userId             = request('user_id');
                $roleIds             = request('role_id');
                $branchId           = request('branch_id') ?: null;
                $departmentId       = request('department_id') ?: null;
                $assignedBy         = request('assigned_by') ?: auth()->id();
                $isPrimary          = request()->has('isPrimary') ? 1 : 0;
                $effectiveFrom      = request('effectiveFrom') ?: null;
                $effectiveTo        = request('effectiveTo') ?: null;


                if ($id != 0) {

                    $oldData = DB::table('user_roles')
                        ->where('organization_type_id', $organizationTypeId)
                        ->where('organization_id', $organizationId)
                        ->where('user_id', $userId)
                        ->orderBy('role_id')
                        ->get()
                        ->toArray();

                    // Delete old roles
                    DB::table('user_roles')
                        ->where('organization_type_id', $organizationTypeId)
                        ->where('organization_id', $organizationId)
                        ->where('user_id', $userId)
                        ->delete();

                    foreach ($roleIds as $roleId) {

                        $row = [
                            'user_id'              => $userId,
                            'role_id'              => $roleId,
                            'organization_type_id' => $organizationTypeId,
                            'organization_id'      => $organizationId,
                            'branch_id'            => $branchId,
                            'department_id'        => $departmentId,
                            'is_primary'           => $isPrimary,
                            'effective_from'       => $effectiveFrom,
                            'effective_to'         => $effectiveTo,
                            'assigned_by'          => $assignedBy,
                            'active_status'        => 1,
                            'created_by'           => auth()->id(),
                            'created_at'           => now(),
                            'updated_by'           => auth()->id(),
                            'updated_at'           => now(),
                        ];

                        DB::table('user_roles')->insert($row);
                    }

                    $newData = DB::table('user_roles')
                        ->where('organization_type_id', $organizationTypeId)
                        ->where('organization_id', $organizationId)
                        ->where('user_id', $userId)
                        ->orderBy('role_id')
                        ->get()
                        ->toArray();

                    app(CommonController::class)->auditLog(
                        'user_roles',
                        $userId,
                        'UPDATE',
                        $oldData,
                        $newData
                    );
                } else {

                    $insertRows = [];

                    foreach ($roleIds as $roleId) {

                        $row = [
                            'user_id'              => $userId,
                            'role_id'              => $roleId,
                            'organization_type_id' => $organizationTypeId,
                            'organization_id'      => $organizationId,
                            'branch_id'            => $branchId,
                            'department_id'        => $departmentId,
                            'is_primary'           => $isPrimary,
                            'effective_from'       => $effectiveFrom,
                            'effective_to'         => $effectiveTo,
                            'assigned_by'          => $assignedBy,
                            'active_status'        => 1,
                            'created_by'           => auth()->id(),
                            'created_at'           => now(),
                            'updated_by'           => auth()->id(),
                            'updated_at'           => now(),
                        ];

                        DB::table('user_roles')->insert($row);
                        

                        $insertRows[] = $row;
                    }

                    app(CommonController::class)->auditLog(
                        'user_roles',
                        $userId,
                        'INSERT',
                        [],
                        $insertRows
                    );
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'User Role ' . ($id ? 'updated' : 'assigned') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'UserRolesController',
                'Method' => $method,
                'Error' => $t->getMessage()
            ]);

            return back()->with([
                'level' => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addUserRoles', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function getOrganizationDetails(Request $request)
    {
        $organizationTypeId = $request->organization_type_id;
        $organizationId     = $request->organization_id;

        $branches = DB::table('mst_branches')
            ->select('id', 'branch_name')
            ->where('organization_type_id', $organizationTypeId)
            ->where('organization_id', $organizationId)
            ->where('active_status', 1)
            ->orderBy('branch_name')
            ->get();

        $departments = DB::table('mst_department as od')
            ->join('mst_department_type as d', 'd.id', '=', 'od.department_id')
            ->select('od.id', 'd.department_name')
            ->where('od.organization_type_id', $organizationTypeId)
            ->where('od.organization_id', $organizationId)
            ->where('od.active_status', 1)
            ->distinct()
            ->orderBy('d.department_name')
            ->get();

        $roles = DB::table('mst_roles')
            ->select('id', 'role_name')
            ->where('organization_type_id', $organizationTypeId)
            ->where('organization_id', $organizationId)
            ->where('active_status', 1)
            ->orderBy('role_name')
            ->get();

        $users = DB::table('users')
            ->select('id', 'name')
            ->where('organization_type_id', $organizationTypeId)
            ->where('organization_id', $organizationId)
            ->where('active_status', 1)
            ->orderBy('name')
            ->get();

        return response()->json([
            'branches'    => $branches,
            'departments' => $departments,
            'roles'       => $roles,
            'users'       => $users,
        ]);
    }
}
