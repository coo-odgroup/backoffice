<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;

class BranchController extends Controller
{
    public function branch()
    {
        return view('master.branch');
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

            $dataQuery = DB::table('mst_branches as b')
                ->leftJoin('mst_organization_types as ot', 'ot.id', '=', 'b.organization_id')
                ->leftJoin('mst_branch_types as bt', 'bt.id', '=', 'b.branch_type_id')
                ->leftJoin('mst_branches as pb', 'pb.id', '=', 'b.parent_branch_id')
                ->leftJoin('users as u1', 'u1.id', '=', 'b.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'b.updated_by')
                ->select(
                    'b.id as branch_id',
                    'b.branch_name',
                    'b.branch_code',
                    'b.is_head_office',
                    'b.active_status',
                    'b.created_at',
                    'b.updated_at',
                    'ot.type_name as organization_name',
                    'bt.branch_type_name',
                    'pb.branch_name as parent_branch_name',
                    'u1.name as created_by_name',
                    'u2.name as updated_by_name'
                );

            if (!empty($txtSearch)) {

                $dataQuery->where(function ($q) use ($txtSearch) {

                    $q->where('b.branch_name', 'like', "%{$txtSearch}%")
                        ->orWhere('b.branch_code', 'like', "%{$txtSearch}%")
                        ->orWhere('ot.type_name', 'like', "%{$txtSearch}%")
                        ->orWhere('bt.branch_type_name', 'like', "%{$txtSearch}%")
                        ->orWhere('pb.branch_name', 'like', "%{$txtSearch}%");
                });
            }

            if ($orgSearch != '') {
                $dataQuery->where('b.organization_id', $orgSearch);
            }

            if ($selStatus != '') {
                $dataQuery->where('b.active_status', $selStatus);
            }

            $count = $dataQuery->count('b.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            if (!empty(request('order'))) {

                $columns = [
                    2 => 'b.branch_name',
                    3 => 'b.branch_code',
                    4 => 'b.is_head_office',
                    5 => 'ot.type_name',
                    6 => 'bt.branch_type_name',
                    7 => 'pb.branch_name',
                    8 => 'b.updated_at',
                    9 => 'b.active_status'
                ];

                $orderBy = request('order');

                $orderColumn = $columns[$orderBy[0]['column']] ?? 'b.branch_name';
                $orderType   = $orderBy[0]['dir'];
            } else {

                $orderColumn = 'b.branch_name';
                $orderType   = 'asc';
            }

            $dataQuery->orderBy($orderColumn, $orderType);

            if ($length == -1) {

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

                $row->is_active = $row->active_status ? 'Active' : 'Inactive';

                $row->head_office = $row->is_head_office ? 'Yes' : 'No';

                $row->enc_branch_id = Crypt::encryptString($row->branch_id);
            }

            $recordsTotal    = $count;
            $recordsFiltered = $count;
            $data            = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in BranchController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            Log::error("Error", [
                'Controller' => 'BranchController',
                'Method'     => 'dataTableView',
                'Error'      => $t->getMessage()
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

                $redirectPage = "admin/branch/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = DB::table('mst_branches')
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect()->route('branch.index');
                }

                $data['row'] = $dataResQry;
            } else {

                $id = 0;
                $redirectPage = "admin/branch";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [

                    'orgType'       => 'required',
                    'branchType'    => 'required',
                    'parentBranch'  => 'nullable',
                    'branchName'    => 'required|max:150',
                    'branchCode'    => 'required|max:100',
                    'email'         => 'required|email|max:100',
                    'phoneNo'       => 'required|digits:10',
                    'address1'      => 'required|max:255',
                    'address2'      => 'nullable|max:255',
                    'city'          => 'required',
                    'state'         => 'required',
                    'country'       => 'required',
                    'pinCode'       => 'required|digits:6',
                    'latitude'      => 'nullable|numeric',
                    'longitude'     => 'nullable|numeric',
                    'openingDate'   => 'nullable|date'

                ], [

                    'orgType.required'      => 'Organization Type is required.',
                    'branchType.required'   => 'Branch Type is required.',
                    'branchName.required'   => 'Branch Name cannot be blank.',
                    'branchCode.required'   => 'Branch Code cannot be blank.',
                    'email.required'        => 'Email is required.',
                    'email.email'           => 'Enter a valid Email.',
                    'phoneNo.required'      => 'Phone Number is required.',
                    'phoneNo.digits'        => 'Phone Number must be 10 digits.',
                    'address1.required'     => 'Address Line 1 is required.',
                    'city.required'         => 'City is required.',
                    'state.required'        => 'State is required.',
                    'country.required'      => 'Country is required.',
                    'pinCode.required'      => 'Pin Code is required.',
                    'pinCode.digits'        => 'Pin Code must be 6 digits.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $organizationId = request('orgType');
                $branchTypeId   = request('branchType');
                $parentBranchId = request('parentBranch');

                $branchName = htmlEncode(request('branchName'));

                $branchCode = strtoupper(
                    str_replace(' ', '_', htmlEncode(request('branchCode')))
                );

                $email       = htmlEncode(request('email'));
                $phone       = htmlEncode(request('phoneNo'));
                $address1    = htmlEncode(request('address1'));
                $address2    = htmlEncode(request('address2'));
                $cityId      = request('city');
                $stateId     = request('state');
                $countryId   = request('country');
                $pincode     = htmlEncode(request('pinCode'));
                $latitude    = request('latitude');
                $longitude   = request('longitude');
                $openingDate = request('openingDate');
                $isHeadOffice = request()->has('isHeadOffice') ? 1 : 0;

                $duplicate = DB::table('mst_branches')
                    ->where(function ($q) use ($branchName, $branchCode) {
                        $q->where('branch_name', $branchName)
                            ->orWhere('branch_code', $branchCode);
                    });

                if ($id != 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {

                    DB::rollBack();

                    return back()->with([
                        'level' => 'danger',
                        'message' => 'Branch Name or Branch Code already exists.'
                    ])->withInput();
                }
                if ($id != 0) {

                    $oldData = DB::table('mst_branches')
                        ->where('id', $id)
                        ->first();

                    $newData = [
                        'organization_id'  => $organizationId,
                        'branch_type_id'   => $branchTypeId,
                        'parent_branch_id' => $parentBranchId,
                        'branch_name'      => $branchName,
                        'branch_code'      => $branchCode,
                        'email'            => $email,
                        'phone'            => $phone,
                        'address1'         => $address1,
                        'address2'         => $address2,
                        'city_id'          => $cityId,
                        'state_id'         => $stateId,
                        'country_id'       => $countryId,
                        'pincode'          => $pincode,
                        'latitude'         => $latitude,
                        'longitude'        => $longitude,
                        'opening_date'     => $openingDate,
                        'is_head_office'   => $isHeadOffice
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
                            'mst_branches',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    DB::table('mst_branches')
                        ->where('id', $id)
                        ->update([

                            'organization_id'  => $organizationId,
                            'branch_type_id'   => $branchTypeId,
                            'parent_branch_id' => $parentBranchId,
                            'branch_name'      => $branchName,
                            'branch_code'      => $branchCode,
                            'email'            => $email,
                            'phone'            => $phone,
                            'address1'         => $address1,
                            'address2'         => $address2,
                            'city_id'          => $cityId,
                            'state_id'         => $stateId,
                            'country_id'       => $countryId,
                            'pincode'          => $pincode,
                            'latitude'         => $latitude,
                            'longitude'        => $longitude,
                            'opening_date'     => $openingDate,
                            'is_head_office'   => $isHeadOffice,

                            'updated_by'       => auth()->id(),
                            'updated_at'       => now()

                        ]);
                } else {

                    $row = [

                        'organization_id'  => $organizationId,
                        'branch_type_id'   => $branchTypeId,
                        'parent_branch_id' => $parentBranchId,
                        'branch_name'      => $branchName,
                        'branch_code'      => $branchCode,
                        'email'            => $email,
                        'phone'            => $phone,
                        'address1'         => $address1,
                        'address2'         => $address2,
                        'city_id'          => $cityId,
                        'state_id'         => $stateId,
                        'country_id'       => $countryId,
                        'pincode'          => $pincode,
                        'latitude'         => $latitude,
                        'longitude'        => $longitude,
                        'opening_date'     => $openingDate,
                        'is_head_office'   => $isHeadOffice,

                        'active_status'    => 1,
                        'created_by'       => auth()->id(),
                        'created_at'       => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_branches',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    DB::table('mst_branches')->insert($row);
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Branch ' . (($id != 0) ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'BranchController',
                'Method'     => $method,
                'Error'      => $t->getMessage(),
                'Line'       => $t->getLine(),
                'File'       => $t->getFile()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addBranch', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function view($encId)
    {
        try {

            $id = Crypt::decryptString($encId);

            $row = DB::table('mst_branches as b')
                ->leftJoin('mst_organization_types as ot', 'ot.id', '=', 'b.organization_id')
                ->leftJoin('mst_branch_types as bt', 'bt.id', '=', 'b.branch_type_id')
                ->leftJoin('mst_branches as pb', 'pb.id', '=', 'b.parent_branch_id')
                ->leftJoin('mst_states as s', 's.id', '=', 'b.state_id')
                ->leftJoin('mst_cities as c', 'c.id', '=', 'b.city_id')
                ->select(
                    'b.*',
                    'ot.type_name',
                    'bt.branch_type_name',
                    'pb.branch_name as parent_branch',
                    's.state_name',
                    'c.city_name'
                )
                ->where('b.id', $id)
                ->first();

            return response()->json($row);
        } catch (\Throwable $t) {

            Log::error("Error", [
                'Controller' => 'BranchController',
                'Method'     => 'view',
                'Error'      => $t->getMessage()
            ]);

            return response()->json([]);
        }
    }
}
