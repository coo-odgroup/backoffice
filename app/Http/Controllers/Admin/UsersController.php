<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index() {
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
            $apiApp = (request('apiApp') !== null && request('apiApp') !== '') ? (int)request('apiApp') : '';
            $selCity = (request('selCity') !== null && request('selCity') !== '') ? (int)request('selCity') : '';

            $dataQuery = DB::table('city_api_ids as cp')
                ->select(
                    'cp.id as city_api_ids_id',
                    'cp.city_id',
                    'cp.api_app_id',
                    'cp.api_city_ids',
                    'cp.created_at',
                    'cp.created_by',
                    'cp.updated_at',
                    'cp.updated_by',
                    'cp.active_status',
                    DB::raw('(SELECT city_name FROM mst_cities WHERE id = cp.city_id LIMIT 1) as city_name'),
                    DB::raw('(SELECT app_name FROM api_apps WHERE id = cp.api_app_id LIMIT 1) as app_name'),
                    DB::raw('(SELECT name FROM users WHERE id = cp.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = cp.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('cp.api_city_ids', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($apiApp) && $apiApp != '') {
                $dataQuery->where('cp.api_app_id', $apiApp);
            }

            if (isset($selCity) && $selCity != '') {
                $dataQuery->where('cp.city_id', $selCity);
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('cp.active_status', $selStatus);
            }

            $count = $dataQuery->count('cp.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'cp.api_app_id', 3 => 'cp.api_city_ids', 4 => 'cp.created_at', 5 => 'cp.created_by', 6 => 'cp.active_status'];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'cp.api_city_ids';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'cp.api_city_ids';
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
                    $val->enc_city_api_ids_id = Crypt::encryptString($val->city_api_ids_id);
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in UserController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'UserController',
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

    public function add($encId = null)
    {

        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = "admin/user/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = Users::select('id', 'user_role', 'unique_id', 'name', 'organization_name', 'primary_email', 'primary_contact', 'location');

                $dataResQry = $dataResQry->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("user");
                }
                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/user";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'user_role' => 'bail|required|exists:mst_roles,id',
                    
                    'unique_id' => 'bail|required|max:100|unique:user,unique_id',
                    
                    'name' => 'bail|required|max:150',
                    
                    'organization_name' => 'bail|required|max:150',
                    
                    'primary_email' => 'bail|required|email|max:150|unique:user,primary_email',
                    
                    'primary_contact' => 'bail|required|numeric|digits_between:10,15',
                    
                    'location' => 'bail|required|max:150'
                ], [
                    'user_role.required' => 'User Role cannot be left blank.',
                    'user_role.exists' => 'Selected User Role is invalid.',

                    'unique_id.required' => 'Unique Id cannot be left blank.',
                    'unique_id.max' => 'Unique Id cannot exceed 100 characters.',
                    'unique_id.unique' => 'Unique Id already exists.',

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

                    $user_role = request('user_role');
                    $unique_id = htmlEncode(request('unique_id'));
                    $name = htmlEncode(request('name'));
                    $organization_name = htmlEncode(request('organization_name'));
                    $primary_email = htmlEncode(request('primary_email'));
                    $primary_contact = htmlEncode(request('primary_contact'));
                    $location = htmlEncode(request('location'));

                    $duplicate = Users::select('id')->where(['name' => $name]);

                    if ($id != 0) {
                        $duplicate->where('id', '!=', $id);
                    }

                    if ($duplicate->exists()) {
                        return back()->with([
                            'level' => 'danger',
                            'message' => 'User already exist'
                        ])->withInput();
                    } else {
                        $obj = ($id != 0) ? Users::find($id) : new Users();
                        $obj->user_role = $user_role;
                        $obj->unique_id = $unique_id;
                        $obj->name = $name;
                        $obj->organization_name = $organization_name;
                        $obj->primary_email = $primary_email;
                        $obj->primary_contact = $primary_contact;
                        $obj->location = $location;
                        $obj->created_by = 1;
                        $obj->active_status = 1;

                        if ($id != 0) {
                            $obj->updated_by = 1;
                        }

                        $obj->save();

                        session()->flash('level', 'success');
                        session()->flash('message', 'User ' . (($id != 0) ? 'updated' : 'created') . ' successfully.');
                    }

                    DB::commit();
                    return redirect($redirectPage);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'UserController',
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

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
