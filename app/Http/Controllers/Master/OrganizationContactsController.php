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

class OrganizationContactsController extends Controller
{
    public function organizationContacts()
    {
        return  $this->add();
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

                $redirectPage = "admin/organization-contacts/edit/" . $encId;

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
                        'website_url',

                    )
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect()->route('organization-contacts.index');
                }

                $data['row'] = $dataResQry;

                $data['contacts'] = DB::table('mst_organization_contacts')
                    ->where('organization_id', $id)
                    ->where('active_status', 1)
                    ->orderByDesc('is_primary')
                    ->orderBy('id')
                    ->get();
            } else {

                $redirectPage = "admin/organization-contacts";
            }
            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [

                    'contact_type.*'      => 'required',
                    'fullname.*'          => 'required|max:128',
                    'designation.*'       => 'required|max:128',
                    'mobile.*'            => 'required|max:15',
                    'alternate_mobile.*'  => 'nullable|max:15',
                    'email.*'             => 'required|email|max:128',

                ], [

                    'contact_type.*.required' => 'Contact Type is required.',
                    'fullname.*.required'     => 'Full Name is required.',
                    'designation.*.required'  => 'Designation is required.',
                    'mobile.*.required'       => 'Mobile Number is required.',
                    'email.*.required'        => 'Email is required.',
                    'email.*.email'           => 'Please enter a valid Email.',

                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                try {

                    $organizationId = $id;

                    if ($id > 0) {
                        DB::table('mst_organization_contacts')
                            ->where('organization_id', $organizationId)
                            ->delete();
                    }

                    $contactTypes      = request('contact_type');
                    $fullNames         = request('fullname');
                    $designations      = request('designation');
                    $mobiles           = request('mobile');
                    $alternateMobiles  = request('alternate_mobile');
                    $emails            = request('email');

                    $primary = (int) request('primary_account', 0);

                    foreach ($fullNames as $key => $value) {

                        $row = [
                            'organization_id'   => $organizationId,
                            'contact_type'      => $contactTypes[$key] ?? null,
                            'fullname'          => htmlEncode(trim($fullNames[$key] ?? '')),
                            'designation'       => htmlEncode(trim($designations[$key] ?? '')),
                            'mobile'            => htmlEncode(trim($mobiles[$key] ?? '')),
                            'alternate_mobile'  => htmlEncode(trim($alternateMobiles[$key] ?? '')),
                            'email'             => htmlEncode(trim($emails[$key] ?? '')),
                            'is_primary'        => ($key === $primary) ? 1 : 0,
                            'active_status'     => 1,
                            'created_by'        => auth()->id(),
                            'created_at'        => now(),
                            'updated_by'        => auth()->id(),
                            'updated_at'        => now(),
                        ];

                        $insertId = DB::table('mst_organization_contacts')
                            ->insertGetId($row);

                        app(CommonController::class)->auditLog(
                            'mst_organization_contacts',
                            $insertId,
                            'INSERT',
                            [],
                            $row
                        );
                    }

                    DB::commit();

                    return redirect($redirectPage)->with([
                        'level'   => 'success',
                        'message' => 'Organization Contacts saved successfully.'
                    ]);
                } catch (\Throwable $e) {

                    DB::rollBack();

                    Log::error($e);

                    return back()->with([
                        'level'   => 'danger',
                        'message' => config('constants.SERVER_ERROR_MESSAGE')
                    ])->withInput();
                }
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

        return view('Master.organizationContacts', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
