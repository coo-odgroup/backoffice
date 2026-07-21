<?php

namespace App\Http\Controllers\Master;

use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\RolesHierarchy;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\DB;

class RolesHierarchyController extends Controller
{

    public function RolesHierarchy()
    {
        return view('master.rolesHierarchy');
    }

    public function add($encId = null)
    {
        $data = [];
        $data['strPage']   = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset']  = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString(urldecode($encId)) : 0;

            if ($id > 0) {

                $redirectPage = "admin/roles-hierarchy/edit/" . $encId;
                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = RolesHierarchy::select(
                    'id',
                    'organization_type_id',
                    'role_id',
                    'hierarchy_level',
                    'parent_role_id',
                    'can_create_users',
                    'can_manage_lower_roles'
                )->where('id', $id)->first();

                if (!$row) {
                    return redirect('roles-hierarchy');
                }

                $data['row'] = $row;
            } else {
                $id = 0;
                $redirectPage = "admin/roles-hierarchy";
            }

            if (request()->isMethod('post')) {
                request()->replace(request()->all());
                $validator = Validator::make(request()->all(), [

                    'org'             => 'required|integer',
                    'role'            => 'required|integer',
                    'hierarchylevel'  => 'required|integer|min:1',
                    'parent'          => 'nullable|integer|min:0',

                ], [

                    'org.required'            => 'Please select Organization.',
                    'role.required'           => 'Please select Role.',
                    'hierarchylevel.required' => 'Hierarchy Level cannot be left blank.',
                    'hierarchylevel.integer'  => 'Hierarchy Level must be an integer.'

                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $organizationType = (int) request('org');
                $roleId           = (int) request('role');
                $parentRoleId = request('parent') == 0 ? null : (int) request('parent');
                $hierarchyLevel = (int) request('hierarchylevel');
                $canCreateUsers = request()->has('can_create_users') ? 1 : 0;
                $canManageLowerRoles = request()->has('can_manage_lower_roles') ? 1 : 0;
                $duplicate = DB::table('mst_role_hierarchy')
                    ->where('organization_type_id', $organizationType)
                    ->where('role_id', $roleId);

                $duplicate = DB::table('mst_role_hierarchy')
                    ->where('organization_type_id', $organizationType)
                    ->where('role_id', $roleId);

                if ($id > 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    DB::rollBack();
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Role hierarchy already exists.'
                    ])->withInput();
                }

                if ($id > 0) {

                    $oldData = RolesHierarchy::find($id);

                    $newData = [

                        'organization_type_id'   => $organizationType,
                        'role_id'                => $roleId,
                        'parent_role_id'         => $parentRoleId,
                        'hierarchy_level'        => $hierarchyLevel,
                        'can_create_users'       => $canCreateUsers,
                        'can_manage_lower_roles' => $canManageLowerRoles,
                        'active_status'          => 1

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
                            'mst_role_hierarchy',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }
                    $oldData->organization_type_id   = $organizationType;
                    $oldData->role_id                = $roleId;
                    $oldData->parent_role_id         = $parentRoleId;
                    $oldData->hierarchy_level        = $hierarchyLevel;
                    $oldData->can_create_users       = $canCreateUsers;
                    $oldData->can_manage_lower_roles = $canManageLowerRoles;
                    $oldData->active_status          = 1;
                    $oldData->updated_by             = 1;

                    $oldData->save();
                } else {

                    $row = [

                        'organization_type_id'   => $organizationType,
                        'role_id'                => $roleId,
                        'parent_role_id'         => $parentRoleId,
                        'hierarchy_level'        => $hierarchyLevel,
                        'can_create_users'       => $canCreateUsers,
                        'can_manage_lower_roles' => $canManageLowerRoles,
                        'active_status'          => 1,
                        'created_by'             => 1,
                        'created_at'             => now()

                    ];

                    app(CommonController::class)->auditLog(
                        'mst_role_hierarchy',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    $obj = new RolesHierarchy();
                    $obj->fill($row);
                    $obj->save();
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Role ' . ($id > 0 ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error in RolesController@add", [
                'method' => $method,
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addRolesHierarchy', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function dataTableView()
    {
        $recordsTotal    = 0;
        $recordsFiltered = 0;
        $data            = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int) request('selStatus') : '';
            $selOrg = (request('org') !== null && request('org') !== '') ? (int) request('org') : '';
            $dataQuery = DB::table('mst_role_hierarchy as rh')
                ->leftJoin('mst_roles as r', 'r.id', '=', 'rh.role_id')
                ->leftJoin('mst_roles as pr', 'pr.id', '=', 'rh.parent_role_id')
                ->leftJoin('mst_organization_types as ot', 'ot.id', '=', 'rh.organization_type_id')
                ->select(
                    'rh.id',
                    'rh.organization_type_id',
                    'rh.role_id',
                    'rh.parent_role_id',
                    'rh.hierarchy_level',
                    'rh.can_create_users',
                    'rh.can_manage_lower_roles',
                    'rh.active_status',
                    'rh.created_at',
                    'rh.updated_at',
                    'rh.created_by',
                    'rh.updated_by',
                    'r.role_name',
                    DB::raw("COALESCE(pr.role_name,'None') as parent_name"),
                    'ot.type_name as org_name',
                    DB::raw("CASE WHEN rh.can_create_users=1 THEN 'Yes' ELSE 'No' END as create_user"),
                    DB::raw("CASE WHEN rh.can_manage_lower_roles=1 THEN 'Yes' ELSE 'No' END as manage_level"),
                    DB::raw("(SELECT name FROM users WHERE id=rh.created_by LIMIT 1) created_by_name"),
                    DB::raw("(SELECT name FROM users WHERE id=rh.updated_by LIMIT 1) updated_by_name")

                );


            if (!empty($txtSearch)) {

                $dataQuery->where(function ($q) use ($txtSearch) {

                    $q->where('r.role_name', 'like', "%{$txtSearch}%")
                        ->orWhere('pr.role_name', 'like', "%{$txtSearch}%")
                        ->orWhere('ot.type_name', 'like', "%{$txtSearch}%")
                        ->orWhere('rh.hierarchy_level', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== '' && $selStatus !== null) {
                $dataQuery->where('rh.active_status', (int)$selStatus);
            }

            if ($selOrg !== '' && $selOrg !== null) {
                $dataQuery->where('rh.organization_type_id', $selOrg);
            }

            $recordsTotal = $dataQuery->count('rh.id');
            $recordsFiltered = $recordsTotal;
            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            if (!empty(request('order'))) {

                $columns = [
                    2 => 'r.role_name',
                    3 => 'pr.role_name',
                    4 => 'ot.type_name',
                    5 => 'rh.hierarchy_level',
                    6 => 'rh.can_create_users',
                    7 => 'rh.can_manage_lower_roles',
                    8 => 'rh.updated_at',
                    9 => 'rh.active_status'
                ];

                $order      = request('order');
                $orderCol   = $columns[$order[0]['column']] ?? 'r.role_name';
                $orderDir   = $order[0]['dir'] ?? 'asc';
            } else {
                $orderCol = 'rh.id';
                $orderDir = 'desc';
            }

            $dataQuery->orderBy($orderCol, $orderDir);


            if ($length === -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery
                    ->offset($start)
                    ->limit($length)
                    ->get();
            }


            foreach ($arrRes as $row) {
                $row->created_date = date('d-M-Y H:i:s', strtotime($row->created_at));
                $row->updated_date = $row->updated_at
                    ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                    : null;

                $row->is_active = ($row->active_status == 1) ? 'Active' : 'Inactive';
                $row->enc_role_id = urlencode(Crypt::encryptString($row->id));
            }

            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error("Exception in RolesController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace'         => $t->getTraceAsString()
            ]);

            $recordsTotal    = 0;
            $recordsFiltered = 0;
            $data             = [];
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function getRoleByOrganization($organizationTypeId)
    {
        try {

            $roles = DB::table('mst_roles')
                ->select(
                    'id',
                    'role_name'
                )
                ->where('organization_type_id', $organizationTypeId)
                ->where('active_status', 1)
                ->orderBy('role_name')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $roles
            ]);
        } catch (\Throwable $e) {

            Log::error($e);

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }
    }
}
