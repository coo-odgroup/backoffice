<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\TicketFareSlab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Validator;

class TicketFareSlabController extends Controller
{
    public function index()
    {
        return view('Master.ticketfareslab');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtsearch'));
            $selStatus = request('selstatus');

            $dataQuery = DB::table('mst_ticket_fare_slab as tfs')
                ->select(
                    'tfs.id as slab_id',
                    'tfs.slab_name',
                    'tfs.small_desc as description',
                    'tfs.created_at',
                    'tfs.created_by',
                    'tfs.updated_at',
                    'tfs.updated_by',
                    'tfs.active_status',
                    DB::raw('(SELECT name FROM users WHERE id = tfs.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = tfs.updated_by LIMIT 1) as updated_by_name')
                );

            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('tfs.slab_name', 'like', "%{$txtSearch}%")
                        ->orWhere('tfs.small_desc', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== null && $selStatus !== '') {
                $dataQuery->where('tfs.active_status', $selStatus);
            }

            $recordsTotal = $dataQuery->count();

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            $columns = [
                2 => 'tfs.slab_name',
                3 => 'tfs.small_desc',
                4 => 'tfs.updated_at',
                5 => 'tfs.active_status'
            ];

            if (!empty(request('order'))) {
                $orderBy     = request('order')[0];
                $orderColumn = $columns[$orderBy['column']] ?? 'tfs.slab_name';
                $orderType   = $orderBy['dir'] ?? 'asc';
            } else {
                $orderColumn = 'tfs.slab_name';
                $orderType   = 'asc';
            }

            $dataQuery->orderBy($orderColumn, $orderType);

            if ($length == -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery->offset($start)->limit($length)->get();
            }

            // Format
            foreach ($arrRes as $val) {

                $val->created_date = date('d-M-Y H:i:s', strtotime($val->created_at));

                $val->updated_date = $val->updated_at
                    ? date('d-M-Y H:i:s', strtotime($val->updated_at))
                    : null;

                $val->is_active = $val->active_status == 1 ? 'Active' : 'Inactive';

                $val->enc_slab_id = Crypt::encryptString($val->slab_id);
            }

            $recordsFiltered = $recordsTotal;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error("TicketFareSlabController@dataTableView Error", [
                'message' => $t->getMessage(),
                'trace'   => $t->getTraceAsString()
            ]);

            return response()->json([
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function add($encId = null)
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = route('ticketfareslab.edit', $encId);

                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = TicketFareSlab::select('id', 'slab_name', 'small_desc')
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect("ticketfareslab");
                }

                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = route('ticketfareslab.index');
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'slab_name' => 'required'
                ], [
                    'slab_name.required' => 'Slab Name cannot be left blank.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $slab_name  = htmlEncode(request('slab_name'));
                $small_desc = htmlEncode(request('small_desc'));

                $duplicate = TicketFareSlab::where('slab_name', $slab_name);

                if ($id != 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Slab already exists'
                    ])->withInput();
                }

                // ================= UPDATE =================
                if ($id != 0) {

                    $oldData = TicketFareSlab::find($id);

                    $newData = [
                        'slab_name'  => $slab_name,
                        'small_desc' => $small_desc
                    ];

                    $oldChanged = [];
                    $newChanged = [];

                    foreach ($newData as $key => $value) {
                        if (trim((string)$oldData->$key) !== trim((string)$value)) {
                            $oldChanged[$key] = $oldData->$key;
                            $newChanged[$key] = $value;
                        }
                    }

                    if (!empty($newChanged)) {
                        app(CommonController::class)->auditLog(
                            'mst_ticket_fare_slab',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    $oldData->slab_name  = $slab_name;
                    $oldData->small_desc = $small_desc;
                    $oldData->updated_by = 1;

                    // ✅ IMPORTANT FIX
                    $oldData->updated_at = now();

                    $oldData->save();
                }
                // ================= INSERT =================
                else {

                    $row = [
                        'slab_name'     => $slab_name,
                        'small_desc'    => $small_desc,
                        'created_by'    => 1,
                        'active_status' => 1,
                        'created_at'    => now(),
                        'updated_at'    => null // ✅ force NULL
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_ticket_fare_slab',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    $obj = new TicketFareSlab();

                    // ✅ IMPORTANT FIX (disable auto timestamps)
                    $obj->timestamps = false;

                    $obj->fill($row);
                    $obj->save();
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Ticket Fare Slab ' . (($id != 0) ? 'updated' : 'created') . ' successfully.'
                );

                return redirect()->route('ticketfareslab.index');
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("TicketFareSlabController Error", [
                'Method' => $method,
                'Error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addTicketFareSlab', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
