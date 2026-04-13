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

class BusCancelController extends Controller
{
    public function index()
    {
        return view('Master.viewBusCancel');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtsearch'));
            $selStatus = request('selstatus');

            $query = DB::table('mst_ticket_fare_slab_info as t')
                ->leftJoin('mst_ticket_fare_slab as s', 's.id', '=', 't.slab_id')
                ->leftJoin('users as u', function ($join) {
                    $join->on('u.id', '=', 't.bus_operator_id')
                        ->where('u.user_role', 9);
                })
                ->select(
                    't.id',
                    't.slab_id',
                    's.slab_name',
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

            if (!empty($txtSearch)) {
                $query->where(function ($q) use ($txtSearch) {
                    $q->where('s.slab_name', 'like', "%{$txtSearch}%")
                        ->orWhere('u.organization_name', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== null && $selStatus !== '') {
                $query->where('t.active_status', $selStatus);
            }

            $rows = $query->orderBy('t.id', 'desc')->get();

            $grouped = [];

            foreach ($rows as $row) {

                $slabId = $row->slab_id;

                if (!isset($grouped[$slabId])) {

                    $grouped[$slabId] = [
                        'id' => $row->id,
                        'slab_id' => $row->slab_id,
                        'slab_name' => $row->slab_name,
                        'operators' => [],
                        'slab_info' => [],
                        'created_date' => date('d-M-Y H:i:s', strtotime($row->created_at)),
                        'updated_date' => $row->updated_at
                            ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                            : null,
                        'created_by_name' => $row->created_by_name,
                        'updated_by_name' => $row->updated_by_name,
                        'is_active' => $row->active_status == 1 ? 'Active' : 'Inactive',
                        'enc_id' => Crypt::encryptString($row->slab_id),
                    ];
                }

                if (
                    !empty($row->operator_name) &&
                    !in_array($row->operator_name, $grouped[$slabId]['operators'])
                ) {

                    $grouped[$slabId]['operators'][] = $row->operator_name;
                }

                $key = md5(
                    $row->starting_fare . '|' .
                        $row->upto_fare . '|' .
                        $row->commision . '|' .
                        date('Y-m-d', strtotime($row->from_date)) . '|' .
                        date('Y-m-d', strtotime($row->to_date))
                );

                if (!isset($grouped[$slabId]['slab_info'][$key])) {

                    $grouped[$slabId]['slab_info'][$key] = [
                        'starting_fare' => $row->starting_fare,
                        'upto_fare' => $row->upto_fare,
                        'commision' => $row->commision,
                        'from_date' => $row->from_date,
                        'to_date' => $row->to_date,
                    ];
                }
            }

            // ✅ MOVE THIS OUTSIDE LOOP (VERY IMPORTANT)
            foreach ($grouped as &$slab) {
                $slab['slab_info'] = array_values($slab['slab_info']);
            }

            $data = array_values($grouped);

            $recordsTotal = count($data);
            $recordsFiltered = $recordsTotal;
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
        $data['strPage']   = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset']  = 'Reset';

        try {

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'operator' => 'required',
                    'bus'      => 'required',
                    'year'     => 'required',
                    'month'    => 'required',
                    'reason'   => 'required',
                    'dates'    => 'required|array|min:1',
                ], [
                    'operator.required' => 'Please select operator',
                    'bus.required'      => 'Please select at least one bus',
                    'year.required'     => 'Please select year',
                    'month.required'    => 'Please select month',
                    'reason.required'   => 'Please select reason',
                    'dates.required'    => 'Please select at least one date',
                    'dates.array'       => 'Invalid date selection',
                    'dates.min'         => 'Please select at least one date',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                if (request('reason') == 77 && empty(trim(request('other_reason')))) {
                    return back()->with([
                        'level' => 'danger',
                        'message' => 'Please enter other reason'
                    ])->withInput();
                }

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $operator_id = request('operator');
                $bus_ids = explode(',', request('bus'));
                $year = request('year');
                $month = request('month');
                $reason = request('reason');
                $other_reason = request('other_reason');
                $dates = request('dates');

                foreach ($bus_ids as $bus_id) {

                    // 🔹 Insert into bus_cancelled
                    $cancel_id = DB::connection('mysql_dev')
                        ->table('bus_cancelled')
                        ->insertGetId([
                            'bus_id' => $bus_id,
                            'bus_operator_id' => $operator_id,
                            'month' => $month,
                            'year' => $year,
                            'reason' => $reason,
                            'other_reason' => ($reason == 77) ? $other_reason : null,
                            'active_status' => 1,
                            'created_at' => now(),
                        ]);

                    // 🔹 Insert multiple dates
                    $dateInsert = [];

                    foreach ($dates as $date) {
                        $dateInsert[] = [
                            'bus_cancelled_id' => $cancel_id,
                            'cancelled_date' => $date,
                            'active_status' => 1,
                            'created_at' => now(),
                        ];
                    }

                    DB::connection('mysql_dev')
                        ->table('bus_cancelled_date')
                        ->insert($dateInsert);
                }

                DB::commit();

                return redirect()->back()->withInput()->with([
                    'level' => 'success',
                    'message' => 'Bus Cancelled Successfully'
                ]);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Bus Cancel Error", [
                'error' => $t->getMessage()
            ]);

            return back()->with([
                'level' => 'danger',
                'message' => 'Something went wrong'
            ]);
        }

        return view('Master.addBusCancel', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function getBusScheduleDatesByMonth(Request $request)
    {
        try {

            $bus_ids = explode(',', $request->bus_ids);
            $year = $request->year;
            $month = $request->month;

            $startDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';

            $data = [];

            $schedules = DB::connection('mysql_dev')
                ->table('bus_schedule')
                ->whereIn('bus_id', $bus_ids)
                ->where('active_status', 1)
                ->orderByDesc('id')
                ->get()
                ->groupBy('bus_id');

            foreach ($schedules as $bus_id => $rows) {

                $schedule = $rows->first();

                $dates = DB::connection('mysql_dev')
                    ->table('bus_schedule_date')
                    ->where('bus_schedule_id', $schedule->id)
                    ->whereDate('entry_date', '>=', $startDate)
                    ->orderBy('entry_date')
                    ->pluck('entry_date');

                if ($dates->isEmpty()) continue;

                $bus = DB::connection('mysql_dev')
                    ->table('bus')
                    ->where('id', $bus_id)
                    ->first();

                $data[$bus_id] = [
                    'bus_name' => $bus->name ?? '',
                    'bus_number' => $bus->bus_number ?? '',
                    'dates' => $dates
                ];
            }

            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getCancelledBusData(Request $request)
    {
        try {

            $bus_ids = explode(',', $request->bus_ids);

            $startDate = $request->year . '-' . str_pad($request->month, 2, '0', STR_PAD_LEFT) . '-01';

            $data = DB::connection('mysql_dev')
                ->table('bus_cancelled as bc')
                ->join('bus_cancelled_date as bcd', 'bcd.bus_cancelled_id', '=', 'bc.id')
                ->join('bus as b', 'b.id', '=', 'bc.bus_id')
                ->leftJoin('odbusmaster.mst_annexture as ma', 'ma.id', '=', 'bc.reason')
                ->whereIn('bc.bus_id', $bus_ids)
                ->whereDate('bcd.cancelled_date', '>=', $startDate)
                ->select(
                    'bc.bus_id',
                    'b.name as bus_name',
                    'b.bus_number',
                    'bcd.cancelled_date',
                    'bc.reason',
                    'bc.other_reason',
                    'ma.annexture_name',
                    'bc.created_at'
                )
                ->orderBy('bcd.cancelled_date')
                ->get();

            $grouped = [];

            foreach ($data as $row) {

                $reasonText = ($row->reason == 77)
                    ? $row->other_reason
                    : $row->annexture_name;

                $grouped[$row->bus_id]['bus_name'] = $row->bus_name;
                $grouped[$row->bus_id]['bus_number'] = $row->bus_number;
                $grouped[$row->bus_id]['reason'] = $reasonText;
                $grouped[$row->bus_id]['created_at'] = $row->created_at;

                $grouped[$row->bus_id]['dates'][] = $row->cancelled_date;
            }

            return response()->json([
                'status' => true,
                'data' => $grouped
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
