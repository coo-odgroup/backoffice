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
                            'organisation_name' => trim($row['A'] ?? ''),
                            'email'             => trim($row['B'] ?? ''),
                            'mobile_number'     => trim($row['C'] ?? ''),
                            'operator_name'     => trim($row['D'] ?? ''),
                            'status'            => trim($row['G'] ?? ''),
                        ];
                    }
                }


                foreach ($rows as $index => $row) {

                    $organizationName = trim($row['organisation_name']);

                    if (empty($organizationName)) {
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
                    $mobile = trim($row['mobile_number']);

                    if (empty($mobile)) {
                        $mobile = '9999999999';
                    }

                    $contactExists = DB::table('mst_organization_contacts')
                        ->where('organization_id', $organization->id)
                        ->where('mobile', $mobile)
                        ->exists();

                    if ($contactExists) {

                        Log::warning('Duplicate contact skipped', [
                            'organization_name' => $organizationName,
                            'mobile' => $row['mobile_number']
                        ]);

                        continue;
                    }

                    DB::table('mst_organization_contacts')->insert([
                        'organization_id'   => $organization->id,
                        'contact_type'      => 1, // ALL
                        'fullname'          => $row['operator_name'] ?: null,
                        'designation'       => 'Owner',
                        'mobile' => $mobile,
                        'alternate_mobile'  => null,
                        'email'             => $row['email'] ?: null,
                        'is_primary'        => 1,
                        'active_status'     => ($row['status'] == 1) ? 1 : 0,
                        'created_at'        => now(),
                        'created_by'        => auth()->id(),
                        'updated_at'        => null,
                        'updated_by'        => null,
                    ]);

                    Log::info('Contact imported', [
                        'organization_id' => $organization->id,
                        'organization_name' => $organizationName,
                        'mobile' => $mobile
                    ]);
                }
                DB::commit();

                return redirect()
                    ->route('excel.index')
                    ->with([
                        'level' => 'success',
                        'message' => 'Organization contacts imported successfully.'
                    ]);
            } catch (\Throwable $e) {

                DB::rollBack();

                Log::error('Contacts Import Failed', [
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
