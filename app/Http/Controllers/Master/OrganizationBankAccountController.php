<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;

class OrganizationBankAccountController extends Controller
{
    public function organizationBankAccount()
    {
        return  $this->add();
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = trim(request('txtSearch'));
            $selStatus = request('selStatus');
            $selOrgType = request('selOrgType');

            $query = DB::table('mst_organization as o')
                ->leftJoin('mst_organization as p', 'p.id', '=', 'o.parent_id')
                ->leftJoin('mst_organization_types as ot', 'ot.id', '=', 'o.organization_type')
                ->leftJoin('users as u1', 'u1.id', '=', 'o.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'o.updated_by')
                ->select(
                    'o.id',
                    'o.organization_name',
                    'o.organization_type',
                    'o.unique_id',
                    'ot.type_name as org_type',
                    'o.parent_id',
                    'p.organization_name as parent_name',
                    'o.website_url',
                    'o.logo',
                    'o.active_status',
                    'o.created_at',
                    'o.updated_at',
                    'u1.name as created_by_name',
                    'u2.name as updated_by_name'
                );

            if (!empty($txtSearch)) {
                $query->where(function ($q) use ($txtSearch) {
                    $q->where('o.organization_name', 'like', "%{$txtSearch}%")
                        ->orWhere('o.organization_type', 'like', "%{$txtSearch}%")
                        ->orWhere('o.unique_id', 'like', "%{$txtSearch}%")
                        ->orWhere('o.organization_code', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== '' && $selStatus !== null) {
                $query->where('o.active_status', $selStatus);
            }
            
            if (!empty($selOrgType)) {
                $query->where('o.organization_type', $selOrgType);
            }

            $recordsTotal = $query->count();

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $columns = [
                2 => 'o.organization_name',
                3 => 'o.organization_type',
                4 => 'o.unique_id',
                5 => 'p.organization_name',
                6 => 'o.website_url',
                7 => 'o.updated_at',
                8 => 'o.active_status'
            ];

            if (request()->has('order')) {
                $order = request('order')[0];
                $orderColumn = $columns[$order['column']] ?? 'o.organization_name';
                $orderDir = $order['dir'];
            } else {
                $orderColumn = 'o.organization_name';
                $orderDir = 'asc';
            }

            $query->orderBy($orderColumn, $orderDir);

            if ($length != -1) {
                $query->offset($start)->limit($length);
            }

            $data = $query->get();

            foreach ($data as $row) {

                $row->org_name = $row->organization_name;
                $row->org_type = $row->org_type  ?? '--';
                $row->parent = $row->parent_name ?? '--';

                $row->created_date = $row->created_at
                    ? date('d-M-Y H:i:s', strtotime($row->created_at))
                    : '--';

                $row->updated_date = $row->updated_at
                    ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                    : '--';

                $row->is_active = $row->active_status ? 'Active' : 'Inactive';

                $row->enc_id = Crypt::encryptString($row->id);
            }

            $recordsFiltered = $recordsTotal;
        } catch (\Throwable $e) {

            Log::error('OrganizationController@DataTableView', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            $recordsTotal = 0;
            $recordsFiltered = 0;
            $data = [];
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data
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

            // Generate 6 character Alpha Numeric Unique ID
            if ($id == 0) {

                do {
                    $uniqueId = random_int(100000, 999999);
                } while (
                    DB::table('mst_organization')
                    ->where('unique_id', $uniqueId)
                    ->exists()
                );

                $data['uniqueId'] = $uniqueId;
            }

            if ($id > 0) {

                $redirectPage = "admin/organization-bank-account/edit/" . $encId;

                $data['strPage']   = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = DB::table('mst_organization')
                    ->select(
                        'id',
                        'unique_id',
                        'organization_name',
                        'organization_code',
                        'organization_type',
                        'parent_id',
                        'logo',
                        'website_url'
                    )
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect()->route('organization-bank-account.index');
                }

                $data['row'] = $dataResQry;
            } else {

                $redirectPage = "admin/organization-bank-account";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [

                    'org'          => 'required',
                    'orgName'      => 'required|max:150',
                    'orgCode'      => 'required|max:50',
                    'parent_id'    => 'nullable|integer',
                    'logo'         => 'nullable|mimes:svg|max:2048',

                ], [

                    'org.required'         => 'Organization Type is required.',
                    'orgName.required'     => 'Organization Name is required.',
                    'orgCode.required'     => 'Organization Code is required.',
                    'logo.mimes'           => 'Only SVG file is allowed.'

                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $organizationType = request('org');


                $organizationName           = htmlEncode(request('orgName'));
                $organizationCode  = strtoupper(htmlEncode(request('orgCode')));
                $parentId          = request('parent_id');
                $websiteUrl        = request('website_url');

                if ($id == 0) {
                    $uniqueId = request('uniqueId');
                } else {
                    $uniqueId = $data['row']->unique_id;
                }

                // Duplicate Organization Name
                $duplicate = DB::table('mst_organization')
                    ->where('organization_name', $organizationName);

                if ($id != 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {

                    DB::rollBack();

                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Organization Name already exists.'
                    ])->withInput();
                }

                // Duplicate Organization Code
                $duplicateCode = DB::table('mst_organization')
                    ->where('organization_code', $organizationCode);

                if ($id != 0) {
                    $duplicateCode->where('id', '!=', $id);
                }

                if ($duplicateCode->exists()) {

                    DB::rollBack();

                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Organization Code already exists.'
                    ])->withInput();
                }

                // Upload Logo
                $logoName = null;

                if (request()->hasFile('logo')) {

                    $file = request()->file('logo');

                    $logoName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

                    $file->move(public_path('uploads/organization-bank-account'), $logoName);
                } elseif ($id > 0) {

                    $logoName = $data['row']->logo;
                }

                if ($id > 0) {

                    $oldData = DB::table('mst_organization')
                        ->where('id', $id)
                        ->first();

                    $newData = [

                        'organization_name' => $organizationName,
                        'organization_type' => $organizationType,
                        'organization_code' => $organizationCode,
                        'parent_id' => $parentId,
                        'website_url' => $websiteUrl,
                        'logo' => $logoName

                    ];

                    $oldChanged = [];
                    $newChanged = [];

                    foreach ($newData as $key => $value) {

                        if (($oldData->$key ?? '') != $value) {
                            $oldChanged[$key] = $oldData->$key ?? '';
                            $newChanged[$key] = $value;
                        }
                    }

                    if (!empty($newChanged)) {

                        app(CommonController::class)->auditLog(
                            'mst_organization',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    DB::table('mst_organization')
                        ->where('id', $id)
                        ->update([

                            'organization_name' => $organizationName,
                            'organization_type' => $organizationType,
                            'organization_code' => $organizationCode,
                            'parent_id'         => $parentId,
                            'website_url'       => $websiteUrl,
                            'logo'              => $logoName,
                            'updated_by'        => auth()->id(),
                            'updated_at'        => now()

                        ]);
                } else {

                    $row = [

                        'unique_id'         => $uniqueId,
                        'organization_name' => $organizationName,
                        'organization_type' => $organizationType,
                        'organization_code' => $organizationCode,
                        'parent_id'         => $parentId,
                        'website_url'       => $websiteUrl,
                        'logo'              => $logoName,
                        'active_status'     => 1,
                        'created_by'        => auth()->id(),
                        'created_at'        => now()

                    ];
                    $insertId = DB::table('mst_organization')->insertGetId($row);

                    app(CommonController::class)->auditLog(
                        'mst_organization',
                        $insertId,
                        'INSERT',
                        [],
                        $row
                    );
                }

                DB::commit();

                return redirect($redirectPage)->with([
                    'level' => 'success',
                    'message' => 'Organization ' . ($id ? 'updated' : 'created') . ' successfully.'
                ]);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Organization Error", [
                'Controller' => 'OrganizationController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.organizationBankAcccount', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
