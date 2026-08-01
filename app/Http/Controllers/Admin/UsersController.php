<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Models\UsersAddress;
use App\Models\UsersBankDetails;
use App\Models\UsersInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;
use Mews\Purifier\Facades\Purifier;

class UsersController extends Controller
{
    public function users()
    {
        return view('admin.users.users');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = request('selStatus');
            $user_role = request('user_role');

            // Base Query
            $dataQuery = DB::table('users as u')
                ->leftJoin('mst_organization as o', 'o.id', '=', 'u.organization_id')
                ->leftJoin('mst_roles as r', 'r.id', '=', 'u.role_id')
                ->select(
                    'u.id as users_id',
                    'u.unique_id',
                    'u.name as user_name',
                    'o.organization_name',
                    'r.role_name as user_role',
                    'u.primary_email',
                    'u.primary_contact',
                    'u.location',
                    'u.created_at',
                    'u.updated_at',
                    'u.active_status',
                    DB::raw('(SELECT name FROM users WHERE id = u.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = u.updated_by LIMIT 1) as updated_by_name')
                );

            // Search
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('u.name', 'like', '%' . $txtSearch . '%')
                        ->orWhere('u.primary_email', 'like', '%' . $txtSearch . '%')
                         ->orWhere('u.primary_contact', 'like', '%' . $txtSearch . '%');
                });
            }
            // Role Filter
            if (!empty($user_role)) {
                $dataQuery->where('u.role_id', $user_role);
            }

            // Status Filter
            if ($selStatus !== '' && $selStatus !== null) {
                $dataQuery->where('u.active_status', $selStatus);
            }

            // Clone query for count
            $countQuery = clone $dataQuery;
            $recordsTotal = $countQuery->count();

            // Pagination
            $start  = (int) request('start', 0);
            $length = (int) request('length', 10);

            // Ordering
            $columns = [
                2 => 'r.role_name',
                3 => 'u.name',
                4 => 'o.organization_name',
                5 => 'u.created_at',
                6 => 'u.active_status'
            ];

            if (request()->has('order')) {
                $order = request('order')[0];
                $orderColumn = $columns[$order['column']] ?? 'u.name';
                $orderType = $order['dir'];
            } else {
                $orderColumn = 'u.name';
                $orderType = 'asc';
            }

            $dataQuery->orderBy($orderColumn, $orderType);

            if ($length != -1) {
                $dataQuery->skip($start)->take($length);
            }

            $arrRes = $dataQuery->get();

            foreach ($arrRes as $row) {

                $row->created_date = $row->created_at
                    ? date('d-M-Y H:i:s', strtotime($row->created_at))
                    : '--';

                $row->updated_date = $row->updated_at
                    ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                    : '--';

                $row->is_active = $row->active_status ? 'Active' : 'Inactive';

                $row->enc_users_id = Crypt::encryptString($row->users_id);
            }

            $recordsFiltered = $recordsTotal;
            $data = $arrRes;
        } catch (\Throwable $t) {

            return response()->json([
                'error' => $t->getMessage(),
                'line'  => $t->getLine(),
                'file'  => $t->getFile(),
            ]);
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
    public function add()
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        $data['edit_param'] = '';

        try {

            $redirectPage = "admin/users";

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'user_role' => 'bail|required|exists:mst_roles,id',
                    'name' => 'bail|required|max:150',
                    'organization_name' => 'bail|required|max:150',
                    'primary_email' => 'bail|required|email|max:150|unique:users,primary_email',
                    'primary_contact' => 'bail|required|numeric|digits_between:10,15',
                    'location' => 'bail|required|max:150'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $user_role = Purifier::clean(request('user_role'));
                $unique_id = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4)) . rand(1000, 9999);

                $name = htmlEncode(Purifier::clean(request('name')));
                $organization_name = htmlEncode(Purifier::clean(request('organization_name')));
                $primary_email = htmlEncode(Purifier::clean(request('primary_email')));
                $primary_contact = htmlEncode(Purifier::clean(request('primary_contact')));
                $location = htmlEncode(Purifier::clean(request('location')));

                $duplicate = Users::where('name', $name);

                if ($duplicate->exists()) {
                    return back()->with([
                        'level' => 'danger',
                        'message' => 'User already exist'
                    ])->withInput();
                }

                $row = [
                    'role_id'          => request('role_id'),
                    'organization_id'  => request('organization_id'),
                    'unique_id'        => $unique_id,
                    'name'             => $name,
                    'primary_email'    => $primary_email,
                    'primary_contact'  => $primary_contact,
                    'location'         => $location,
                    'created_by'       => 1,
                    'active_status'    => 1,
                    'created_at'       => now(),
                ];

                app(CommonController::class)->auditLog(
                    'users',
                    null,
                    'INSERT',
                    [],
                    $row
                );

                $obj = new Users();
                $obj->fill($row);
                $obj->save();

                $users_id = $obj->id;

                $this->saveUsersInfo($users_id);
                $this->saveAddress($users_id);
                $this->saveBankDetails($users_id);

                app(CommonController::class)->auditLog(
                    'users_full_creation',
                    $users_id,
                    'INSERT',
                    [],
                    ['message' => 'User with related info, address and bank details created']
                );

                DB::commit();

                session()->flash('level', 'success');
                session()->flash('message', 'Users created successfully.');

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'UsersController',
                'Method' => $method,
                'Error' => $t->getMessage()
            ]);

            return back()->with([
                'level' => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('admin.users.addUsers', compact('data'));
    }

    public function update($edit_param = null, $encId = null)
    {

        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        $data['edit_param'] = $edit_param;

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = "admin/users/edit/" . $edit_param . "/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';
                $dataResQry = Users::with(['info', 'address', 'bankdetails']);
                $dataResQry = $dataResQry->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("users");
                }
                $data['row'] = $dataResQry;
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                if ($edit_param == 'basic') {
                    $validator = Validator::make(request()->all(), [
                        'user_role' => 'bail|required|exists:mst_roles,id',

                        'name' => 'bail|required|max:150',

                        'organization_name' => 'bail|required|max:150',

                        'primary_email' => 'bail|required|email|max:150|unique:users,primary_email,' . $id,

                        'primary_contact' => 'bail|required|numeric|digits_between:10,15',

                        'location' => 'bail|required|max:150'
                    ], [
                        'user_role.required' => 'User Role cannot be left blank.',
                        'user_role.exists' => 'Selected User Role is invalid.',

                        'name.required' => 'User Name cannot be left blank.',
                        'name.max' => 'User Name cannot exceed 150 characters.',

                        'organization_name.required' => 'Organization Name cannot be left blank.',
                        'organization_name.max' => 'Organization Name cannot exceed 150 characters.',

                        'primary_email.required' => 'Primary Email cannot be left blank.',
                        'primary_email.email' => 'Enter a valid Email address.',
                        'primary_email.max' => 'Email cannot exceed 150 characters.',
                        'primary_email.unique' => 'Email already exists.',

                        'primary_contact.required' => 'Primary Contact cannot be left blank.',
                        'primary_contact.numeric' => 'Primary Contact must be numeric.',
                        'primary_contact.digits_between' => 'Primary Contact must be between 10 to 15 digits.',

                        'location.required' => 'Location cannot be left blank.',
                        'location.max' => 'Location cannot exceed 150 characters.'
                    ]);

                    if ($validator->fails()) {
                        return back()->withErrors($validator)->withInput();
                    } else {
                        DB::beginTransaction();

                        $user_role = Purifier::clean(request('user_role'));
                        $name = htmlEncode(Purifier::clean(request('name')));
                        $organization_name = htmlEncode(Purifier::clean(request('organization_name')));
                        $primary_email = htmlEncode(Purifier::clean(request('primary_email')));
                        $primary_contact = htmlEncode(Purifier::clean(request('primary_contact')));
                        $location = htmlEncode(Purifier::clean(request('location')));

                        $duplicate = Users::select('id')->where(['name' => $name]);
                        $duplicate->where('id', '!=', $id);

                        if ($duplicate->exists()) {
                            return back()->with([
                                'level' => 'danger',
                                'message' => 'User already exist'
                            ])->withInput();
                        } else {

                            $obj = Users::find($id);

                            $obj->user_role = $user_role;
                            $obj->name = $name;
                            $obj->organization_name = $organization_name;
                            $obj->primary_email = $primary_email;
                            $obj->primary_contact = $primary_contact;
                            $obj->location = $location;
                            $obj->active_status = 1;
                            $obj->updated_by = 1;

                            $obj->save();

                            session()->flash('level', 'success');
                            session()->flash('message', 'Users updated successfully.');
                        }

                        DB::commit();
                        return redirect($redirectPage);
                    }
                } elseif ($edit_param == 'moreinfo') {
                    $this->saveUsersInfo($id);
                    session()->flash('level', 'success');
                    session()->flash('message', 'Users Info updated successfully.');
                    DB::commit();
                    return redirect($redirectPage);
                } elseif ($edit_param == 'address') {
                    $this->saveAddress($id);
                    session()->flash('level', 'success');
                    session()->flash('message', 'Users Address updated successfully.');
                    DB::commit();
                    return redirect($redirectPage);
                } elseif ($edit_param == 'bankdetails') {
                    $this->saveBankDetails($id);
                    session()->flash('level', 'success');
                    session()->flash('message', 'Users Bank Details updated successfully.');
                    DB::commit();
                    return redirect($redirectPage);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'UsersController',
                'Method' => $method,
                'Error' => $t->getMessage()
            ]);

            DB::rollBack();

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            return back()->with([
                'level' => 'danger',
                'message' => $errorMsg
            ])->withInput();
        }
        return view('admin.users.addUsers', compact('data'));
    }

    private function saveUsersInfo($users_id)
    {
        $data = request()->only([
            'secondary_email',
            'secondary_contact',
            'aadhaar_no',
            'pancard_no',
            'president_name',
            'president_phone',
            'general_secretary_name',
            'general_secretary_phone',
            'has_gst',
            'gst_no'
        ]);

        foreach ($data as $key => $value) {
            $data[$key] = htmlEncode(Purifier::clean($value));
        }

        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && $value !== '';
        });

        if (!empty($filteredData)) {

            UsersInfo::updateOrCreate(
                ['users_id' => $users_id],
                $filteredData
            );
        }
    }

    private function saveAddress($users_id)
    {
        $data = request()->only([
            'address',
            'street',
            'landmark',
            'city',
            'pincode'
        ]);

        foreach ($data as $key => $value) {
            $data[$key] = htmlEncode(Purifier::clean($value));
        }

        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && $value !== '';
        });

        if (!empty($filteredData)) {

            UsersAddress::updateOrCreate(
                ['users_id' => $users_id],
                $filteredData
            );
        }
    }

    private function saveBankDetails($users_id)
    {
        $data = request()->only([
            'bank_account_name',
            'bank_name',
            'bank_ifsc',
            'bank_account_number',
            'bank_address',
            'upi_id'
        ]);

        foreach ($data as $key => $value) {
            $data[$key] = htmlEncode(Purifier::clean($value));
        }

        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && $value !== '';
        });

        if (!empty($filteredData)) {

            UsersBankDetails::updateOrCreate(
                ['users_id' => $users_id],
                $filteredData
            );
        }
    }

    public function edit($edit_param, $encId)
    {
        return $this->update($edit_param, $encId);
    }

    public function viewUserRecord(Request $request)
    {
        $id = Crypt::decryptString($request->id);

        $record = Users::with(['info', 'address', 'bankdetails'])->where('id', $id)->first();

        return response()->json($record ?? []);
    }
}
