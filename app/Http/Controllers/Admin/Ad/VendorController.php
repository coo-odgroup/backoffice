<?php

namespace App\Http\Controllers\Admin\Ad;

use App\Http\Controllers\Controller;
use App\Models\Master\Roles;
use App\Models\Ad\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    public function index()
    {
        return view('admin.Ad.vendor');
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

                $redirectPage = "admin/Ad/vendor/edit/" . $encId;
                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = Vendor::select(
                    'id',
                    'company_name',
                    'contact_person',
                    'email',
                    'phone',
                    'gst_number',
                )->where('id', $id)->first();

                if (!$row) {
                    return redirect('vendor');
                }

                $data['row'] = $row;
            } else {
                $redirectPage = "admin/vendor";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [

                    'id'             => 'nullable',

                    'companyName'   => 'nullable|max:150',

                    'personName' => 'required|max:100',

                    'email'          => 'required|email|max:100',

                    'phone'          => [
                        'required',
                        'regex:/^[0-9]{10}$/'
                    ],

                    'gst'     => 'nullable|max:15'

                ], [

                    'personName.required' => 'Contact Person cannot be left blank.',
                    'personName.max'      => 'Contact Person cannot be more than 100 characters.',

                    'email.required'          => 'Email cannot be left blank.',
                    'email.email'             => 'Please enter a valid Email Id.',
                    'email.max'               => 'Email cannot be more than 100 characters.',

                    'phone.required'          => 'Phone Number cannot be left blank.',
                    'phone.regex'             => 'Enter a valid 10 digit mobile number.',

                    'companyName.max'        => 'Company Name cannot be more than 150 characters.',

                    'gst.max'          => 'GST Number cannot be more than 15 characters.'
                ]);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $companyName    = trim(Purifier::clean(request('companyName')));
                $personName     = trim(Purifier::clean(request('personName')));
                $email          = trim(Purifier::clean(request('email')));
                $phone          = trim(Purifier::clean(request('phone')));
                $gst            = trim(Purifier::clean(request('gst')));

                $duplicateEmail = Vendor::where('email', $email);
                $duplicatePhone = Vendor::where('phone', $phone);

                if ($id > 0) {
                    $duplicateEmail->where('id', '!=', $id);
                }

                if ($duplicateEmail->exists()) {
                    DB::rollBack();
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Email Id  already exists.'
                    ])->withInput();
                }

                if ($duplicatePhone->exists()) {
                    DB::rollBack();
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Phone Number already exists.'
                    ])->withInput();
                }

                $obj = ($id > 0) ? Vendor::find($id) : new Vendor();

                $obj->company_name            = htmlEncode($companyName);
                $obj->contact_person          = htmlEncode($personName);
                $obj->email                   = htmlEncode($email);
                $obj->phone                   = htmlEncode($phone);
                $obj->gst_number              = htmlEncode($gst);

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
                    'Vendor ' . ($id > 0 ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error in VendorController@add", [
                'method' => $method,
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Admin.Ad.addVendor', compact('data'));
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

            $txtSearch = trim(htmlEncode(request('txtSearch')));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int) request('selStatus') : '';

            $dataQuery = DB::connection('mysql_dev')->table('vendors as v')
                ->select(
                    'v.id as vendor_id',
                    'v.company_name',
                    'v.contact_person',
                    'v.email',
                    'v.phone',
                    'v.gst_number',
                    'v.active_status',
                    'v.created_at',
                    'v.updated_at',
                    'v.created_by',
                    'v.updated_by',
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = v.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = v.updated_by LIMIT 1) as updated_by_name')
                );


            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('v.company_name', 'like', "%{$txtSearch}%")
                        ->orWhere('v.contact_person', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== '' && $selStatus !== null) {
                $dataQuery->where('v.active_status', (int) $selStatus);
            }


            $recordsTotal = DB::connection('mysql_dev')->table('vendors')->count();
            $recordsFiltered = (clone $dataQuery)->count();

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);


            if (!empty(request('order'))) {


                $columns = [
                    2 => 'v.company_name',
                    3 => 'v.contact_person',
                    4 => 'v.email',
                    5 => 'v.phone',
                    6 => 'v.gst_number',
                    7 => 'v.created_at',
                    8 => 'v.active_status'
                ];

                $order      = request('order');
                $orderCol   = $columns[$order[0]['column']] ?? 'v.company_name';
                $orderDir   = $order[0]['dir'] ?? 'asc';
            } else {
                $orderCol = 'v.company_name';
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
                $row->enc_vendor_id = Crypt::encryptString($row->vendor_id);
            }

            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error("Exception in VendorController@dataTableView", [
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
