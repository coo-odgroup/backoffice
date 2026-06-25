<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Str;

class SchemaController extends Controller
{
    public function index()
    {
        return view('Master.schema');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));

            $status = (
                request('selStatus') !== null &&
                request('selStatus') !== ''
            ) ? (int) request('selStatus') : '';

            $schemaType = (int) request('schema_type');
            $schemaPage = (int) request('schema_page');

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            $dataQuery = DB::table('odbusmaster.mst_schema as ms')
                ->select(
                    'ms.id',
                    'ms.schema_page_id',
                    'ms.schema_type_id',
                    'ms.schema_content',
                    'ms.active_status',
                    'ms.created_at',
                    'ms.updated_at',

                    DB::raw("(
                                SELECT annexture_name
                                FROM odbusmaster.mst_annexture
                                WHERE annexture_type_id = 25
                                AND annexture_value = ms.schema_page_id
                                LIMIT 1
                            ) as schema_page_name"),

                    DB::raw("(
                                SELECT annexture_name
                                FROM odbusmaster.mst_annexture
                                WHERE annexture_type_id = 26
                                AND annexture_value = ms.schema_type_id
                                LIMIT 1
                            ) as schema_type_name"),

                    DB::raw("(
                    SELECT name
                    FROM odbusmaster.users
                    WHERE id = ms.created_by
                    LIMIT 1
                ) as created_by_name"),

                    DB::raw("(
                    SELECT name
                    FROM odbusmaster.users
                    WHERE id = ms.updated_by
                    LIMIT 1
                ) as updated_by_name")
                );

            // Search
            if (!empty($txtSearch)) {

                $dataQuery->where(function ($q) use ($txtSearch) {

                    $q->where('ms.schema_content', 'like', "%{$txtSearch}%");
                });
            }

            // Filters
            if ($schemaType > 0) {
                $dataQuery->where('ms.schema_type_id', $schemaType);
            }

            if ($schemaPage > 0) {
                $dataQuery->where('ms.schema_page_id', $schemaPage);
            }

            if ($status !== '') {
                $dataQuery->where('ms.active_status', $status);
            }

            $count = $dataQuery->count();

            // Ordering
            $columns = [
                2 => 'schema_page_name',
                3 => 'schema_type_name',
                4 => 'ms.schema_content',
                6 => 'ms.created_at',
                7 => 'ms.active_status'
            ];

            if (!empty(request('order'))) {

                $order = request('order')[0];

                $orderColumn = $columns[$order['column']] ?? 'ms.id';
                $orderType   = $order['dir'];
            } else {

                $orderColumn = 'ms.id';
                $orderType   = 'desc';
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

                $val->enc_id = Crypt::encryptString($val->id);
                $val->schema_page = $val->schema_page_name ?? '--';
                $val->schema_type = $val->schema_type_name ?? '--';
                $val->schema_content = Str::limit(strip_tags($val->schema_content), 80);
                $val->created_date = $val->created_at ? date('d-M-Y H:i:s', strtotime($val->created_at)) : '--';
                $val->updated_date = $val->updated_at ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : '--';
                $val->created_by_name = $val->created_by_name ?? '--';
                $val->updated_by_name = $val->updated_by_name ?? '--';
                $val->is_active = $val->active_status == 1 ? 'Active' : 'Inactive';
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error('Schema DataTable Error', [
                'message' => $t->getMessage(),
                'line'    => $t->getLine(),
                'file'    => $t->getFile()
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
        $data['strPage']   = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset']  = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $data['strPage']   = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = DB::table('odbusmaster.mst_schema')
                    ->where('id', $id)
                    ->first();

                if (!$row) {
                    return redirect()->route('schema.index');
                }

                $data['row'] = $row;
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'schema_page_id' => 'required|integer',
                    'schema_type_id' => 'required|integer',
                    'schema_content'     => 'required|string',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $insertData = [
                    'schema_page_id' => (int) request('schema_page_id'),
                    'schema_type_id' => (int) request('schema_type_id'),

                    // Store JSON exactly as entered
                    'schema_content' => request('schema_content'),

                    'active_status'  => 1,
                ];

                $redirectPage = "admin/schema";

                if ($id > 0) {

                    $oldData = DB::table('odbusmaster.mst_schema')
                        ->where('id', $id)
                        ->first();

                    $newData = [
                        ...$insertData,
                        'updated_by' => 1, // Replace with auth()->id()
                        'updated_at' => now(),
                    ];

                    $oldChanged = [];
                    $newChanged = [];

                    foreach ($insertData as $key => $value) {

                        $oldValue = $oldData->$key ?? null;

                        if ((string)$oldValue !== (string)$value) {

                            $oldChanged[$key] = $oldValue;
                            $newChanged[$key] = $value;
                        }
                    }

                    if (!empty($newChanged)) {

                        app(CommonController::class)->auditLog(
                            'mst_schema',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    DB::table('odbusmaster.mst_schema')
                        ->where('id', $id)
                        ->update($newData);
                } else {

                    $row = [
                        ...$insertData,
                        'created_by' => 1, // Replace with auth()->id()
                        'created_at' => now(),
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_schema',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    DB::table('odbusmaster.mst_schema')
                        ->insertGetId($row);
                }

                DB::commit();

                return redirect($redirectPage)->with([
                    'level'   => 'success',
                    'message' => 'Schema ' . ($id ? 'updated' : 'created') . ' successfully'
                ]);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Schema Error", [
                'method' => $data['strPage'],
                'error'  => $t->getMessage(),
                'line'   => $t->getLine(),
                'file'   => $t->getFile(),
            ]);

            return back()
                ->withInput()
                ->with([
                    'level'   => 'danger',
                    'message' => config('constants.SERVER_ERROR_MESSAGE')
                ]);
        }

        return view('Master.addSchema', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function viewDetails(Request $request)
    {
        try {

            $id = (int) $request->id;

            $schema = DB::table('odbusmaster.mst_schema as ms')
                ->select(
                    'ms.id',
                    'ms.schema_content',

                    DB::raw("(
                    SELECT ma.annexture_name
                    FROM odbusmaster.mst_annexture ma
                    INNER JOIN odbusmaster.mst_annexture_type mat
                        ON mat.id = ma.annexture_type_id
                    WHERE mat.annexture_type = 'SCHEMA_PAGE'
                    AND ma.annexture_value = ms.schema_page_id
                    LIMIT 1
                ) as schema_page"),

                    DB::raw("(
                    SELECT ma.annexture_name
                    FROM odbusmaster.mst_annexture ma
                    INNER JOIN odbusmaster.mst_annexture_type mat
                        ON mat.id = ma.annexture_type_id
                    WHERE mat.annexture_type = 'SCHEMA_TYPE'
                    AND ma.annexture_value = ms.schema_type_id
                    LIMIT 1
                ) as schema_type")
                )
                ->where('ms.id', $id)
                ->first();

            if (!$schema) {
                return response()->json([
                    'status' => false,
                    'message' => 'Schema not found'
                ]);
            }

            return response()->json([
                'status' => true,
                'data' => $schema
            ]);
        } catch (\Throwable $e) {

            Log::error($e);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }
}
