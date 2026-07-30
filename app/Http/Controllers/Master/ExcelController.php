<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Validator;

class ExcelController extends Controller
{
    public function excel()
    {
        return view('master.excel');
    }

    public function add($encId = null)
    {
        $data = [];
        $data['strPage']   = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset']  = 'Reset';

        Log::info('ExcelController add() called', [
            'method' => request()->method(),
            'hasFile' => request()->hasFile('excel_file')
        ]);

        if (request()->isMethod('post')) {

            Log::info('POST request received');

            if (!request()->hasFile('excel_file')) {
                Log::error('No file received');
                return back()->with('message', 'No file uploaded.');
            }

            Log::info('Uploaded file', [
                'name' => request()->file('excel_file')->getClientOriginalName(),
                'extension' => request()->file('excel_file')->getClientOriginalExtension(),
                'size' => request()->file('excel_file')->getSize()
            ]);

            $validator = Validator::make(request()->all(), [
                'excel_file' => 'required|mimes:xlsx,xls,csv'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator);
            }

            DB::beginTransaction();

            try {

                $file = request()->file('excel_file');
                $rows = [];
                if ($file->getClientOriginalExtension() == 'csv') {
                    if (($handle = fopen($file->getRealPath(), 'r')) !== FALSE) {
                        $header = fgetcsv($handle);
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            $rows[] = array_combine($header, $data);
                        }

                        fclose($handle);
                    }
                } else {

                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                    $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                    $header = array_shift($sheet);
                    foreach ($sheet as $row) {

                        $rows[] = [
                            'organisation_name'   => trim($row['A'] ?? ''),
                            'operator_url'         => trim($row['B'] ?? ''),
                            'bank_account_name'    => trim($row['C'] ?? ''),
                            'bank_name'            => trim($row['D'] ?? ''),
                            'bank_ifsc'            => trim($row['E'] ?? ''),
                            'bank_account_number'  => trim($row['F'] ?? ''),
                            'status'               => 1
                        ];
                    }
                }


                foreach ($rows as $index => $row) {

                    $organizationName = trim($row['organisation_name']);

                    if ($organizationName == '') {
                        Log::warning("Row {$index}: Organization name is blank.");
                        continue;
                    }

                    $organization = DB::table('mst_organization')
                        ->where('organization_name', $organizationName)
                        ->first();

                    if (!$organization) {

                        Log::warning("Organization not found", [
                            'organization_name' => $organizationName
                        ]);

                        continue;
                    }

                    $activeStatus = ($row['status'] == 1) ? 1 : 0;
                    $accountNumber = trim($row['bank_account_number']);
                    if (!empty($accountNumber)) {

                        $exists = DB::table('mst_organization_bank_accounts')
                            ->where('account_number', $accountNumber)
                            ->exists();

                        if ($exists) {

                            Log::warning('Duplicate account number skipped', [
                                'organization_name' => $organizationName,
                                'account_number' => $accountNumber
                            ]);

                            continue;
                        }
                    }

                    $insertId = DB::table('mst_organization_bank_accounts')->insertGetId([

                        'organization_id' => $organization->id,

                        'bank_name' => $row['bank_name'] ?: null,

                        'branch_name' => null,

                        'account_holder' => $row['bank_account_name'] ?: null,

                        'account_number' => $row['bank_account_number'] ?: null,

                        'ifsc' => $row['bank_ifsc']
                            ? strtoupper($row['bank_ifsc'])
                            : null,

                        'is_primary' => null,

                        'active_status' => $activeStatus,

                        'upi_id' => null,

                        'created_at' => now(),

                        'created_by' => auth()->id(),

                        'updated_at' => null,

                        'updated_by' => null

                    ]);

                    Log::info('Bank account imported', [
                        'bank_account_id' => $insertId,
                        'organization_id' => $organization->id,
                        'organization_name' => $organizationName
                    ]);
                }

                DB::commit();

                return redirect()
                    ->route('excel.index')
                    ->with([
                        'level' => 'success',
                        'message' => 'Organization bank accounts imported successfully.'
                    ]);
            } catch (\Throwable $e) {

                DB::rollBack();

                Log::error('Bank Import Failed', [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'trace' => $e->getTraceAsString()
                ]);

                return back()->with([
                    'level' => 'danger',
                    'message' => $e->getMessage()
                ]);
            }
        }

        return view('master.addExcel', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
