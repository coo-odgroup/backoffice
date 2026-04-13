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

class BusScheduleController extends Controller
{
    public function index()
    {
        return view('Master.viewBusSchedule');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $operator  = request('operator') !== null && request('operator') !== '' ? (int)request('operator') : null;
            $bus       = request('bus') !== null && request('bus') !== '' ? (int)request('bus') : null;
            $status    = request('selStatus') !== null && request('selStatus') !== '' ? (int)request('selStatus') : null;

            $start  = request('start', 0);
            $length = request('length', 10);

            $query = DB::connection('mysql_dev')
                ->table('bus_cancelled as bc')
                ->join('bus_cancelled_date as bcd', 'bcd.bus_cancelled_id', '=', 'bc.id')
                ->select(
                    'bc.id',
                    'bc.bus_id',
                    'bc.bus_operator_id',

                    DB::raw('(SELECT name FROM odbusdev.bus WHERE id = bc.bus_id LIMIT 1) as bus_name'),
                    DB::raw('(SELECT bus_number FROM odbusdev.bus WHERE id = bc.bus_id LIMIT 1) as bus_number'),

                    DB::raw('(SELECT organization_name 
                    FROM odbusmaster.users 
                    WHERE id = bc.bus_operator_id 
                    AND user_role = 9 
                    LIMIT 1) as operator_name'),

                    'bc.reason',
                    'bc.other_reason',
                    'bc.active_status',
                    'bc.created_at',
                    'bc.updated_at',
                    'bcd.cancelled_date',

                    DB::raw('(SELECT annexture_name FROM odbusmaster.mst_annexture WHERE id = bc.reason LIMIT 1) as reason_name'),

                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = bc.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = bc.updated_by LIMIT 1) as updated_by_name')
                )
                ->where('bcd.active_status', 1); // 🔥 IMPORTANT

            // total
            $recordsTotal = DB::connection('mysql_dev')
                ->table('bus_cancelled_date')
                ->where('active_status', 1)
                ->count();

