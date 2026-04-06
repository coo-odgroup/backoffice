<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\TicketFareSlabInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;

class TicketFareSlabInfoController extends Controller
{
    public function index()
    {
        return view('Master.ticketFareSlabInfo');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            // ✅ MATCH BLADE PARAMS
            $txtSearch = htmlEncode(request('txtsearch'));
            $selStatus = request('selstatus');

            $dataQuery = DB::table('mst_ticket_fare_slab_info as t')

                ->leftJoin('mst_ticket_fare_slab as s', 's.id', '=', 't.slab_id')

                ->leftJoin('users as u', function ($join) {
                    $join->on('u.id', '=', 't.bus_operator_id')
                        ->where('u.user_role', 9);
                })

                ->select(
                    't.id',
                    't.slab_id',
                    's.slab_name',
                    't.bus_operator_id',
                    'u.organization_name as operator_name',
                    't.starting_fare',
                    't.upto_fare',
                    't.commision',
                    't.from_date',
                    't.to_date',
                    't.active_status',
                    't.created_at',
                    't.updated_at',
                    DB::raw('(SELECT name FROM users WHERE id = t.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = t.updated_by LIMIT 1) as updated_by_name')
                );

            // 🔍 SEARCH
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('s.slab_name', 'like', "%{$txtSearch}%")
                        ->orWhere('u.organization_name', 'like', "%{$txtSearch}%");
                });
            }

            // ✅ STATUS FILTER
            if ($selStatus !== null && $selStatus !== '') {
                $dataQuery->where('t.active_status', $selStatus);
            }

            // ✅ COUNT FIX
            $countQuery = clone $dataQuery;
            $recordsTotal = $countQuery->count();

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            $columns = [
                2 => 's.slab_name',
                3 => 'u.organization_name',
                4 => 't.starting_fare',
                5 => 't.upto_fare',
                6 => 't.commision',
                7 => 't.from_date',
                8 => 't.to_date',
                10 => 't.active_status'
            ];

            if (!empty(request('order'))) {
                $orderBy     = request('order')[0];
                $orderColumn = $columns[$orderBy['column']] ?? 't.id';
                $orderType   = $orderBy['dir'] ?? 'desc';
            } else {
                $orderColumn = 't.id';
                $orderType   = 'desc';
            }

            $dataQuery->orderBy($orderColumn, $orderType);

            $arrRes = ($length == -1)
                ? $dataQuery->get()
                : $dataQuery->offset($start)->limit($length)->get();

            foreach ($arrRes as $val) {

                $val->created_date = date('d-M-Y H:i:s', strtotime($val->created_at));

                $val->updated_date = $val->updated_at
                    ? date('d-M-Y H:i:s', strtotime($val->updated_at))
                    : null;

                $val->is_active = $val->active_status == 1 ? 'Active' : 'Inactive';

                $val->enc_id = Crypt::encryptString($val->id);
            }

            $recordsFiltered = $recordsTotal;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error("TicketFareSlabInfoController Error", [
                'message' => $t->getMessage()
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
        $data['strPage']   = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset']  = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = "admin/ticketfareslab-info/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                // ✅ FETCH EXISTING DATA
                $row = DB::table('mst_ticket_fare_slab_info')
                    ->where('slab_id', $id)
                    ->get();

                if ($row->isEmpty()) {
                    return redirect('ticketfareslab-info');
                }

                $data['row'] = $row;
            } else {
                $id = 0;
                $redirectPage = "admin/ticketfareslab-info";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                // ✅ VALIDATION
                $validator = Validator::make(request()->all(), [
                    'slab_id' => 'bail|required|integer',
                    'bus_operator_id' => 'bail|required',

                    'starting_fare.*' => 'required|numeric|min:0',
                    'upto_fare.*'     => 'required|numeric',
                    'commision.*'     => 'required|numeric|min:0|max:100',

                    'from_date.*' => 'required|date',
                    'to_date.*'   => 'required|date',
                ], [
                    'slab_id.required' => 'Please select slab',
                    'bus_operator_id.required' => 'Please select at least one operator',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                // ✅ INPUT CLEANING
                $slab_id = (int) request('slab_id');
                $bus_operator_id = request('bus_operator_id');

                $operators = !empty($bus_operator_id)
                    ? explode(',', $bus_operator_id)
                    : [];

                $starting_fare = request('starting_fare');
                $upto_fare     = request('upto_fare');
                $commision     = request('commision');
                $from_date     = request('from_date');
                $to_date       = request('to_date');

                if (empty($operators)) {
                    DB::rollBack();
                    return back()->with([
                        'level' => 'danger',
                        'message' => 'Please select at least one operator'
                    ])->withInput();
                }

                // ✅ MANUAL VALIDATION (ARRAY COMPARISON)
                foreach ($starting_fare as $i => $start) {
                    if ($upto_fare[$i] < $start) {
                        DB::rollBack();
                        return back()->with([
                            'level' => 'danger',
                            'message' => 'To Fare must be greater than or equal to From Fare'
                        ])->withInput();
                    }

                    if ($to_date[$i] < $from_date[$i]) {
                        DB::rollBack();
                        return back()->with([
                            'level' => 'danger',
                            'message' => 'To Date must be after From Date'
                        ])->withInput();
                    }
                }

                // ✅ DELETE OLD DATA (EDIT MODE)
                if ($id > 0) {
                    DB::table('mst_ticket_fare_slab_info')
                        ->where('slab_id', $slab_id)
                        ->delete();
                }

                // ✅ PREPARE INSERT DATA
                $insertData = [];

                foreach ($operators as $operator) {

                    foreach ($starting_fare as $key => $val) {

                        $rowData = [
                            'slab_id'         => $slab_id,
                            'bus_operator_id' => (int) $operator,
                            'starting_fare'   => $starting_fare[$key],
                            'upto_fare'       => $upto_fare[$key],
                            'commision'       => $commision[$key],
                            'from_date'       => $from_date[$key],
                            'to_date'         => $to_date[$key],
                            'active_status'   => 1,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];

                        $insertData[] = $rowData;

                        app(CommonController::class)->auditLog(
                            'mst_ticket_fare_slab_info',
                            null,
                            ($id > 0 ? 'UPDATE' : 'INSERT'),
                            [],
                            $rowData
                        );
                    }
                }

                DB::table('mst_ticket_fare_slab_info')->insert($insertData);

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Ticket Fare Slab Info ' . ($id > 0 ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error in TicketFareSlabInfoController@add", [
                'method' => $method,
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addTicketFareSlabInfo', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function getOperatorSlabData(Request $request)
    {
        $operator_id = $request->operator_id;

        $data = DB::table('mst_ticket_fare_slab_info as t')
            ->leftJoin('mst_ticket_fare_slab as s', 's.id', '=', 't.slab_id')
            ->where('t.bus_operator_id', $operator_id)
            ->select(
                't.*',
                's.slab_name'
            )
            ->orderBy('t.starting_fare')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
