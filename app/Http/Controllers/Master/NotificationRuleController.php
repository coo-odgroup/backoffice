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

            $draw = request('draw');

            $txtSearch = request('txtSearch');
            $status    = request('selStatus');
            $channel = request('channel');
            $recipient = request('recipient');
            $condition = request('condition');

            $start  = request('start', 0);
            $length = request('length', 10);


            $baseQuery = DB::table('odbusmaster.cron_job_notifications as cjn')
                ->leftJoin('odbusmaster.mst_cron_jobs as cj', 'cj.id', '=', 'cjn.cron_job_id')

                ->leftJoin('odbusmaster.mst_notification_templates as mt', 'mt.id', '=', 'cjn.template_id')
                ->leftJoin('odbusmaster.mst_roles as mr', 'mr.id', '=', 'cjn.role_type');

            if (!empty($txtSearch)) {

                $baseQuery->where(function ($q) use ($txtSearch) {

                    $q->where('cj.name', 'like', "%{$txtSearch}%")
                        ->orWhere('cjn.recipient_value', 'like', "%{$txtSearch}%")
                        ->orWhere('mt.name', 'like', "%{$txtSearch}%")
                        ->orWhere('mr.name', 'like', "%{$txtSearch}%")
                        ->orWhere('cjn.status_condition', 'like', "%{$txtSearch}%");
                });
            }

            if ($status !== null && $status !== '') {
                $baseQuery->where('cjn.active_status', (int)$status);
            }

            if (!empty($channel)) {
                $baseQuery->where('cjn.channel', $channel);
            }

            if (!empty($recipient)) {
                $baseQuery->where('cjn.reciptent_type', $recipient);
            }

            if (!empty($condition)) {
                $baseQuery->where('cjn.status_condition', $condition);
            }


            $recordsTotal = DB::table('odbusmaster.cron_job_notifications')->count();
            $countQuery = DB::table('odbusmaster.cron_job_notifications as cjn')

                ->leftJoin(
                    'odbusmaster.mst_cron_jobs as cj',
                    'cj.id',
                    '=',
                    'cjn.cron_job_id'
                )

                ->leftJoin(
                    'odbusmaster.mst_notification_templates as mt',
                    'mt.id',
                    '=',
                    'cjn.template_id'
                )

                ->leftJoin(
                    'odbusmaster.mst_roles as mr',
                    'mr.id',
                    '=',
                    'cjn.role_type'
                );
            if (!empty($txtSearch)) {

                $countQuery->where(function ($q) use ($txtSearch) {

                    $q->where('cj.name', 'like', "%{$txtSearch}%")
                        ->orWhere('cjn.recipient_value', 'like', "%{$txtSearch}%")
                        ->orWhere('mt.name', 'like', "%{$txtSearch}%")
                        ->orWhere('mr.name', 'like', "%{$txtSearch}%")
                        ->orWhere('cjn.status_condition', 'like', "%{$txtSearch}%");
                });
            }

            if ($status !== null && $status !== '') {
                $countQuery->where('cjn.active_status', (int)$status);
            }

            $recordsFiltered = $countQuery->count();
            $query = $baseQuery->select(
                'cjn.id',
                'cjn.channel',
                'cjn.reciptent_type',
                'cjn.recipient_value',
                'cjn.template_id',
                'mt.name as template_name',
                'mr.name as role_name',
                'cjn.role_type',
                'cjn.status_condition',
                'cjn.active_status',
                'cjn.created_at',
                'cj.name as cron_name'
            );

            if ($length != -1) {
                $query->offset($start)->limit($length);
            }

            $rows = $query->orderBy('cjn.id', 'desc')->get();

            foreach ($rows as $row) {

                $data[] = [

                    'id' => $row->id,
                    'enc_id' => Crypt::encryptString($row->id),

                    'channel' => match ((int)$row->channel) {
                        1 => 'Email',
                        2 => 'SMS',
                        3 => 'Push Notification',
                        4 => 'WhatsApp',
                        default => '--'
                    },
                    'recipient_type' => match ((int)$row->reciptent_type) {
                        1 => 'Manual',
                        2 => 'Role Based',
                        3 => 'Dynamic Variable',
                        default => '--'
                    },

                    'recipient_value' => $row->recipient_value ?? '--',
                    'template_id'     => $row->template_name ?? '--',
                    'role_type'       => $row->role_name ?? '--',
                    'status_condition' => $row->status_condition ?? '--',

                    'created_date' => $row->created_at
                        ? date('d-M-Y H:i:s', strtotime($row->created_at))
                        : '--',

                    'created_by_name' => '--',
                    'updated_by_name' => '--',
                    'updated_date'    => '--',

                    'is_active' => $row->active_status == 1 ? 'Active' : 'Inactive',

                    'cron_name' => $row->cron_name ?? '--'
                ];
            }
        } catch (\Throwable $t) {

            Log::error("NotificationRuleController Error", [
                'message' => $t->getMessage()
            ]);

            return response()->json([
                'draw' => intval(request('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        return response()->json([
            'draw'            => intval($draw),
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
                } else if (
                    str_contains(
                        strtolower($recipientType),
                        'dynamic'
                    )
                ) {

                    $recipientValue =
                        request('dynamic_variable');
                } else if (
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

    public function getCronNotificationRules(Request $request)
    {
        try {

            $cronId = $request->cron_id;

            $rows = DB::table('odbusmaster.cron_job_notifications as cjn')

                ->leftJoin(
                    'odbusmaster.mst_notification_templates as mt',
                    'mt.id',
                    '=',
                    'cjn.template_id'
                )

                ->leftJoin(
                    'odbusmaster.mst_roles as mr',
                    'mr.id',
                    '=',
                    'cjn.role_type'
                )

                ->where(
                    'cjn.cron_job_id',
                    $cronId
                )

                ->select(
                    'cjn.*',
                    'mt.name as template_name',
                    'mr.name as role_name'
                )

                ->orderBy(
                    'cjn.id',
                    'desc'
                )

                ->get();

            $html = '';

            if ($rows->count() > 0) {

                $sl = 1;

                foreach ($rows as $row) {

                    // CHANNEL
                    $channel = match ((int)$row->channel) {

                        1 => 'Email',
                        2 => 'SMS',
                        3 => 'Push Notification',
                        4 => 'WhatsApp',

                        default => '--'
                    };
                    $channelBadge = 'primary';

                    if ($channel == 'SMS') {

                        $channelBadge = 'success';
                    } elseif (
                        $channel == 'WhatsApp'
                    ) {

                        $channelBadge = 'dark';
                    } elseif (
                        $channel ==
                        'Push Notification'
                    ) {

                        $channelBadge =
                            'warning text-dark';
                    }

                    $recipientType = match ((int)$row->reciptent_type) {

                        1 => 'Manual',
                        2 => 'Role Based',
                        3 => 'Dynamic Variable',

                        default => '--'
                    };

                    $recipientValue =
                        $row->recipient_value;

                    if (
                        $recipientType ==
                        'Role Based'
                    ) {

                        $recipientValue =
                            $row->role_name ?? '--';
                    }

                    $statusBadge =
                        $row->active_status == 1
                        ?
                        'success'
                        :
                        'danger';

                    $statusText =
                        $row->active_status == 1
                        ?
                        'Active'
                        :
                        'Inactive';
                    $html .= '
                <tr>
                    <td>' . $sl++ . '</td>
                    <td>
                        <span class="badge bg-' .
                        $channelBadge . '">
                            ' . $channel . '
                        </span>
                    </td>
                    <td>
                        ' .
                        ($row->template_name ?? '--')
                        . '
                    </td>
                    <td>
                        ' .
                        ($row->status_condition ?? '--')
                        . '
                    </td>
                    <td>
                        ' .
                        $recipientType . '
                    </td>
                    <td>
                        ' .
                        ($recipientValue ?? '--')
                        . '

                    </td>
                    <td>
                        <span class="badge bg-' .
                        $statusBadge . '">

                            ' . $statusText . '
                        </span>
                    </td>
                </tr>';
                }
            } else {
                $html = '
            <tr>
                <td colspan="8"
                    class="text-center text-muted py-4">
                    No Notification Rules Found
                </td>
            </tr>';
            }

            return response()->json([
                'status' => true,
                'html' => $html
            ]);
        } catch (\Throwable $t) {

            return response()->json([
                'status' => false,
                'html' => '
            <tr>
                <td colspan="8"
                    class="text-center text-danger py-4">
                    Failed To Load Rules
                </td>
            </tr>'
            ]);
        }
    }


    public function getCronSummary(Request $request)
    {
        try {

            $id = $request->cron_id;

            $row = DB::table('odbusmaster.mst_cron_jobs')
                ->where('id', $id)
                ->first();

            if (!$row) {

                return response()->json([
                    'status' => false
                ]);
            }

            // SCHEDULE TYPE
            $scheduleType = match ((int)$row->schedule_type) {

                1 => 'Interval',
                2 => 'Daily',
                3 => 'Specific Time',
                4 => 'Custom Cron',

                default => '--'
            };

            // CRON TYPE
            $cronType = $row->type ?? '--';

            // SCHEDULER
            $scheduler = '--';

            if ($row->schedule_type == 1) {

                $scheduler =
                    'Every ' .
                    $row->interval_minutes .
                    ' Minutes';
            } elseif (
                in_array(
                    $row->schedule_type,
                    [2, 3]
                )
            ) {

                $times = [];

                if (!empty($row->run_times_json)) {

                    $times = json_decode(
                        $row->run_times_json,
                        true
                    );
                }

                $scheduler =
                    !empty($times)
                    ?
                    implode(', ', $times)
                    :
                    '--';
            } elseif ($row->schedule_type == 4) {

                $scheduler =
                    $row->cron_expression ?? '--';
            }

            return response()->json([

                'status' => true,

                'data' => [

                    'name' =>
                    $row->name ?? '--',

                    'cron_type' =>
                    $cronType,

                    'execution_type' =>
                    $row->execution_type ?? '--',

                    'schedule_type' =>
                    $scheduleType,

                    'scheduler' =>
                    $scheduler,

                    'active_status' =>
                    $row->active_status
                ]
            ]);
        } catch (\Throwable $t) {

            return response()->json([
                'status' => false
            ]);
        }
    }
}
