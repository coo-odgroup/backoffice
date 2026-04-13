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

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = request('selStatus');

            $query = DB::connection('mysql_dev')
                ->table('bus_cancelled as bc')

                // ✅ JOIN CANCELLED DATES
                ->join('bus_cancelled_date as bcd', function ($join) {
                    $join->on('bcd.bus_cancelled_id', '=', 'bc.id')
                        ->where('bcd.active_status', 1);
                })

                ->join('bus as b', 'b.id', '=', 'bc.bus_id')

                ->join('odbusmaster.users as u', function ($join) {
                    $join->on('u.id', '=', 'bc.bus_operator_id')
                        ->where('u.user_role', 9);
                })

                ->leftJoin('odbusmaster.mst_annexture as ma', 'ma.id', '=', 'bc.reason')

                ->select(
                    'bc.id',
                    'bc.bus_id',
                    'bc.bus_operator_id',

                    'u.organization_name as operator_name',

                    'b.name as bus_name',
                    'b.bus_number',

                    'bcd.cancelled_date',

                    'bc.reason',
                    'bc.other_reason',
                    'ma.annexture_name',

                    'bc.active_status',
                    'bc.created_at',
                    'bc.updated_at'
                );

            // 🔍 SEARCH
            if (!empty($txtSearch)) {
                $query->where(function ($q) use ($txtSearch) {
                    $q->where('b.name', 'like', "%{$txtSearch}%")
                        ->orWhere('b.bus_number', 'like', "%{$txtSearch}%")
                        ->orWhere('u.organization_name', 'like', "%{$txtSearch}%");
                });
            }

            // 🔍 STATUS
            if ($selStatus !== null && $selStatus !== '') {
                $query->where('bc.active_status', $selStatus);
            }

            $rows = $query->orderBy('bc.id', 'desc')->get();

            $grouped = [];

            foreach ($rows as $row) {

                $key = $row->bus_id; // 🔥 IMPORTANT (per bus)

                if (!isset($grouped[$key])) {

                    $reasonText = ($row->reason == 77)
                        ? $row->other_reason
                        : $row->annexture_name;

                    $grouped[$key] = [
                        'id' => $row->id,
                        'bus_cancel_id ' => $row->id,
                        'enc_bus_cancel_id' => Crypt::encryptString($row->id),

                        'operator' => $row->operator_name ?? '--',

                        'busName' => trim(($row->bus_name ?? '') . ' / ' . ($row->bus_number ?? '')),

                        'route' => '--',

                        'reason' => $reasonText,

                        'cancelDates' => [],

                        'created_date' => $row->created_at
                            ? date('d-M-Y H:i:s', strtotime($row->created_at))
                            : null,

                        'updated_date' => $row->updated_at
                            ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                            : null,

                        'is_active' => $row->active_status == 1 ? 'Active' : 'Inactive',
                    ];
                }

                // ✅ ADD DATES
                $grouped[$key]['cancelDates'][] = date('d-M-Y', strtotime($row->cancelled_date));
            }

            // ✅ FORMAT DATES
            foreach ($grouped as &$g) {
                $g['cancelDates'] = implode('<br>', $g['cancelDates']);
            }

            $data = array_values($grouped);

            $recordsTotal = count($data);
            $recordsFiltered = $recordsTotal;
        } catch (\Throwable $t) {

            Log::error("BusCancelController Error", [
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

                DB::beginTransaction();

                $operator_id = request('operator');
                $bus_ids = explode(',', request('bus'));
                $year = request('year');
                $month = request('month');
                $reason = request('reason');
                $other_reason = request('other_reason');
                $dates = request('dates');

                // 🔥 NEW: removed cancelled dates
                $removedDates = json_decode(request('removed_dates'), true) ?? [];

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

                    $dateInsert = [];

                    foreach ($dates as $date) {

                        // 🔥 CHECK IF ALREADY EXISTS
                        $existing = DB::connection('mysql_dev')
                            ->table('bus_cancelled as bc')
                            ->join('bus_cancelled_date as bcd', 'bcd.bus_cancelled_id', '=', 'bc.id')
                            ->where('bc.bus_id', $bus_id)
                            ->whereDate('bcd.cancelled_date', $date)
                            ->select('bcd.id')
                            ->first();

                        if ($existing) {
                            // 🔥 Reactivate if previously inactive
                            DB::connection('mysql_dev')
                                ->table('bus_cancelled_date')
                                ->where('id', $existing->id)
                                ->update([
                                    'active_status' => 1,
                                    'updated_at' => now()
                                ]);
                        } else {
                            // 🔹 Insert new
                            $dateInsert[] = [
                                'bus_cancelled_id' => $cancel_id,
                                'cancelled_date' => $date,
                                'active_status' => 1,
                                'created_at' => now(),
                            ];
                        }
                    }

                    if (!empty($dateInsert)) {
                        DB::connection('mysql_dev')
                            ->table('bus_cancelled_date')
                            ->insert($dateInsert);
                    }
                }

                if (!empty($removedDates)) {

                    foreach ($removedDates as $rd) {
                        $record = DB::connection('mysql_dev')
                            ->table('bus_cancelled as bc')
                            ->join('bus_cancelled_date as bcd', 'bcd.bus_cancelled_id', '=', 'bc.id')
                            ->where('bc.bus_id', $rd['bus_id'])
                            ->whereDate('bcd.cancelled_date', $rd['date'])
                            ->select('bcd.id')
                            ->first();

                        if ($record) {
                            DB::connection('mysql_dev')
                                ->table('bus_cancelled_date')
                                ->where('id', $record->id)
                                ->update([
                                    'active_status' => 0,
                                    'updated_at' => now()
                                ]);
                        }
                    }
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
                ->join('odbusdev.bus as b', 'b.id', '=', 'bc.bus_id')
                ->leftJoin('odbusmaster.mst_annexture as ma', 'ma.id', '=', 'bc.reason')
                ->whereIn('bc.bus_id', $bus_ids)
                ->where('bcd.active_status', 1)
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
