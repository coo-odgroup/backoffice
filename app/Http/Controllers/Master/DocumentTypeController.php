<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DocumentTypeController extends Controller
{
    public function documentType()
    {
        return view('master.documentType');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));

            $selStatus = (request('selStatus') !== null && request('selStatus') !== '')
                ? (int) request('selStatus')
                : '';

            $dataQuery = DB::table('mst_document_types as m')
                ->select(
                    'm.id as documentType_id',
                    'm.document_code',
                    'm.document_name',
                    'm.is_mandatory',
                    'm.has_expiry',
                    'm.created_at',
                    'm.updated_at',
                    'm.active_status',
                    DB::raw('(SELECT name FROM users WHERE id = m.created_by) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = m.updated_by) as updated_by_name')
                );

            if (!empty($txtSearch)) {

                $dataQuery->where(function ($q) use ($txtSearch) {

                    $q->where('m.document_name', 'like', "%{$txtSearch}%")
                        ->orWhere('m.document_code', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== '') {
                $dataQuery->where('m.active_status', $selStatus);
            }

            $count = $dataQuery->count();

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            if (!empty(request('order'))) {

                $columns = [
                    2 => 'm.document_name',
                    3 => 'm.document_code',
                    4 => 'm.updated_at',
                    5 => 'm.active_status'
                ];

                $orderBy = request('order');

                $orderColumn = $columns[$orderBy[0]['column']] ?? 'm.document_name';
                $orderType   = $orderBy[0]['dir'];
            } else {

                $orderColumn = 'm.document_name';
                $orderType   = 'asc';
            }

            $dataQuery->orderBy($orderColumn, $orderType);

            if ($length == -1) {

                $arrRes = $dataQuery->get();
            } else {

                $arrRes = $dataQuery
                    ->offset($start)
                    ->limit($length)
                    ->get();
            }

            foreach ($arrRes as $val) {

                $val->created_date = date('d-M-Y H:i:s', strtotime($val->created_at));

                $val->updated_date = !empty($val->updated_at)
                    ? date('d-M-Y H:i:s', strtotime($val->updated_at))
                    : null;

                $val->is_active = ($val->active_status == 1) ? 'Active' : 'Inactive';

                // Convert 1/0 to Yes/No
                $val->is_mandatory = (int) $val->is_mandatory;
                $val->has_expiry   = (int) $val->has_expiry;

                $val->enc_documentType_id = Crypt::encryptString($val->documentType_id);
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error('DocumentTypeController@DataTableView', [
                'message' => $t->getMessage()
            ]);
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

                $redirectPage = route('documentType.edit', $encId);

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = DB::table('mst_document_types')
                    ->select(
                        'id',
                        'document_code',
                        'document_name',
                        'is_mandatory',
                        'has_expiry'
                    )
                    ->where('id', $id)
                    ->first();


                if (!$dataResQry) {
                    return redirect()->route('documentType.index');
                }

                $data['row'] = $dataResQry;
            } else {

                $id = 0;
                $redirectPage = route('documentType.index');
            }

            if (request()->isMethod('post')) {
                $validator = Validator::make(request()->all(), [

                    'documentCode' => 'required|max:100',
                    'documentType' => 'required|max:100',

                ], [

                    'documentCode.required' => 'Document Code cannot be blank.',
                    'documentType.required' => 'Document Name cannot be blank.',

                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $documentCode = strtoupper(htmlEncode(request('documentCode')));
                $documentName = htmlEncode(request('documentType'));

                $isMandatory = request()->has('is_mandatory') ? 1 : 0;
                $hasExpiry   = request()->has('has_expiry') ? 1 : 0;

                $duplicate = DB::table('mst_document_types')
                    ->where(function ($q) use ($documentCode, $documentName) {
                        $q->where('document_code', $documentCode)
                            ->orWhere('document_name', $documentName);
                    });

                if ($id > 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {

                    return back()->with([
                        'level' => 'danger',
                        'message' => 'Document already exists.'
                    ])->withInput();
                }

                if ($id > 0) {

                    DB::table('mst_document_types')
                        ->where('id', $id)
                        ->update([

                            'document_code' => $documentCode,
                            'document_name' => $documentName,
                            'is_mandatory'  => $isMandatory,
                            'has_expiry'    => $hasExpiry,

                            'updated_by' => auth()->id(),
                            'updated_at' => now(),

                        ]);
                } else {

                    DB::table('mst_document_types')->insert([

                        'document_code' => $documentCode,
                        'document_name' => $documentName,
                        'is_mandatory'  => $isMandatory,
                        'has_expiry'    => $hasExpiry,

                        'active_status' => 1,
                        'created_by'    => auth()->id(),
                        'created_at'    => now(),

                    ]);
                }

                DB::commit();

                session()->flash(
                    'level',
                    'success'
                );

                session()->flash(
                    'message',
                    'Document Type ' . ($id ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'documentTypeController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addDocumentType', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
