<?php

namespace App\Http\Controllers\Master;

use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Roles;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{

    public function Roles()
    {
        return view('master.roles');
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

                $redirectPage = "admin/roles/edit/" . $encId;
                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = Roles::select(
                    'id',
                    'organization_type_id',
                    'organization_id',
                    'role_name',
                    'role_code',
                    'description',
                    'is_system_role'
                )->where('id', $id)->first();

                if (!$row) {
                    return redirect('roles');
                }

                $data['row'] = $row;
            } else {
                $id = 0;
                $redirectPage = "admin/roles";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'orgType'     => 'bail|required|integer',
                    'org'         => 'bail|required|integer',
                    'roleType'    => 'bail|required|max:100',
                    'roleCode'    => [
                        'bail',
                        'required',
                        'max:100',
                        'regex:/^[A-Z]+(_[A-Z]+)*$/'
                    ],
                    'Type'        => 'bail|required|in:1,2',
                    'description' => 'nullable|max:256'
                ], [
                    'roleType.required' => 'Role Type cannot be left blank.',
                    'roleCode.required' => 'Role Code cannot be left blank.',
                    'roleCode.regex'    => 'Role Code must be CAPITAL letters separated by underscore (_).',
                    'Type.required'     => 'Please select System Role type.',
                    'org.required' => 'Please select Organization Type.',
                    'orgType.required' => 'Please select Organization Type.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $roleType    = htmlEncode(trim(Purifier::clean(request('roleType'))));
                $roleCode    = htmlEncode(strtoupper(trim(Purifier::clean(request('roleCode')))));
                $description = htmlEncode(trim(Purifier::clean(request('description'))));
                $organizationType = (int) request('orgType');
                $organizationId   = (int) request('org');
                $roleFlag    = (int) Purifier::clean(request('Type'));

                $duplicate = Roles::where('role_code', $roleCode);

                if ($id > 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    DB::rollBack();
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Role Code already exists.'
                    ])->withInput();
                }

                if ($id > 0) {

                    $oldData = Roles::find($id);

                    $newData = [
                        'organization_type_id' => $organizationType,
                        'organization_id'      => $organizationId,
                        'role_name'           => $roleType,
                        'role_code'           => $roleCode,
                        'description'    => $description,
                        'is_system_role' => $roleFlag,
                        'active_status'  => 1
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
                            'mst_roles',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    $oldData->organization_type_id = $organizationType;
                    $oldData->organization_id      = $organizationId;
                    $oldData->role_name            = $roleType;
                    $oldData->role_code            = $roleCode;
                    $oldData->description     = $description;
                    $oldData->is_system_role  = $roleFlag;
                    $oldData->active_status   = 1;
                    $oldData->updated_by      = 1;
                    $oldData->save();
                } else {

                    $row = [
                        'organization_type_id' => $organizationType,
                        'organization_id'      => $organizationId,
                        'role_name'           => $roleType,
                        'role_code'           => $roleCode,
                        'description'    => $description,
                        'is_system_role' => $roleFlag,
                        'active_status'  => 1,
                        'created_by'     => 1,
                        'created_at'     => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_roles',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    $obj = new Roles();
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

        return view('Master.addRoles', compact('data'));
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
            $selSystemRole = (request('selSystemRole') !== null && request('selSystemRole') !== '') ? (int) request('selSystemRole') : '';
            $selOrg = (request('org') !== null && request('org') !== '') ? (int) request('org') : '';

            $dataQuery = DB::table('mst_roles as r')
                ->leftJoin('mst_organization_types as ot', 'ot.id', '=', 'r.organization_type_id')
                ->select(
                    'r.organization_type_id',
                    'ot.type_name as org',
                    'r.id as role_id',
                    'r.role_name',
                    'r.description',
                    'r.role_code',
                    'r.is_system_role',
                    'r.active_status',
                    'r.created_at',
                    'r.updated_at',
                    'r.created_by',
                    'r.updated_by',
                    DB::raw('(SELECT name FROM users WHERE id = r.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = r.updated_by LIMIT 1) as updated_by_name')
                );


            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('r.role_name', 'like', "%{$txtSearch}%")
                        ->orWhere('r.role_code', 'like', "%{$txtSearch}%")
                        ->orWhere('r.description', 'like', "%{$txtSearch}%")
                        ->orWhere('ot.type_name', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== '' && $selStatus !== null) {
                $dataQuery->where('r.active_status', (int) $selStatus);
            }

            if ($selSystemRole !== '' && $selSystemRole !== null) {
                $dataQuery->where('r.is_system_role', (int) $selSystemRole);
            }

            if ($selOrg !== '' && $selOrg !== null) {
                $dataQuery->where('r.organization_type_id', $selOrg);
            }

            $recordsTotal = (clone $dataQuery)->count('r.id');
            $recordsFiltered = $recordsTotal;

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);


            if (!empty(request('order'))) {


                $columns = [
                    2 => 'r.role_code',
                    3 => 'r.role_name',
                    4 => 'r.is_system_role',
                    5 => 'ot.type_name',
                    6 => 'r.updated_at',
                    7 => 'r.active_status'
                ];

                $order      = request('order');
                $orderCol   = $columns[$order[0]['column']] ?? 'r.role_name';
                $orderDir   = $order[0]['dir'] ?? 'asc';
            } else {
                $orderCol = 'r.id';
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
                $row->enc_role_id = Crypt::encryptString($row->role_id);
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
}
