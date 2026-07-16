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

class OrganizationAddressController extends Controller
{
    public function organizationAddress()
    {
        return $this->add();
    }

    public function add($encId = null)
    {
        $data = [];
        $data['strPage']   = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset']  = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;
            $organizationId = $id;


            if ($id > 0) {

                $redirectPage = "admin/organization-address/edit/" . $encId;

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
                    return redirect()->route('organization-address.index');
                }

                $data['row'] = $dataResQry;

                $data['addresses'] = DB::table('mst_organization_address')
                    ->where('organization_id', $id)
                    ->where('active_status', 1)
                    ->orderBy('is_default', 'desc')
                    ->orderBy('id')
                    ->get();
            } else {

                $redirectPage = "admin/organization-address";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [

                    'address_type.*' => 'required',
                    'address1.*'     => 'required|max:255',
                    'address2.*'     => 'nullable|max:255',
                    'country_id.*'   => 'required|integer',
                    'state_id.*'     => 'required|integer',
                    'district_id.*'  => 'required|integer',
                    'selCity.*'      => 'required|integer',
                    'pincode.*'      => 'nullable|max:10',
                    'latitude.*'     => 'nullable|max:30',
                    'longitude.*'    => 'nullable|max:30',
                    'landmark.*'    => 'nullable|max:30',

                ], [

                    'address_type.*.required'  => 'Address Type is required.',
                    'address1.*.required'      => 'Address Line 1 is required.'

                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                try {

                    $organizationId = $id;


                    if ($id > 0) {

                        DB::table('mst_organization_address')
                            ->where('organization_id', $organizationId)
                            ->delete();
                    }

                    $addressTypes = request('address_type');
                    $address1s    = request('address1');
                    $address2s    = request('address2');
                    $countryIds   = request('country_id');
                    $stateIds     = request('state_id');
                    $districtIds  = request('district_id');
                    $cityIds      = request('city_id');
                    $pincodes     = request('pincode');
                    $latitudes    = request('latitude');
                    $longitudes   = request('longitude');
                    $landmark   = request('landmark');

                    foreach ($addressTypes as $key => $value) {

                        $row = [

                            'organization_id' => $organizationId,
                            'address_type' => $addressTypes[$key] ?? null,
                            'address1'     => htmlEncode($address1s[$key] ?? ''),
                            'address2'     => htmlEncode($address2s[$key] ?? ''),
                            'city_id'      => $cityIds[$key] ?? null,
                            'district_id'  => $districtIds[$key] ?? null,
                            'state_id'     => $stateIds[$key] ?? null,
                            'country_id'   => $countryIds[$key] ?? 1,
                            'pincode'      => htmlEncode($pincodes[$key] ?? ''),
                            'landmark'      => htmlEncode($landmark[$key] ?? ''),
                            'latitude'     => $latitudes[$key] ?? null,
                            'longitude'    => $longitudes[$key] ?? null,
                            'is_default'   => ($key == 0) ? 1 : 0,
                            'active_status' => 1,
                            'created_by'   => auth()->id(),
                            'created_at'   => now(),
                            'updated_by'   => auth()->id(),
                            'updated_at'   => now()

                        ];

                        $insertId = DB::table('mst_organization_address')
                            ->insertGetId($row);

                        app(CommonController::class)->auditLog(
                            'mst_organization_address',
                            $insertId,
                            'INSERT',
                            [],
                            $row
                        );
                    }

                    DB::commit();

                    return redirect($redirectPage)->with([
                        'level'   => 'success',
                        'message' => 'Organization Address saved successfully.'
                    ]);
                } catch (\Throwable $e) {

                    DB::rollBack();

                    Log::error($e);

                    return back()->with([
                        'level' => 'danger',
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

        return view('Master.organizationAddress', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
