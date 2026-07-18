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

class OrganizationDocumentController extends Controller
{
    public function organizationDocument()
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

                $data['documentTypes'] = DB::table('mst_document_types')
                    ->where('active_status', 1)
                    ->orderBy('document_name')
                    ->get();

                $data['documents'] = DB::table('mst_organization_documents')
                    ->where('organization_id', $id)
                    ->where('active_status', 1)
                    ->orderBy('id')
                    ->get();
            }

            if ($id > 0) {

                $redirectPage = "admin/organization-document/edit/" . $encId;

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

                // Load Document Types
                $data['documentTypes'] = DB::table('mst_document_types')
                    ->where('active_status', 1)
                    ->orderBy('document_name')
                    ->get();

                // Load previously saved documents
                $data['documents'] = DB::table('mst_organization_documents')
                    ->where('organization_id', $id)
                    ->where('active_status', 1)
                    ->orderBy('id')
                    ->get();
            } else {

                $redirectPage = "admin/organization-document";

                $data['documentTypes'] = DB::table('mst_document_types')
                    ->where('active_status', 1)
                    ->orderBy('document_name')
                    ->get();

                // Empty collection for Add page
                $data['documents'] = collect();
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [

                    'document_type.*'   => 'required|exists:mst_document_types,id',
                    'document_number.*' => 'required|max:100',
                    'file_name.*'       => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
                    'issue_date.*'      => 'nullable|date',
                    'expiry_date.*'     => 'nullable|date',

                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                try {

                    $organizationId   = $id;
                    $documentIds      = request('document_id', []);
                    $documentTypes    = request('document_type', []);
                    $documentNumbers  = request('document_number', []);
                    $issueDates       = request('issue_date', []);
                    $expiryDates      = request('expiry_date', []);
                    $files            = request()->file('file_name', []);

                    foreach ($documentTypes as $key => $documentType) {

                        $row = [

                            'document_type'   => $documentType,
                            'document_number' => htmlEncode($documentNumbers[$key] ?? ''),
                            'issue_date'      => !empty($issueDates[$key]) ? $issueDates[$key] : null,
                            'expiry_date'     => !empty($expiryDates[$key]) ? $expiryDates[$key] : null,
                            'updated_by'      => auth()->id(),
                            'updated_at'      => now(),

                        ];

                        // Upload new file only if selected
                        if (!empty($files[$key])) {

                            $file = $files[$key];

                            $extension = $file->getClientOriginalExtension();

                            $fileName = time() . '_' . $key . '.' . $extension;

                            $file->move(
                                public_path('uploads/organization-documents'),
                                $fileName
                            );

                            $row['file_name'] = $fileName;
                            $row['file_path'] = 'uploads/organization-documents/' . $fileName;
                        }

                        // Existing document -> Update
                        if (!empty($documentIds[$key])) {

                            $oldData = DB::table('mst_organization_documents')
                                ->where('id', $documentIds[$key])
                                ->first();

                            DB::table('mst_organization_documents')
                                ->where('id', $documentIds[$key])
                                ->update($row);

                            app(CommonController::class)->auditLog(
                                'mst_organization_documents',
                                $documentIds[$key],
                                'UPDATE',
                                (array)$oldData,
                                $row
                            );
                        } else {

                            // New document -> Insert
                            $row['organization_id'] = $organizationId;
                            $row['active_status']   = 1;
                            $row['created_by']      = auth()->id();
                            $row['created_at']      = now();

                            // File is mandatory for new document
                            if (empty($row['file_name'])) {
                                throw new \Exception('Please upload a file for newly added document.');
                            }

                            $insertId = DB::table('mst_organization_documents')
                                ->insertGetId($row);

                            app(CommonController::class)->auditLog(
                                'mst_organization_documents',
                                $insertId,
                                'INSERT',
                                [],
                                $row
                            );
                        }
                    }

                    DB::commit();

                    return redirect($redirectPage)->with([
                        'level'   => 'success',
                        'message' => 'Organization Documents saved successfully.'
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


        return view('Master.organizationDocument', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
