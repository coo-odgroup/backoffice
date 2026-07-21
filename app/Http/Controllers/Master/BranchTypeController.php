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

class BranchTypeController extends Controller
{
    public function branchType()
    {
        return view('master.branchType');
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

            $dataQuery = DB::table('mst_branch_types as bt')
                ->leftJoin('mst_organization_types as ot', 'ot.id', '=', 'bt.organization_type_id')
                ->leftJoin('users as u1', 'u1.id', '=', 'bt.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'bt.updated_by')
                ->select(
                    'bt.id as branch_id',
                    'bt.branch_type_name',
                    'bt.branch_type_code',
                    'bt.description',
                    'bt.display_order',
                    'bt.active_status',
                    'bt.created_at',
                    'bt.updated_at',
                    'ot.type_name as organization_name',
                    'u1.name as created_by_name',
                    'u2.name as updated_by_name'
                );

            // Search
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('bt.branch_type_name', 'like', "%{$txtSearch}%")
                        ->orWhere('bt.branch_type_code', 'like', "%{$txtSearch}%")
                        ->orWhere('ot.type_name', 'like', "%{$txtSearch}%");
                });
            }

            // Organization Filter
            if ($orgSearch !== '') {
                $dataQuery->where('bt.organization_type_id', $orgSearch);
            }

            // Status Filter
            if ($selStatus !== '') {
                $dataQuery->where('bt.active_status', $selStatus);
            }

            $count = $dataQuery->count('bt.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Sorting
            if (!empty(request('order'))) {

                $columns = [
                    2 => 'bt.branch_type_name',
                    3 => 'bt.branch_type_code',
                    4 => 'ot.type_name',
                    5 => 'bt.display_order',
                    6 => 'bt.description',
                    7 => 'bt.updated_at',
                    8 => 'bt.active_status'
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

                $row->updated_date = $row->updated_at
                    ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                    : null;

                $row->is_active = $row->active_status == 1
                    ? 'Active'
                    : 'Inactive';

                $row->enc_branch_id = Crypt::encryptString($row->branch_id);
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

                $redirectPage = "admin/branch-type/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = DB::table('mst_branch_types')
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect()->route('branch-type.index');
                }

                $data['row'] = $dataResQry;
            } else {

                $id = 0;
                $redirectPage = "admin/branch-type";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'org'      => 'required',
                    'branchName'   => 'required|max:100',
                    'branchCode'   => 'required|max:100',
                    'desc'         => 'nullable|max:500',
                    'displyOrder'  => 'required|integer|min:1'
                ], [
                    'org.required'     => 'Organization Type must be selected.',
                    'branchName.required'  => 'Branch Type Name cannot be blank.',
                    'branchCode.required'  => 'Branch Type Code cannot be blank.',
                    'displyOrder.required' => 'Display Order is required.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $organizationType = request('org');

                $branchName = htmlEncode(request('branchName'));

                $branchCode = strtoupper(
                    str_replace(' ', '_', htmlEncode(request('branchCode')))
                );

                $description = htmlEncode(request('desc'));

                $displayOrder = request('displyOrder');

                $duplicate = DB::table('mst_branch_types')
                    ->where('organization_type_id', $organizationType)
                    ->where('branch_type_name', $branchName);

                if ($id != 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {

                    return back()->with([
                        'level' => 'danger',
                        'message' => 'Branch Type already exists.'
                    ])->withInput();
                }

                if ($id != 0) {

                    $oldData = DB::table('mst_branch_types')
                        ->where('id', $id)
                        ->first();

                    $newData = [
                        'organization_type_id' => $organizationType,
                        'branch_type_name' => $branchName,
                        'branch_type_code' => $branchCode,
                        'description' => $description,
                        'display_order' => $displayOrder
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
                            'mst_branch_types',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    DB::table('mst_branch_types')
                        ->where('id', $id)
                        ->update([
                            'organization_type_id' => $organizationType,
                            'branch_type_name' => $branchName,
                            'branch_type_code' => $branchCode,
                            'description' => $description,
                            'display_order' => $displayOrder,
                            'updated_by' => auth()->id(),
                            'updated_at' => now()
                        ]);
                } else {

                    $row = [
                        'organization_type_id' => $organizationType,
                        'branch_type_name' => $branchName,
                        'branch_type_code' => $branchCode,
                        'description' => $description,
                        'display_order' => $displayOrder,
                        'active_status' => 1,
                        'created_by' => auth()->id(),
                        'created_at' => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_branch_types',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    DB::table('mst_branch_types')->insert($row);
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Branch Type ' . ($id ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'BranchTypeController',
                'Method' => $method,
                'Error' => $t->getMessage()
            ]);

            return back()->with([
                'level' => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addBranchType', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
