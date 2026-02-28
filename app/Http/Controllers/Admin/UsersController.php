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
use Mews\Purifier\Facades\Purifier;

class UsersController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $user_role = (request('user_role') !== null && request('user_role') !== '') ? (int)request('user_role') : '';

            $dataQuery = DB::table('users as u')
                ->select(
                    'u.id as users_id',
                    'u.unique_id',
                    'u.name as user_name',
                    'u.name as created_by_name',
                    'u.name as updated_by_name',
                    'u.organization_name',
                    'u.primary_email',
                    'u.primary_contact',
                    'u.location',
                    'u.created_at',
                    'u.created_by',
                    'u.updated_at',
                    'u.updated_by',
                    'u.active_status',
                    DB::raw('(SELECT name FROM mst_roles WHERE id = u.user_role LIMIT 1) as user_role')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('u.name', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($user_role) && $user_role != '') {
                $dataQuery->where('u.user_role', $user_role);
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('u.active_status', $selStatus);
            }

            $count = $dataQuery->count('u.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'u.name', 3 => 'u.organization_name', 4 => 'u.created_at', 5 => 'u.created_by', 6 => 'u.active_status'];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'u.name';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'u.name';
                $orderType = 'asc';
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
                    $val->created_date = date('d-M-Y H:i:s', strtotime($val->created_at));
                    $val->updated_date = ($val->updated_at != null) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : null;
                    $val->is_active = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_users_id = Crypt::encryptString($val->users_id);
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in UsersController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'UsersController',
                'Method'     => 'dataTableView',
                'Error'      => $errorMsg
            ]);

            $recordsTotal = 0;
            $recordsFiltered = 0;
            $data = [];
        }

        return response()->json([
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
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

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'user_role' => 'bail|required|exists:mst_roles,id',

                    'name' => 'bail|required|max:150',

                    'organization_name' => 'bail|required|max:150',

                    'primary_email' => 'bail|required|email|max:150|unique:users,primary_email',

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
                    $unique_id = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4)) . rand(1000, 9999);
                    $name = htmlEncode(Purifier::clean(request('name')));
                    $organization_name = htmlEncode(Purifier::clean(request('organization_name')));
                    $primary_email = htmlEncode(Purifier::clean(request('primary_email')));
                    $primary_contact = htmlEncode(Purifier::clean(request('primary_contact')));
                    $location = htmlEncode(Purifier::clean(request('location')));

                    $duplicate = Users::select('id')->where(['name' => $name]);

                    if ($duplicate->exists()) {
                        return back()->with([
                            'level' => 'danger',
                            'message' => 'User already exist'
                        ])->withInput();
                    } else {

                        $obj = new Users();
                        $obj->user_role = $user_role;
                        $obj->unique_id = $unique_id;
                        $obj->name = $name;
                        $obj->organization_name = $organization_name;
                        $obj->primary_email = $primary_email;
                        $obj->primary_contact = $primary_contact;
                        $obj->location = $location;
                        $obj->created_by = 1;
                        $obj->active_status = 1;

                        $obj->save();

                        $users_id = $obj->id;

                        $this->saveUsersInfo($users_id);
                        $this->saveAddress($users_id);
                        $this->saveBankDetails($users_id);

                        session()->flash('level', 'success');
                        session()->flash('message', 'Users created successfully.');
                    }

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
