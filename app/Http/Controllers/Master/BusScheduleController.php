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
use Mews\Purifier\Facades\Purifier;

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
            $runningCycle = request('runningCycle') !== null && request('runningCycle') !== '' ? (int)request('runningCycle') : null;
            $scheduleType = request('scheduleType') !== null && request('scheduleType') !== '' ? htmlEncode(request('scheduleType')) : null;


            $start  = request('start', 0);
            $length = request('length', 10);

            $query = DB::table('odbusdev.bus_schedule as bs')
                ->select(

                    'bs.id',
                    'bs.operator_id',
                    'bs.bus_id',
                    'bs.running_cycle',
                    'bs.schedule_type',

                    DB::raw('(
                            SELECT name
                            FROM odbusdev.bus
                            WHERE id = bs.bus_id
                            LIMIT 1
                        ) as bus_name'),

                    DB::raw('(
                            SELECT bus_number
                            FROM odbusdev.bus
                            WHERE id = bs.bus_id
                            LIMIT 1
                        ) as bus_number'),

                    DB::raw('(
                            SELECT organization_name
                            FROM odbusmaster.users
                            WHERE id = bs.operator_id
                            AND user_role = 9
                            LIMIT 1
                        ) as operator_name'),

                    DB::raw('(
                            SELECT name
                            FROM odbusmaster.users
                            WHERE id = bs.created_by
                            LIMIT 1
                        ) as created_by_name'),

                    DB::raw('(
                                        SELECT name
                                        FROM odbusmaster.users
                                        WHERE id = bs.updated_by
                                        LIMIT 1
                                    ) as updated_by_name'),

                    DB::raw('(
                            SELECT GROUP_CONCAT(
                                CASE bsd.day_number
                                    WHEN 1 THEN "Sun"
                                    WHEN 2 THEN "Mon"
                                    WHEN 3 THEN "Tue"
                                    WHEN 4 THEN "Wed"
                                    WHEN 5 THEN "Thu"
                                    WHEN 6 THEN "Fri"
                                    WHEN 7 THEN "Sat"
                                END
                                ORDER BY bsd.day_number
                                SEPARATOR ", "
                            )
                            FROM odbusdev.bus_schedule_days as bsd
                            WHERE bsd.bus_schedule_id = bs.id
                        ) as week_day_name'),
                    'bs.active_status',
                    'bs.created_at',
                    'bs.updated_at'
                );

            $recordsTotal = DB::table('odbusdev.bus_schedule')->count();

            if (!empty(trim($txtSearch))) {
                $search = trim($txtSearch);

                $query->where(function ($q) use ($search) {
                    $q->whereRaw("(SELECT name FROM odbusdev.bus WHERE id = bs.bus_id LIMIT 1) LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("(SELECT bus_number FROM odbusdev.bus WHERE id = bs.bus_id LIMIT 1) LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("(SELECT organization_name FROM odbusmaster.users WHERE id = bs.operator_id LIMIT 1) LIKE ?", ["%{$search}%"]);
                });
            }

            if (!empty($operator)) {
                $query->where('bs.operator_id', $operator);
            }

            if (!empty($bus)) {
                $query->where('bs.bus_id', $bus);
            }

            if (!empty($runningCycle)) {
                $query->where('bs.running_cycle', $runningCycle);
            }

            if (!empty($scheduleType)) {
                $query->where('bs.schedule_type', $scheduleType);
            }

            if ($status !== null && $status !== '') {
                $query->where('bs.active_status', $status);
            }

            $recordsFiltered = (clone $query)->count();

            if ($length != -1) {
                $query->offset($start)->limit($length);
            }

            $rows = $query->orderBy('bs.id', 'desc')->get();

            foreach ($rows as $row) {

                $data[] = [
                    'id' => $row->id,
                    'bus_schedule_id' => $row->id,
                    'enc_bus_schedule_id' => Crypt::encryptString($row->id),
                    'operator_name' => $row->operator_name ?? '--',
                    'bus_name' => trim(($row->bus_name ?? '') . ' - ( ' . ($row->bus_number ?? '') . ' )'),
                    'schedule_type' => $row->schedule_type ?? '--',
                    'week_day_name' => $row->week_day_name ?? '--',
                    'running_cycle' => $row->running_cycle ?? '--',
                    'created_date' => $row->created_at ? date('d-M-Y H:i:s', strtotime($row->created_at)) : null,
                    'updated_date' => $row->updated_at ? date('d-M-Y H:i:s', strtotime($row->updated_at)) : null,
                    'created_by_name' => $row->created_by_name ?? '--',
                    'updated_by_name' => $row->updated_by_name ?? '--',
                    'is_active' => $row->active_status == 1 ? 'Active' : 'Inactive',
                    'enc_bustype_id' => Crypt::encryptString($row->bus_id),
                    'layout_name' => $row->bus_name ?? 'Bus'
                ];
            }
        } catch (\Throwable $t) {

            Log::error("BusScheduleController Error", [
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

            $row = null;
            $scheduleDates = [];

            if ($id > 0) {

                $data['strPage']   = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = DB::table('odbusdev.bus_schedule')
                    ->where('id', $id)
                    ->first();

                if (!$row) {
                    return redirect("bus-schedule");
                }

                $data['row'] = $row;

                $lastDate = DB::table('odbusdev.bus_schedule_date')
                    ->where('bus_schedule_id', $id)
                    ->orderByDesc('entry_date')
                    ->value('entry_date');

                $data['lastDate'] = $lastDate;

                $scheduleDates = DB::table('odbusdev.bus_schedule_date')
                    ->where('bus_schedule_id', $id)
                    ->orderBy('entry_date', 'asc')
                    ->pluck('entry_date')
                    ->toArray();

                $bus_id = $row->bus_id;
            } else {

                $bus_id = request('bus') ?? old('bus');

                if ($bus_id) {

                    $schedule = DB::table('odbusdev.bus_schedule')
                        ->where('bus_id', $bus_id)
                        ->where('active_status', 1)
                        ->orderByDesc('id')
                        ->first();

                    if ($schedule) {

                        $scheduleDates = DB::table('odbusdev.bus_schedule_date')
                            ->where('bus_schedule_id', $schedule->id)
                            ->orderBy('entry_date', 'asc')
                            ->limit(30)
                            ->pluck('entry_date')
                            ->toArray();
                    }
                }
            }

            $data['scheduleDates'] = $scheduleDates;

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [

                    'operator'      => 'required|integer',
                    'bus'           => 'required|integer',
                    'schedule_type' => 'required',
                    'from_date'     => 'required|date',

                ]);

                if ($validator->fails()) {

                    return back()
                        ->withErrors($validator)
                        ->withInput();
                }

                DB::beginTransaction();

                $operator_id = (int) request('operator');
                $bus_id = (int) request('bus');
                $schedule_type = htmlEncode(Purifier::clean(request('schedule_type')));
                $from_date = htmlEncode(Purifier::clean(request('from_date')));
                $running_cycle = (int) request('running_cycle');
                $week_days = array_map(function ($day) {
                    return (int) Purifier::clean($day);
                }, request('week_days') ?? []);
                $custom_dates = htmlEncode(Purifier::clean(request('custom_dates')));

                if (
                    $schedule_type == 'Daily' &&
                    empty($running_cycle)
                ) {

                    return back()->withInput()->with([
                        'level' => 'danger',
                        'message' => 'Running cycle is required'
                    ]);
                }

                if (
                    $schedule_type == 'Weekly' &&
                    empty($week_days)
                ) {

                    return back()->withInput()->with([
                        'level' => 'danger',
                        'message' => 'Please select weekly days'
                    ]);
                }

                if (
                    $schedule_type == 'custom' &&
                    empty($custom_dates)
                ) {

                    return back()->withInput()->with([
                        'level' => 'danger',
                        'message' => 'Please select custom dates'
                    ]);
                }
                $duplicateQuery = DB::table('odbusdev.bus_schedule')

                    ->where('bus_id', $bus_id)
                    ->where('active_status', 1);

                if ($id > 0) {

                    $duplicateQuery->where('id', '!=', $id);
                }

                if ($duplicateQuery->exists()) {

                    DB::rollBack();
                    return back()->withInput()->with([

                        'level' => 'danger',
                        'message' => 'This bus already has a schedule.'

                    ]);
                }

                $scheduleData = [

                    'operator_id'   => $operator_id,
                    'bus_id'        => $bus_id,
                    'schedule_type' => $schedule_type,
                    'start_date'    => $from_date,
                    'running_cycle' => ($schedule_type == 'Daily') ? $running_cycle : null,
                    'active_status' => 1,
                ];

                if ($id > 0) {

                    $oldData = DB::table('odbusdev.bus_schedule')
                        ->where('id', $id)
                        ->first();

                    $newData = [

                        'operator_id'   => $operator_id,
                        'bus_id'        => $bus_id,
                        'schedule_type' => $schedule_type,
                        'start_date'    => $from_date,
                        'running_cycle' => ($schedule_type == 'Daily')
                            ? $running_cycle
                            : null,
                        'active_status' => 1,
                    ];

                    $oldChanged = [];
                    $newChanged = [];

                    foreach ($newData as $key => $value) {

                        $oldValue = $oldData->$key ?? null;

                        if (trim((string)$oldValue) !== trim((string)$value)) {

                            $oldChanged[$key] = $oldValue;
                            $newChanged[$key] = $value;
                        }
                    }

                    if (!empty($newChanged)) {
                        app(CommonController::class)->auditLog(
                            'bus_schedule',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged

                        );
                    }

                    DB::table('odbusdev.bus_schedule')
                        ->where('id', $id)
                        ->update([

                            ...$newData,
                            'updated_by' => 1,
                            'updated_at' => now()

                        ]);

                    DB::table('odbusdev.bus_schedule_date')
                        ->where('bus_schedule_id', $id)
                        ->delete();

                    DB::table('odbusdev.bus_schedule_days')
                        ->where('bus_schedule_id', $id)
                        ->delete();

                    $schedule_id = $id;
                } else {

                    app(CommonController::class)->auditLog(

                        'bus_schedule',
                        null,
                        'INSERT',
                        [],
                        [
                            ...$scheduleData,
                            'created_by' => 1,
                            'created_at' => now()

                        ]

                    );

                    $schedule_id = DB::table('odbusdev.bus_schedule')

                        ->insertGetId([
                            ...$scheduleData,
                            'created_by' => 1,
                            'created_at' => now()
                        ]);
                }

                $dates = [];
                if ($schedule_type == 'Daily') {

                    $current = \Carbon\Carbon::parse($from_date);
                    for ($i = 0; $i < 30; $i++) {
                        $dates[] = [

                            'bus_schedule_id' => $schedule_id,
                            'entry_date' => $current->format('Y-m-d'),
                            'created_by' => 1,
                            'created_at' => now()
                        ];

                        $current->addDays($running_cycle);
                    }
                }

                if ($schedule_type == 'Weekly') {

                    $dates = $this->prepareWeeklyBusScheduleDates(
                        $schedule_id,
                        $week_days,
                        $from_date
                    );

                    $dayInsert = [];

                    foreach ($week_days as $day) {

                        $dayInsert[] = [

                            'bus_schedule_id' => $schedule_id,
                            'day_number' => $day

                        ];
                    }

                    if (!empty($dayInsert)) {

                        DB::table('odbusdev.bus_schedule_days')
                            ->insert($dayInsert);
                    }
                }

                if ($schedule_type == 'custom') {

                    $customDateArray = explode(',', $custom_dates);
                    foreach ($customDateArray as $date) {

                        $date = htmlEncode(
                            Purifier::clean(
                                trim($date)
                            )
                        );
                        if (!empty($date)) {

                            $dates[] = [

                                'bus_schedule_id' => $schedule_id,
                                'entry_date' => $date,
                                'created_by' => 1,
                                'created_at' => now()

                            ];
                        }
                    }
                }



                if (!empty($dates)) {
                    DB::table('odbusdev.bus_schedule_date')
                        ->insert($dates);
                }

                DB::table('odbusdev.bus')
                    ->where('id', $bus_id)
                    ->update([
                        'running_cycle' => ($schedule_type == 'Daily') ? $running_cycle : null,
                        'updated_at' => now()

                    ]);

                DB::commit();

                return redirect()->back()
                    ->withInput()
                    ->with([

                        'level' => 'success',
                        'message' => 'Bus Schedule ' .
                            ($id ? 'updated' : 'created') .
                            ' successfully'

                    ]);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("BusScheduleController Error", [
                'method' => $data['strPage'],
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addBusSchedule', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function prepareWeeklyBusScheduleDates($schedule_id, $week_days, $from_date)
    {

        $dates = [];
        $today = \Carbon\Carbon::parse($from_date);
        $selectedDays = collect($week_days)

            ->map(function ($day) {
                return (int) $day;
            })

            ->sort()
            ->values()
            ->toArray();

        $count = 0;

        while ($count < 30) {

            foreach ($selectedDays as $day) {

                $currentDay = $today->copy();
                $carbonDay = ($day == 7) ? 6 : ($day - 1);

                if ($currentDay->dayOfWeek == $carbonDay) {
                } elseif ($currentDay->dayOfWeek < $carbonDay) {

                    $currentDay->next($carbonDay);
                } else {

                    $currentDay->next($carbonDay);
                }

                $formattedDate = $currentDay->format('Y-m-d');
                $alreadyExists = collect($dates)->contains(function ($item) use ($formattedDate) {
                    return $item['entry_date']
                        == $formattedDate;
                });

                if (!$alreadyExists) {

                    $dates[] = [
                        'bus_schedule_id' => $schedule_id,
                        'entry_date' => $formattedDate,
                        'created_by' => 1,
                        'created_at' => now()

                    ];

                    $count++;
                    if ($count >= 30) {
                        break;
                    }
                }
            }

            $today->addWeek();
        }

        usort($dates, function ($a, $b) {

            return strtotime($a['entry_date'])
                - strtotime($b['entry_date']);
        });

        return $dates;
    }

    public function getScheduleDates(Request $request)
    {
        try {

            $bus_id = $request->bus_id;
            $schedule_id = $request->bus_schedule_id;

            $scheduleDates = [];
            $runningCycle = null;
            $lastDate = null;
            $busName = '';
            $busNumber = '';

            $today = \Carbon\Carbon::today();

            if (!empty($schedule_id)) {

                $schedule = DB::table('odbusdev.bus_schedule')
                    ->where('id', $schedule_id)
                    ->first();

                if ($schedule) {

                    $runningCycle = $schedule->running_cycle;

                    $bus = DB::table('odbusdev.bus')
                        ->where('id', $schedule->bus_id)
                        ->first();

                    if ($bus) {
                        $busName = ($bus->name ?? '');
                        $busNumber = ($bus->bus_number ?? '');
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
                }
            } elseif (!empty($bus_id)) {

                $schedule = DB::table('odbusdev.bus_schedule')
                    ->where('bus_id', $bus_id)
                    ->where('active_status', 1)
                    ->orderByDesc('id')
                    ->first();

                if ($schedule) {

                    $runningCycle = $schedule->running_cycle;

                    $bus = DB::table('odbusdev.bus')
                        ->where('id', $schedule->bus_id)
                        ->first();

                    if ($bus) {
                        $busName = ($bus->name ?? '');
                        $busNumber = ($bus->bus_number ?? '');
                    }

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

            $html = '
                <div id="modalBusTitle" style="display:none;"> - (' . $busNumber . ')</div>';

            if (!empty($scheduleDates)) {

                $columns = 3;
                $chunkSize = ceil(count($scheduleDates) / $columns);
                $chunks = array_chunk($scheduleDates, $chunkSize);

                $html .= '<div class="row">';

                foreach ($chunks as $chunk) {

                    $html .= '<div class="col-md-4">';

                    foreach ($chunk as $date) {

                        if (empty($date)) {
                            continue;
                        }

                        $dateObj = \Carbon\Carbon::parse($date);

                        $style = '';

                        if ($dateObj->lt($today)) {
                            $style = 'color:#6c757d;
                          text-decoration:line-through;
                          text-decoration-color:red;
                          text-decoration-thickness:2px;';
                        }

                        $html .= '
            <div class="date-box text-center mb-2 p-2 border rounded bg-light" style="' . $style . '">
                ' . $dateObj->format('d-M-Y') . '
            </div>';
                    }

                    $html .= '</div>';
                }

                $html .= '</div>';
            } else {

                $html .= '
                    <div class="text-center text-muted p-4">
                        Bus is not scheduled
                    </div>';
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
        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }
}