            // 🔍 SEARCH
            if (!empty(trim($txtSearch))) {

                $search = trim($txtSearch);

                $query->where(function ($q) use ($search) {
                    $q->whereRaw("(SELECT name FROM odbusdev.bus WHERE id = bc.bus_id LIMIT 1) LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("(SELECT bus_number FROM odbusdev.bus WHERE id = bc.bus_id LIMIT 1) LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("(SELECT organization_name FROM odbusmaster.users WHERE id = bc.bus_operator_id LIMIT 1) LIKE ?", ["%{$search}%"]);
                });
            }

            // 🔍 FILTERS
            if (!empty($operator)) {
                $query->where('bc.bus_operator_id', $operator);
            }

            if (!empty($bus)) {
                $query->where('bc.bus_id', $bus);
            }

            if ($status !== null && $status !== '') {
                $query->where('bc.active_status', $status);
            }

            $recordsFiltered = (clone $query)->count();

            if ($length != -1) {
                $query->offset($start)->limit($length);
            }

            $rows = $query->orderBy('bc.id', 'desc')->get();

            // 🔥 GROUP BY BUS
            $grouped = [];

            foreach ($rows as $row) {

                $key = $row->bus_id . '_' . $row->bus_operator_id;

                if (!isset($grouped[$key])) {

                    $reasonText = ($row->reason == 77)
                        ? $row->other_reason
                        : $row->reason_name;

                    $grouped[$key] = [
                        'id' => $row->id,
                        'bus_schedule_id' => $row->id,
                        'enc_bus_schedule_id' => Crypt::encryptString($row->id),

                        'operator_name' => $row->operator_name ?? '--',

                        'bus_name' => trim(($row->bus_name ?? '') . ' / ' . ($row->bus_number ?? '')),

                        'reason' => $reasonText,

                        'dates' => [],

                        'created_date' => $row->created_at
                            ? date('d-M-Y H:i:s', strtotime($row->created_at))
                            : null,

                        'updated_date' => $row->updated_at
                            ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                            : null,

                        'created_by_name' => $row->created_by_name ?? '--',
                        'updated_by_name' => $row->updated_by_name ?? '--',

                        'is_active' => $row->active_status == 1 ? 'Active' : 'Inactive',

                        'enc_bustype_id' => Crypt::encryptString($row->bus_id),
                        'layout_name' => $row->bus_name ?? 'Bus'
                    ];
                }

                // 🔥 ADD DATES
                $grouped[$key]['dates'][] = date('d-M-Y', strtotime($row->cancelled_date));
            }

            // 🔥 FORMAT DATES
            foreach ($grouped as &$g) {
                $g['dates'] = implode('<br>', $g['dates']);
            }

            $data = array_values($grouped);
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

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            // =======================
            // 🔹 EDIT LOAD DATA
            // =======================
            if ($id > 0) {

                $data['strPage']   = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = DB::connection('mysql_dev')
                    ->table('bus_cancelled')
                    ->where('id', $id)
                    ->first();

                if (!$row) {
                    return redirect()->route('bus-cancel.index');
                }

                $selectedDates = DB::connection('mysql_dev')
                    ->table('bus_cancelled_date')
                    ->where('bus_cancelled_id', $id)
                    ->where('active_status', 1)
                    ->pluck('cancelled_date')
                    ->toArray();

                $data['row'] = $row;
                $data['selected_dates'] = $selectedDates;
            }

            // =======================
            // 🔹 FORM SUBMIT
            // =======================
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
                    'bus.required'      => 'Please select bus',
                    'year.required'     => 'Please select year',
                    'month.required'    => 'Please select month',
                    'reason.required'   => 'Please select reason',
                    'dates.required'    => 'Please select at least one date',
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

                $operator_id  = request('operator');
                $bus_id       = request('bus');
                $year         = request('year');
                $month        = request('month');
                $reason       = request('reason');
                $other_reason = request('other_reason');
                $dates        = request('dates');

                // =======================
                // 🔹 UPDATE
                // =======================
                if ($id > 0) {

                    DB::connection('mysql_dev')
                        ->table('bus_cancelled')
                        ->where('id', $id)
                        ->update([
                            'bus_operator_id' => $operator_id,
                            'bus_id'          => $bus_id,
                            'month'           => $month,
                            'year'            => $year,
                            'reason'          => $reason,
                            'other_reason'    => ($reason == 77) ? $other_reason : null,
                            'updated_at'      => now(),
                        ]);

                    // 🔹 OLD DATES
                    $existingDates = DB::connection('mysql_dev')
                        ->table('bus_cancelled_date')
                        ->where('bus_cancelled_id', $id)
                        ->pluck('cancelled_date')
                        ->toArray();

                    // 🔹 DEACTIVATE UNCHECKED
                    $toDeactivate = array_diff($existingDates, $dates);

                    if (!empty($toDeactivate)) {
                        DB::connection('mysql_dev')
                            ->table('bus_cancelled_date')
                            ->where('bus_cancelled_id', $id)
                            ->whereIn('cancelled_date', $toDeactivate)
                            ->update([
                                'active_status' => 0,
                                'updated_at' => now()
                            ]);
                    }

                    // 🔹 INSERT / ACTIVATE NEW
                    foreach ($dates as $date) {

                        $exists = DB::connection('mysql_dev')
                            ->table('bus_cancelled_date')
                            ->where('bus_cancelled_id', $id)
                            ->where('cancelled_date', $date)
                            ->first();

                        if ($exists) {
                            DB::connection('mysql_dev')
                                ->table('bus_cancelled_date')
                                ->where('id', $exists->id)
                                ->update([
                                    'active_status' => 1,
                                    'updated_at' => now()
                                ]);
                        } else {
                            DB::connection('mysql_dev')
                                ->table('bus_cancelled_date')
                                ->insert([
                                    'bus_cancelled_id' => $id,
                                    'cancelled_date'   => $date,
                                    'active_status'    => 1,
                                    'created_at'       => now(),
                                ]);
                        }
                    }
                }
                // =======================
                // 🔹 INSERT
                // =======================
                else {

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

                    $insertDates = [];

                    foreach ($dates as $date) {
                        $insertDates[] = [
                            'bus_cancelled_id' => $cancel_id,
                            'cancelled_date'   => $date,
                            'active_status'    => 1,
                            'created_at'       => now(),
                        ];
                    }

                    DB::connection('mysql_dev')
                        ->table('bus_cancelled_date')
                        ->insert($insertDates);
                }

                DB::commit();

                return redirect()->back()->withInput()->with([
                    'level' => 'success',
                    'message' => 'Bus Cancel ' . ($id ? 'updated' : 'created') . ' successfully'
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

    public function getScheduleDates(Request $request)
    {
        $bus_id = $request->bus_id;
        $schedule_id = $request->bus_schedule_id;

        $scheduleDates = [];
        $runningCycle = null;
        $lastDate = null;



        if (!empty($schedule_id)) {

            // get schedule
            $schedule = DB::table('odbusdev.bus_schedule')
                ->where('id', $schedule_id)
                ->first();

            if ($schedule) {
                $runningCycle = $schedule->running_cycle;
            }

            $scheduleDates = DB::table('odbusdev.bus_schedule_date')
                ->where('bus_schedule_id', $schedule_id)
                ->orderBy('entry_date', 'asc')
                ->limit(30)
                ->pluck('entry_date')
                ->toArray();

            $lastDate = DB::table('odbusdev.bus_schedule_date')
                ->where('bus_schedule_id', $schedule_id)
                ->orderByDesc('entry_date')
                ->value('entry_date');
        } elseif (!empty($bus_id)) {

            $schedule = DB::table('odbusdev.bus_schedule')
                ->where('bus_id', $bus_id)
                ->where('active_status', 1)
                ->orderByDesc('id')
                ->first();

            if ($schedule) {

                $runningCycle = $schedule->running_cycle;

                $scheduleDates = DB::table('odbusdev.bus_schedule_date')
                    ->where('bus_schedule_id', $schedule->id)
                    ->orderBy('entry_date', 'asc')
                    ->limit(30)
                    ->pluck('entry_date')
                    ->toArray();

                $lastDate = DB::table('odbusdev.bus_schedule_date')
                    ->where('bus_schedule_id', $schedule->id)
                    ->orderByDesc('entry_date')
                    ->value('entry_date');
            }
        }


        if (!empty($scheduleDates)) {

            $chunkSize = ceil(count($scheduleDates) / 3);
            $chunks = array_chunk($scheduleDates, $chunkSize);

            $html = '<div class="row">';

            foreach ($chunks as $chunk) {
                $html .= '<div class="col-4">';

                foreach ($chunk as $date) {
                    $html .= '<div class="date-tile text-center mb-2 p-2 border rounded">'
                        . \Carbon\Carbon::parse($date)->format('d-M-Y') .
                        '</div>';
                }

                $html .= '</div>';
            }

            $html .= '</div>';
        } else {
            $html = '<div class="text-center text-muted p-4">Bus is not scheduled</div>';
        }


        if (!empty($bus_id)) {
            return response()->json([
                'status' => !empty($scheduleDates),
                'html' => $html,
                'running_cycle' => $runningCycle,
                'last_date' => $lastDate
            ]);
        }

        return response($html);
    }
}
