<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Roles;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
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
                    'name',
                    'code',
                    'description',
                    'is_system_role'
                )->where('id', $id)->first();

                if (!$row) {
                    return redirect('roles');
                }

                $data['row'] = $row;

            } else {
                $redirectPage = "admin/roles";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'roleType'        => 'required|max:100',
                    'roleCode'        => [
                        'required',
                        'max:100',
                        'regex:/^[A-Z]+(_[A-Z]+)*$/'
                    ],
                    'Type'            => 'required|in:0,1',
                    'description'     => 'nullable|max:256'
                ], [
                    'roleType.required' => 'Role Type cannot be left blank.',
                    'roleCode.required' => 'Role Code cannot be left blank.',
                    'roleCode.regex'    => 'Role Code must be CAPITAL letters separated by underscore (_).',
                    'Type.required'     => 'Please select System Role type.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $duplicate = Roles::where('code', request('roleCode'));

                if ($id > 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Role Code already exists.'
                    ])->withInput();
                }

                $obj = ($id > 0) ? Roles::find($id) : new Roles();

                $obj->name = htmlEncode(request('roleType'));
                $obj->code = htmlEncode(request('roleCode'));
                $obj->description = htmlEncode(request('description'));
                $obj->is_system_role = (int) request('Type');
                $obj->active_status  = 1;

                if ($id > 0) {
                    $obj->updated_by = 1;
                } else {
                    $obj->created_by = 1;
                }

                $obj->save();

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
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '')
                ? (int) request('selStatus')
                : '';

            $dataQuery = DB::table('mst_roles as r')
                ->select(
                    'r.id as role_id',
                    'r.name',
                    'r.description',
                    'r.code',
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
                    $q->where('r.name', 'like', "%{$txtSearch}%")
                    ->orWhere('r.code', 'like', "%{$txtSearch}%")
                    ->orWhere('r.description', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== '' && $selStatus !== null) {
                $dataQuery->where('r.active_status', (int) $selStatus);
            }

            
            $recordsTotal = $dataQuery->count('r.id');
            $recordsFiltered = $recordsTotal;

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            
            if (!empty(request('order'))) {

            
                $columns = [
                    2 => 'r.name',
                    3 => 'r.code',
                    4 => 'r.created_at',
                    5 => 'r.active_status'
                ];

                $order      = request('order');
                $orderCol   = $columns[$order[0]['column']] ?? 'r.name';
                $orderDir   = $order[0]['dir'] ?? 'asc';

            } else {
                $orderCol = 'r.name';
                $orderDir = 'asc';
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
