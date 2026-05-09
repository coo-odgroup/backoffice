<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;

class NotificationRuleController extends Controller
{
    public function index()
    {
        return view('Master.viewNotificationRules');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));

            $type = request('type') !== null && request('type') !== ''
                ? request('type')
                : null;

            $scheduler = request('scheduler') !== null && request('scheduler') !== ''
                ? request('scheduler')
                : null;

            $execution = request('execution') !== null && request('execution') !== ''
                ? request('execution')
                : null;

            $status = request('selStatus') !== null && request('selStatus') !== ''
                ? (int) request('selStatus')
                : null;

            $start  = request('start', 0);
            $length = request('length', 10);

            $query = DB::table('odbusmaster.mst_cron_jobs as cj')
                ->select(

                    'cj.id',
                    'cj.name',
                    'cj.slug',
                    'cj.type',
                    'cj.schedule_type',

                    // TYPE NAME (SCHEDULER_TYPE)
                    DB::raw('(
                            SELECT annexture_name
                            FROM odbusmaster.mst_annexture
                            WHERE annexture_type_id = 20
                            AND annexture_value = cj.type
                            LIMIT 1
                        ) as type_name'),

                    // SCHEDULER NAME (SCHEDULER_TYPE)
                    DB::raw('(
                            SELECT annexture_name
                            FROM odbusmaster.mst_annexture
                            WHERE annexture_type_id = 20
                            AND annexture_value = cj.schedule_type
                            LIMIT 1
                        ) as scheduler_name'),
                    'cj.interval_minutes',
                    'cj.run_times_json',
                    'cj.cron_expression',
                    'cj.execution_type',
                    'cj.job_class',
                    'cj.command_name',
                    'cj.active_status',
                    'cj.created_at',
                    'cj.updated_at',

                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = cj.created_by LIMIT 1) as created_by_name'),

                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = cj.updated_by LIMIT 1) as updated_by_name')
                );

            $recordsTotal = DB::table('odbusmaster.mst_cron_jobs')->count();

            // SEARCH
            if (!empty(trim($txtSearch))) {

                $search = trim($txtSearch);

                $query->where(function ($q) use ($search) {

                    $q->where('cj.name', 'like', "%{$search}%")
                        ->orWhere('cj.slug', 'like', "%{$search}%")
                        ->orWhere('cj.type', 'like', "%{$search}%")
                        ->orWhere('cj.schedule_type', 'like', "%{$search}%")
                        ->orWhere('cj.execution_type', 'like', "%{$search}%")
                        ->orWhere('cj.job_class', 'like', "%{$search}%")
                        ->orWhere('cj.command_name', 'like', "%{$search}%")
                        ->orWhere('cj.cron_expression', 'like', "%{$search}%");
                });
            }

            // FILTERS
            if (!empty($type)) {
                $query->where('cj.type', $type);
            }

            if (!empty($scheduler)) {
                $query->where('cj.schedule_type', $scheduler);
            }

            if (!empty($execution)) {
                $query->where('cj.execution_type', $execution);
            }

            if ($status !== null && $status !== '') {
                $query->where('cj.active_status', $status);
            }

            $recordsFiltered = (clone $query)->count();

            // PAGINATION
            if ($length != -1) {

                $query->offset($start)
                    ->limit($length);
            }

            $rows = $query->orderBy('cj.id', 'desc')->get();

            foreach ($rows as $row) {

                $data[] = [

                    'id' => $row->id,
                    'enc_id' => Crypt::encryptString($row->id),
                    'cron_name' => $row->name ?? '--',
                    'cron_type' => $row->type ?? '--',
                    'scheduler_type' => $row->scheduler_name ?? '--',
                    'execution_type' => $row->execution_type ?? '--',
                    'interval_minutes' => $row->interval_minutes ?? '--',
                    'run_times_json' => !empty($row->run_times_json)
                        ? implode(
                            ',',
                            json_decode($row->run_times_json, true) ?? []
                        )
                        : '--',
                    'cron_expression' => $row->cron_expression ?? '--',
                    'job_class' => $row->job_class ?? '--',
                    'command_name' => $row->command_name ?? '--',
                    'created_date' => $row->created_at
                        ? date('d-M-Y H:i:s', strtotime($row->created_at))
                        : null,
                    'updated_date' => $row->updated_at
                        ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                        : null,
                    'created_by_name' => $row->created_by_name ?? '--',

                    'updated_by_name' => $row->updated_by_name ?? '--',

                    'is_active' => $row->active_status == 1
                        ? 'Active'
                        : 'Inactive',
                ];
            }
        } catch (\Throwable $t) {

            Log::error("CronJobController Error", [
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

            $id = (!empty($encId))
                ? Crypt::decryptString($encId)
                : 0;

            $row = null;

            $redirectPage =
                route('notification-rules.index');

            // EDIT MODE
            if ($id > 0) {

                $data['strPage']   = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = DB::table(
                    'odbusmaster.cron_job_notifications'
                )
                    ->where('id', $id)
                    ->first();

                if (!$row) {

                    return redirect()
                        ->route('notification-rules.index');
                }

                $data['row'] = $row;
            }

            // SAVE
            if (request()->isMethod('post')) {

                $validator = Validator::make(
                    request()->all(),
                    [

                        'cron_name'       => 'required',

                        'channel'         => 'required',

                        'template'        => 'required',

                        'recipient'       => 'required',

                        'status_condition' => 'required',
                    ]
                );

                if ($validator->fails()) {

                    return back()
                        ->withErrors($validator)
                        ->withInput();
                }

                DB::beginTransaction();

                // CHANNEL MAP
                $channelMap = [

                    'Email' => 1,

                    'SMS' => 2,

                    'Push Notification' => 3,

                    'Whatsapp' => 4,
                ];

                $recipientType = request('recipient');

                $roleType = null;

                $recipientValue = null;

                if (
                    str_contains(
                        strtolower($recipientType),
                        'manual'
                    )
                ) {

                    $recipientValue =
                        request('manual_recipient');
                }

                else if (
                    str_contains(
                        strtolower($recipientType),
                        'dynamic'
                    )
                ) {

                    $recipientValue =
                        request('dynamic_variable');
                }

                else if (
                    str_contains(
                        strtolower($recipientType),
                        'role'
                    )
                ) {

                    $roleType = request('roles');

                    $recipientValue =
                        !empty(request('selected_users'))
                        ?
                        json_encode(
                            request('selected_users')
                        )
                        :
                        null;
                }

                $recipientTypeValue = DB::table(
                    'odbusmaster.mst_annexture'
                )
                    ->where('annexture_name', $recipientType)
                    ->where('annexture_type_id', 23)
                    ->value('annexture_value');

                $insertData = [

                    'cron_job_id' =>
                    request('cron_name'),

                    'channel' =>
                    $channelMap[request('channel')] ?? null,
                    'reciptent_type' =>
                    $recipientTypeValue,

                    'recipient_value' =>
                    $recipientValue,

                    'template_id' =>
                    request('template'),

                    'role_type' =>
                    $roleType,

                    'status_condition' =>
                    request('status_condition'),

                    'active_status' => 1,
                ];

                if ($id > 0) {

                    $oldData = DB::table(
                        'odbusmaster.cron_job_notifications'
                    )
                        ->where('id', $id)
                        ->first();

                    $updateData = [

                        ...$insertData,

                        'updated_by' => 1,

                        'updated_at' => now(),
                    ];

                    DB::table(
                        'odbusmaster.cron_job_notifications'
                    )
                        ->where('id', $id)
                        ->update($updateData);

                    app(CommonController::class)
                        ->auditLog(

                            'cron_job_notifications',

                            $id,

                            'UPDATE',

                            (array)$oldData,

                            $updateData
                        );
                } else {

                    $rowData = [

                        ...$insertData,

                        'created_by' => 1,

                        'created_at' => now(),
                    ];

                    $insertId = DB::table(
                        'odbusmaster.cron_job_notifications'
                    )
                        ->insertGetId($rowData);
                    app(CommonController::class)
                        ->auditLog(

                            'cron_job_notifications',

                            $insertId,

                            'INSERT',

                            [],

                            $rowData
                        );
                }

                DB::commit();

                return redirect()
                    ->route('notification-rules.index')
                    ->with([

                        'level' => 'success',

                        'message' =>
                        'Notification Rule ' .
                            ($id ? 'updated' : 'created') .
                            ' successfully'
                    ]);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error(
                "NotificationRuleController Error",
                [

                    'method' =>
                    $data['strPage'],

                    'error' =>
                    $t->getMessage()
                ]
            );

            return back()
                ->with([

                    'level' => 'danger',

                    'message' =>
                    config(
                        'constants.SERVER_ERROR_MESSAGE'
                    )
                ])
                ->withInput();
        }

        return view(
            'Master.addNotificationRules',
            compact('data')
        );
    }

    public function edit($encId)
    {
        return $this->add($encId);
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

            return response('
            <div class="text-danger text-center p-4">
                Failed to load schedule
            </div>');
        }
    }


    public function getCronJobDetails(Request $request)
    {
        try {

            $id = $request->id;

            $row = DB::table('odbusmaster.mst_cron_jobs')
                ->select(
                    'id',
                    'name',
                    'type',
                    'schedule_type',
                    'interval_minutes',
                    'run_times_json',
                    'execution_type',
                    'job_class',
                    'command_name',
                    'active_status'
                )
                ->where('id', $id)
                ->first();

            if (!$row) {

                return response()->json([
                    'status' => false,
                    'data'   => null
                ]);
            }

            return response()->json([
                'status' => true,
                'data'   => $row
            ]);
        } catch (\Throwable $t) {

            return response()->json([
                'status' => false,
                'data'   => null
            ]);
        }
    }

    public function getNotificationDetails(Request $request)
    {
        try {

            $id = $request->id;

            $template = DB::table('odbusmaster.mst_notification_templates')
                ->where('id', $id)
                ->first();

            if (!$template) {
                return response('<div class="text-danger text-center p-4">Data not found</div>');
            }

            $type = (int) $template->type;

            /*
        1 = Email
        2 = SMS
        3 = Push
        4 = WhatsApp
        */


            $typeMap = [
                1 => 'Email',
                2 => 'SMS',
                3 => 'Push Notification',
                4 => 'WhatsApp'
            ];

            $html = '
            <div class="container-fluid">

                <div class="text-center mb-4">
                    <span class="badge bg-primary px-3 py-2 fs-6">
                        ' . ($typeMap[$type] ?? 'Unknown') . '
                    </span>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
            ';


            // ================= EMAIL =================
            if ($type === 1) {

                $html .= '

                    <div class="text-center mb-3">
                        <span class="text-muted small fw-semibold">' . ($template->name ?? 'Email Notification') . '</span>
                    </div>

                    <div style="
                        background: linear-gradient(135deg, #f5f7fb, #eef1f7);
                        padding:20px;
                        border-radius:12px;
                    ">

                        <div class="d-flex justify-content-center">

                            <div style="
                                background:#e4e6eb;
                                padding:12px 14px;
                                border-radius:18px;
                                font-size:14px;
                                max-width:85%;
                                color:#222;
                                text-align:left;
                            ">

                                <!-- Subject -->
                                <div style="font-weight:600; margin-bottom:6px;">
                                    ' . ($template->subject ?? '--') . '
                                </div>

                                <!-- Divider -->
                                <div style="
                                    height:1px;
                                    background:#ccc;
                                    margin:6px 0 8px 0;
                                "></div>

                                <!-- Body -->
                                <div style="color:#333; line-height:1.5;">
                                    ' . nl2br($template->body ?? '--') . '
                                </div>

                            </div>

                        </div>

                    </div>

                ';
            }

            // ================= SMS =================
            elseif ($type === 2) {

                $html .= '

                    <div class="text-center mb-3">
                        <span class="text-muted small fw-semibold">' . ($template->name ?? 'SMS Notification') . '</span>
                    </div>

                    <div style="
                        background: linear-gradient(135deg, #f5f7fb, #eef1f7);
                        padding:20px;
                        border-radius:12px;
                    ">

                        <div class="d-flex justify-content-center">

                            

                                <!-- SMS Bubble -->
                                
                                    <div style="
                                        background:#e4e6eb;
                                        padding:10px 14px;
                                        border-radius:18px;
                                        font-size:14px;
                                        max-width:85%;
                                        color:#222;
                                    ">
                                        ' . nl2br($template->body ?? '--') . '
                                    </div>
                                </div>

                                <!-- Timestamp -->
                                <div style="
                                    font-size:11px;
                                    color:#999;
                                    margin-top:6px;
                                    text-align:right;
                                ">
                                </div>

                            </div>

                        </div>

                    </div>

                ';
            }

            // ================= PUSH =================
            elseif ($type === 3) {

                $html .= '

                        <div class="text-center mb-3">
                            <span class="text-muted small fw-semibold">' . ($template->name ?? 'Notification') . '</span>
                        </div>

                        <div style="
                            background: linear-gradient(135deg, #f5f7fb, #eef1f7);
                            padding:20px;
                            border-radius:12px;
                        ">

                            <div class="d-flex justify-content-center">
                                <div style="
                                    width:320px;
                                    background:#ffffff;
                                    border-radius:14px;
                                    padding:14px;
                                    box-shadow:0 6px 18px rgba(0,0,0,0.15);
                                    border:1px solid #e6e6e6;
                                ">

                                    <!-- Header -->
                                    <div style="display:flex; align-items:center; margin-bottom:10px;">
                                        
                                        <div style="
                                            width:36px;
                                            height:36px;
                                            border-radius:50%;
                                            background:linear-gradient(135deg,#0d6efd,#4da3ff);
                                            color:#fff;
                                            display:flex;
                                            align-items:center;
                                            justify-content:center;
                                            font-size:16px;
                                            margin-right:10px;
                                            box-shadow:0 2px 6px rgba(13,110,253,0.4);
                                        ">
                                            🔔
                                        </div>

                                

                                    </div>

                                    <!-- Title -->
                                    <div style="font-size:15px; font-weight:600; color:#111;">
                                        ' . ($template->title ?? '--') . '
                                    </div>

                                    <!-- Body -->
                                    <div style="font-size:13px; color:#555; margin-top:4px;">
                                        ' . nl2br($template->body ?? '--') . '
                                    </div>

                                </div>
                            </div>

                        </div>

                    ';
            }


            // ================= WHATSAPP =================
            elseif ($type === 4) {

                $html .= '

                    <h6 class="text-muted mb-3">' . ($template->name ?? 'Notification') . '</h6>

                    <div class="d-flex justify-content-center">
                        <div style="
                            max-width:280px;
                            background:#dcf8c6;
                            padding:12px 15px;
                            border-radius:12px;
                            text-align:left;
                            font-size:14px;
                            box-shadow:0 1px 3px rgba(0,0,0,0.1);
                        ">
                            ' . nl2br($template->body ?? '--') . '
                        </div>
                    </div>

                ';
            }


            // CLOSE
            $html .= '
                    </div>
                </div>
            </div>';

            return response($html);
        } catch (\Exception $e) {

            return response('
            <div class="text-danger text-center p-4">
                Failed to load notification details
            </div>');
        }
    }
}
