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
                        'website_url',

                    )
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect()->route('organization-bank-account.index');
                }

                $data['row'] = $dataResQry;

                $data['accounts'] = DB::table('mst_organization_bank_accounts')
                    ->where('organization_id', $id)
                    ->where('active_status', 1)
                    ->orderByDesc('is_primary')
                    ->orderBy('id')
                    ->get();

                    
            } else {

                $redirectPage = "admin/organization-bank-account";
            }
            if (request()->isMethod('post')) {


                $validator = Validator::make(request()->all(), [

                    'account_number.*' => 'required',
                    'account_holder.*' => 'required|max:128',
                    'bank_name.*'      => 'required|max:64',
                    'branch_name.*'    => 'nullable|max:64',
                    'ifsc.*'           => 'required|max:16',
                    'upi_id.*'         => 'nullable|max:256',

                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                try {

                    $organizationId = $id;

                    DB::table('mst_organization_bank_accounts')
                        ->where('organization_id', $organizationId)
                        ->delete();

                    $accountNumbers = request('account_number');
                    $accountHolders = request('account_holder');
                    $bankNames      = request('bank_name');
                    $branchNames    = request('branch_name');
                    $ifscs          = request('ifsc');
                    $upiIds         = request('upi_id');
                    $primary        = request('primary_account');

                    


                    foreach ($accountNumbers as $key => $value) {

                        $row = [

                            'organization_id' => $organizationId,
                            'bank_name'       => htmlEncode($bankNames[$key] ?? ''),
                            'branch_name'     => htmlEncode($branchNames[$key] ?? ''),
                            'account_holder'  => htmlEncode($accountHolders[$key] ?? ''),
                            'account_number'  => htmlEncode($accountNumbers[$key] ?? ''),
                            'ifsc'            => strtoupper(htmlEncode($ifscs[$key] ?? '')),
                            'upi_id'          => htmlEncode($upiIds[$key] ?? ''),
                            'is_primary'      => ($primary == $key) ? 1 : 0,
                            'active_status'   => 1,
                            'created_by'      => auth()->id(),
                            'created_at'      => now(),
                            'updated_by'      => auth()->id(),
                            'updated_at'      => now(),
                        ];

                        $insertId = DB::table('mst_organization_bank_accounts')
                            ->insertGetId($row);

                        app(CommonController::class)->auditLog(
                            'mst_organization_bank_accounts',
                            $insertId,
                            'INSERT',
                            [],
                            $row
                        );
                    }

                    DB::commit();

                    return redirect($redirectPage)->with([
                        'level'   => 'success',
                        'message' => 'Organization Bank Accounts saved successfully.'
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

        return view('Master.organizationBankAccount', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
