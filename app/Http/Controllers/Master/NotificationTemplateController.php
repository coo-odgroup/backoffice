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

class NotificationTemplateController extends Controller
{
    public function index()
    {
        return view('Master.viewNotificationTemplate');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $status    =  (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $type      = (int) request('type');
            $category  = (int) request('category');
            $trigger   = (int) request('trigger');

            $start  = request('start', 0);
            $length = request('length', 10);

            $query = DB::table('odbusmaster.mst_notification_templates as nt')
                ->select(
                    'nt.id',
                    'nt.name',
                    'nt.type',
                    'nt.category',
                    'nt.event_trigger',
                    'nt.active_status',
                    'nt.created_at',
                    'nt.updated_at',

                    // TYPE (annexture_type_id = 19)
                    DB::raw('(
                        SELECT annexture_name
                        FROM odbusmaster.mst_annexture
                        WHERE annexture_type_id = 19
                        AND annexture_value = nt.type
                        LIMIT 1
                    ) as type_name'),

                    // CATEGORY (annexture_type_id = 21)
                    DB::raw('(
                        SELECT annexture_name
                        FROM odbusmaster.mst_annexture
                        WHERE annexture_type_id = 21
                        AND annexture_value = nt.category
                        LIMIT 1
                    ) as category_name'),

                    //  TRIGGER (annexture_type_id = 22)
                    DB::raw('(
                        SELECT annexture_name
                        FROM odbusmaster.mst_annexture
                        WHERE annexture_type_id = 22
                        AND annexture_value = nt.event_trigger
                        LIMIT 1
                    ) as trigger_name'),

                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = nt.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = nt.updated_by LIMIT 1) as updated_by_name')
                );
            $recordsTotal = DB::table('odbusmaster.mst_notification_templates')->count();

            if ($txtSearch !== null && $txtSearch !== '') {
                $query->where(function ($q) use ($txtSearch) {
                    $q->where('nt.name', 'like', "%{$txtSearch}%")
                        ->orWhere('nt.subject', 'like', "%{$txtSearch}%")
                        ->orWhere('nt.title', 'like', "%{$txtSearch}%")
                        ->orWhere('nt.body', 'like', "%{$txtSearch}%")
                        ->orWhere('nt.short_text', 'like', "%{$txtSearch}%");
                });
            }

            if ($type > 0) {
                $query->where('nt.type', $type);
            }

            if ($category > 0) {
                $query->where('nt.category', $category);
            }

            if ($trigger > 0) {
                $query->where('nt.event_trigger', $trigger);
            }

            if ($status !== null && $status !== '') {
                $query->where('nt.active_status', $status);
            }

            $recordsFiltered = (clone $query)->count();

            if ($length != -1) {
                $query->offset($start)->limit($length);
            }

            $rows = $query->orderBy('nt.id', 'desc')->get();

            foreach ($rows as $row) {

                $data[] = [
                    'id' => $row->id,
                    'enc_id' => Crypt::encryptString($row->id),

                    'notification_name' => $row->name ?? '--',
                    'notification_type' => $row->type_name ?? '--',
                    'notification_category' => $row->category_name ?? '--',
                    'notification_trigger' => $row->trigger_name ?? '--',

                    'created_date' => $row->created_at
                        ? date('d-M-Y H:i:s', strtotime($row->created_at))
                        : null,

                    'updated_date' => $row->updated_at
                        ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                        : null,

                    'created_by_name' => $row->created_by_name ?? '--',
                    'updated_by_name' => $row->updated_by_name ?? '--',

                    'is_active' => $row->active_status == 1 ? 'Active' : 'Inactive',
                ];
            }
        } catch (\Throwable $t) {

            Log::error("NotificationTemplate Error", [
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


            if ($id > 0) {

                $data['strPage']   = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = DB::table('odbusmaster.mst_notification_templates')
                    ->where('id', $id)
                    ->first();

                if (!$row) {
                    return redirect()->route('notification-template.index');
                }

                $data['row'] = $row;
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'name'     => 'required|string|max:100',
                    'slug'     => 'required|string|max:100',
                    'type'     => 'required|integer',
                    'category' => 'required',
                    'trigger'  => 'required',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $typeValue = (int) request('type');

                $subject = null;
                $title   = null;
                $body    = null;

                /*
                1 = Email
                2 = SMS
                3 = Push
                4 = WhatsApp
                */

                switch ($typeValue) {

                    case 1: // EMAIL
                        $subject = request('email_subject');
                        $body    = request('emailContent');
                        break;

                    case 2: // SMS
                        $body = request('smsContent');
                        break;

                    case 3: // PUSH
                        $title = request('title');
                        $body  = request('body');
                        break;

                    case 4: // WHATSAPP
                        $body = request('whatsappContent');
                        break;

                    default:
                        DB::rollBack();
                        return back()->withInput()->with([
                            'level' => 'danger',
                            'message' => 'Invalid Notification Type'
                        ]);
                }

                $insertData = [
                    'name'           => request('name'),
                    'slug'           => request('slug'),
                    'type'           => $typeValue,
                    'category'       => request('category'),
                    'event_trigger'  => request('trigger'),
                    'subject'        => $subject,
                    'title'          => $title,
                    'body'           => $body,
                    'short_text'     => null,
                    'variables_json' => null,
                    'active_status'  => 1,
                ];

                $redirectPage = "admin/notification-template";

                if ($id > 0) {

                    $oldData = DB::table('odbusmaster.mst_notification_templates')
                        ->where('id', $id)
                        ->first();

                    $newData = [
                        ...$insertData,
                        'updated_by' => 1,
                        'updated_at' => now()
                    ];

                    $oldChanged = [];
                    $newChanged = [];

                    foreach ($insertData as $key => $value) {

                        $oldValue = $oldData->$key ?? null;

                        if (trim((string)$oldValue) !== trim((string)$value)) {

                            $oldChanged[$key] = $oldValue;
                            $newChanged[$key] = $value;
                        }
                    }

                    if (!empty($newChanged)) {

                        app(CommonController::class)->auditLog(
                            'mst_notification_templates',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    DB::table('odbusmaster.mst_notification_templates')
                        ->where('id', $id)
                        ->update($newData);
                } else {

                    $row = [
                        ...$insertData,
                        'created_by' => 1,
                        'created_at' => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_notification_templates',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    $insertId = DB::table('odbusmaster.mst_notification_templates')
                        ->insertGetId($row);
                }

                DB::commit();

                return redirect($redirectPage)->with([
                    'level'   => 'success',
                    'message' => 'Notification Template ' . ($id ? 'updated' : 'created') . ' successfully'
                ]);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("NotificationTemplate Error", [
                'method' => $data['strPage'],
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addNotificationTemplate', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
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
