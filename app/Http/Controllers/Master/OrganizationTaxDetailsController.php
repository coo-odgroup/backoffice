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

class OrganizationTaxDetailsController extends Controller
{
    public function organizationTaxDetails()
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

                $redirectPage = "admin/organization-tax-details/edit/" . $encId;

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
                    return redirect()->route('organization-tax-details.index');
                }

                $data['row'] = $dataResQry;

                $data['taxes'] = DB::table('mst_organization_tax_details')
                    ->where('organization_id', $id)
                    ->where('active_status', 1)
                    ->orderBy('id')
                    ->get();
            } else {

                $redirectPage = "admin/organization-tax-details";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [

                    'gst_number.*'               => 'required|max:32',
                    'pan_number.*'               => 'required|max:16',
                    'tan_number.*'               => 'nullable|max:32',
                    'cin_number.*'               => 'nullable|max:32',
                    'msme_number.*'              => 'nullable|max:64',
                    'trade_license_number.*'     => 'nullable|max:64',
                    'gst_registered_name.*'      => 'nullable|max:255',
                    'gst_registered_address.*'   => 'nullable|max:1000',
                    'gst_registration_date.*'    => 'nullable|date',
                    'gst_expiry_date.*'          => 'nullable|date|after_or_equal:gst_registration_date.*',

                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                try {

                    $organizationId = $id;

                    DB::table('mst_organization_tax_details')
                        ->where('organization_id', $organizationId)
                        ->delete();

                    $gstNumbers             = request('gst_number');
                    $panNumbers             = request('pan_number');
                    $tanNumbers             = request('tan_number');
                    $cinNumbers             = request('cin_number');
                    $msmeNumbers            = request('msme_number');
                    $tradeLicenseNumbers    = request('trade_license_number');
                    $gstRegisteredNames     = request('gst_registered_name');
                    $gstRegisteredAddresses = request('gst_registered_address');
                    $gstRegistrationDates   = request('gst_registration_date');
                    $gstExpiryDates         = request('gst_expiry_date');

                    foreach ($gstNumbers as $key => $value) {

                        $row = [

                            'organization_id'         => $organizationId,
                            'gst_number'              => strtoupper(htmlEncode($gstNumbers[$key] ?? '')),
                            'pan_number'              => strtoupper(htmlEncode($panNumbers[$key] ?? '')),
                            'tan_number'              => strtoupper(htmlEncode($tanNumbers[$key] ?? '')),
                            'cin_number'              => strtoupper(htmlEncode($cinNumbers[$key] ?? '')),
                            'msme_number'             => htmlEncode($msmeNumbers[$key] ?? ''),
                            'trade_license_number'    => htmlEncode($tradeLicenseNumbers[$key] ?? ''),
                            'gst_registered_name'     => htmlEncode($gstRegisteredNames[$key] ?? ''),
                            'gst_registered_address'  => htmlEncode($gstRegisteredAddresses[$key] ?? ''),
                            'gst_registration_date'   => !empty($gstRegistrationDates[$key]) ? $gstRegistrationDates[$key] : null,
                            'gst_expiry_date'         => !empty($gstExpiryDates[$key]) ? $gstExpiryDates[$key] : null,

                            'is_gst_verified'         => 0,
                            'verified_by'             => null,
                            'verified_at'             => null,
                            'remarks'                => null,
                            'active_status'          => 1,

                            'created_by'             => auth()->id(),
                            'created_at'             => now(),
                            'updated_by'             => auth()->id(),
                            'updated_at'             => now(),
                        ];

                        $insertId = DB::table('mst_organization_tax_details')
                            ->insertGetId($row);

                        app(CommonController::class)->auditLog(
                            'mst_organization_tax_details',
                            $insertId,
                            'INSERT',
                            [],
                            $row
                        );
                    }

                    DB::commit();

                    return redirect($redirectPage)->with([
                        'level'   => 'success',
                        'message' => 'Organization Tax Details saved successfully.'
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

            Log::error("Organization Tax Details Error", [
                'Controller' => 'OrganizationTaxDetailsController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.organizationTaxDetails', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
