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

class OrganizationTypeController extends Controller
{
    public function organizationType()
    {
        return view('master.organizationType');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = trim(request('txtSearch'));
            $selStatus = request('selStatus');

            $query = DB::table('mst_organization_types as ot')
                ->leftJoin('mst_annexture_type as at', function ($join) {
                    $join->on('at.annexture_type', '=', DB::raw("'ORGANIZATION_TYPE_CODE'"));
                })
                ->leftJoin('mst_annexture as a', function ($join) {
                    $join->on('a.annexture_type_id', '=', 'at.id')
                        ->on('a.annexture_value', '=', 'ot.type_code')
                        ->where('a.active_status', 1);
                })
                ->leftJoin('users as u1', 'u1.id', '=', 'ot.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'ot.updated_by')
                ->select(
                    'ot.id',
                    'ot.type_code',
                    'a.annexture_name as org_type',   // <-- Display this in FE
                    'ot.type_name as org_name',
                    'ot.type_name',
                    'ot.is_branches as branches',
                    'ot.is_employees as employees',
                    'ot.is_sell_tickets as tickets',
                    'ot.active_status',
                    'ot.created_at',
                    'ot.updated_at',
                    'u1.name as created_by_name',
                    'u2.name as updated_by_name'
                );


            if (!empty($txtSearch)) {
                $query->where(function ($q) use ($txtSearch) {
                    $q->where('ot.type_name', 'like', "%{$txtSearch}%")
                        ->orWhere('ot.type_name', 'like', "%{$txtSearch}%")
                        ->orWhere('a.annexture_name', 'like', "%{$txtSearch}%");
                });
            }

            // Status
            if ($selStatus !== '' && $selStatus !== null) {
                $query->where('ot.active_status', $selStatus);
            }

            $recordsTotal = $query->count();

            $start = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            $columns = [
                2 => 'a.annexture_name',
                3 => 'ot.type_name',
                4 => 'ot.is_branches',
                5 => 'ot.is_employees',
                6 => 'ot.is_sell_tickets',
                7 => 'ot.updated_at',
                8 => 'ot.active_status'
            ];
            if (request()->has('order')) {
                $order = request('order')[0];
                $orderColumn = $columns[$order['column']] ?? 'ot.type_name';
                $orderDir = $order['dir'];
            } else {
                $orderColumn = 'ot.type_name';
                $orderDir = 'asc';
            }

            $query->orderBy($orderColumn, $orderDir);

            if ($length != -1) {
                $query->offset($start)->limit($length);
            }

            $data = $query->get();

            foreach ($data as $row) {

                $row->org_name = $row->type_name;   // If your table needs Organization Name column

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

            Log::error('OrganizationTypeController@DataTableView', [
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

            if ($id > 0) {

                $redirectPage = "admin/organization-type/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = DB::table('mst_organization_types')
                    ->select(
                        'id',
                        'type_code',
                        'type_name',
                        'small_desc',
                        'is_branches',
                        'is_employees',
                        'is_sell_tickets'
                    )
                    ->where('id', $id)
                    ->first();


                if (empty($dataResQry)) {
                    return redirect()->route('organization-type.index');
                }

                $data['row'] = $dataResQry;
            } else {

                $id = 0;
                $redirectPage = "admin/organization-type";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'org'         => 'required',
                    'orgName'     => 'required|max:100',
                    'description' => 'nullable|max:500'
                ], [
                    'org.required'         => 'Organization Type is required.',
                    'orgName.required'     => 'Organization Name is required.',
                    'description.max'      => 'Description cannot exceed 500 characters.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $typeCode        = request('org'); // annexture_value
                $typeName        = htmlEncode(request('orgName'));
                $smallDesc       = htmlEncode(request('description'));

                $isBranches      = request()->has('can_have_branches') ? 1 : 0;
                $isEmployees     = request()->has('can_have_employees') ? 1 : 0;
                $isSellTickets   = request()->has('can_sell_tickets') ? 1 : 0;

                $duplicate = DB::table('mst_organization_types')
                    ->where('type_name', $typeName);

                if ($id != 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {

                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Organization Type already exists.'
                    ])->withInput();
                }

                if ($id != 0) {

                    $oldData = DB::table('mst_organization_types')
                        ->where('id', $id)
                        ->first();

                    $newData = [
                        'type_code'       => $typeCode,
                        'type_name'       => $typeName,
                        'small_desc'      => $smallDesc,
                        'is_branches'     => $isBranches,
                        'is_employees'    => $isEmployees,
                        'is_sell_tickets' => $isSellTickets
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
                            'mst_organization_types',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    DB::table('mst_organization_types')
                        ->where('id', $id)
                        ->update([
                            'type_code'       => $typeCode,
                            'type_name'       => $typeName,
                            'small_desc'      => $smallDesc,
                            'is_branches'     => $isBranches,
                            'is_employees'    => $isEmployees,
                            'is_sell_tickets' => $isSellTickets,
                            'updated_by'      => auth()->id(),
                            'updated_at'      => now()
                        ]);
                } else {

                    $row = [
                        'type_code'       => $typeCode,
                        'type_name'       => $typeName,
                        'small_desc'      => $smallDesc,
                        'is_branches'     => $isBranches,
                        'is_employees'    => $isEmployees,
                        'is_sell_tickets' => $isSellTickets,
                        'active_status'   => 1,
                        'created_by'      => auth()->id(),
                        'created_at'      => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_organization_types',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    DB::table('mst_organization_types')->insert($row);
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Organization Type ' . (($id != 0) ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'OrganizationTypeController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addOrganizationType', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
