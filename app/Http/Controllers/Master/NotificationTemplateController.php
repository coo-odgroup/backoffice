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

            $status = (
                request('selStatus') !== null &&
                request('selStatus') !== ''
            ) ? (int) request('selStatus') : '';

            $type = (int) request('type');

            $category = (int) request('category');

            $trigger = (int) request('trigger');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start)
                ? (int) $start
                : 0;

            $length = is_numeric($length)
                ? (int) $length
                : 10;

            $dataQuery = DB::table(
                'odbusmaster.mst_notification_templates as nt'
            )

                ->select(

                    'nt.id',
                    'nt.name',
                    'nt.type',
                    'nt.category',
                    'nt.event_trigger',
                    'nt.active_status',
                    'nt.created_at',
                    'nt.updated_at',

                    DB::raw('(
                    SELECT annexture_name
                    FROM odbusmaster.mst_annexture
                    WHERE annexture_type_id = 19
                    AND annexture_value = nt.type
                    LIMIT 1
                ) as type_name'),

                    DB::raw('(
                    SELECT annexture_name
                    FROM odbusmaster.mst_annexture
                    WHERE annexture_type_id = 21
                    AND annexture_value = nt.category
                    LIMIT 1
                ) as category_name'),

                    DB::raw('(
                    SELECT annexture_name
                    FROM odbusmaster.mst_annexture
                    WHERE annexture_type_id = 22
                    AND annexture_value = nt.event_trigger
                    LIMIT 1
                ) as trigger_name'),

                    DB::raw('(
                    SELECT name
                    FROM odbusmaster.users
                    WHERE id = nt.created_by
                    LIMIT 1
                ) as created_by_name'),

                    DB::raw('(
                    SELECT name
                    FROM odbusmaster.users
                    WHERE id = nt.updated_by
                    LIMIT 1
                ) as updated_by_name')
                );

            // SEARCH
            if (!empty($txtSearch)) {

                $dataQuery->where(function ($q) use ($txtSearch) {

                    $q->where(
                        'nt.name',
                        'like',
                        "%{$txtSearch}%"
                    )

                        ->orWhere(
                            'nt.subject',
                            'like',
                            "%{$txtSearch}%"
                        )

                        ->orWhere(
                            'nt.title',
                            'like',
                            "%{$txtSearch}%"
                        )

                        ->orWhere(
                            'nt.body',
                            'like',
                            "%{$txtSearch}%"
                        )

                        ->orWhere(
                            'nt.short_text',
                            'like',
                            "%{$txtSearch}%"
                        );
                });
            }

            // FILTERS
            if ($type > 0) {
                $dataQuery->where('nt.type', $type);
            }

            if ($category > 0) {
                $dataQuery->where('nt.category', $category);
            }

            if ($trigger > 0) {
                $dataQuery->where('nt.event_trigger', $trigger);
            }

            if (
                isset($status) &&
                $status !== ''
            ) {

                $dataQuery->where(
                    'nt.active_status',
                    $status
                );
            }

            $count = $dataQuery->count('nt.id');

            // ORDERING
            if (!empty(request('order'))) {

                $columns = [

                    2 => 'nt.name',
                    3 => 'type_name',
                    4 => 'category_name',
                    5 => 'trigger_name',
                    6 => 'nt.created_at',
                    7 => 'nt.active_status'
                ];

                $orderBy = request('order');

                $orderColumn =
                    $columns[$orderBy[0]['column']] ?? 'nt.id';

                $orderType =
                    $orderBy[0]['dir'];
            } else {

                $orderColumn = 'nt.id';

                $orderType = 'desc';
            }

            $dataQuery = $dataQuery->orderBy(
                $orderColumn,
                $orderType
            );

            // PAGINATION
            if ($length == -1) {

                $arrRes = $dataQuery->get();
            } else {

                $arrRes = $dataQuery
                    ->limit($length)
                    ->offset($start)
                    ->get();
            }

            // FORMAT DATA
            if (count($arrRes) > 0) {

                foreach ($arrRes as $val) {

                    $val->enc_id =
                        Crypt::encryptString(
                            $val->id
                        );

                    $val->notification_name =
                        $val->name ?? '--';

                    $val->notification_type =
                        $val->type_name ?? '--';

                    $val->notification_category =
                        $val->category_name ?? '--';

                    $val->notification_trigger =
                        $val->trigger_name ?? '--';

                    $val->created_date =
                        $val->created_at
                        ?
                        date(
                            'd-M-Y H:i:s',
                            strtotime($val->created_at)
                        )
                        :
                        null;

                    $val->updated_date =
                        $val->updated_at
                        ?
                        date(
                            'd-M-Y H:i:s',
                            strtotime($val->updated_at)
                        )
                        :
                        null;

                    $val->created_by_name = $val->created_by_name ?? '--';
                    $val->updated_by_name = $val->updated_by_name ?? '--';
                    $val->is_active = ($val->active_status == 1)?'Active':'Inactive';
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info(
                "Exception occurred in NotificationTemplateController@dataTableView",
                [
                    'error_message' => $t->getMessage(),
                    'trace' => $t->getTraceAsString()
                ]
            );

            Log::error("Error", [
                'Controller' => 'NotificationTemplateController',
                'Method' => 'dataTableView',
                'Error' => config('constants.SERVER_ERROR_MESSAGE')
            ]);

            $recordsTotal = 0;
            $recordsFiltered = 0;
            $data = [];
        }

        return response()->json([
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
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

                $name = htmlEncode(ucwords(strtolower(Purifier::clean(request('name')))));
                $slug = htmlEncode(strtolower(Purifier::clean(request('slug'))));
                $typeValue = (int) Purifier::clean(request('type'));
                $category = (int) Purifier::clean(request('category'));
                $trigger = (int) Purifier::clean(request('trigger'));

                $subject = null;
                $title = null;
                $body = null;

                /*
                1 = Email
                2 = SMS
                3 = Push
                4 = WhatsApp
                */

                switch ($typeValue) {

                    case 1:

                        $subject = htmlEncode(Purifier::clean(request('email_subject')));
                        $body = htmlEncode(Purifier::clean(request('emailContent')));
                        break;

                    case 2:

                        $body = htmlEncode(Purifier::clean(request('smsContent')));
                        break;

                    case 3:

                        $title = htmlEncode(Purifier::clean(request('title')));
                        $body = htmlEncode(Purifier::clean(request('body')));
                        break;

                    case 4:

                        $body = htmlEncode(Purifier::clean(request('whatsappContent')));
                        break;

                    default:

                        DB::rollBack();

                        return back()
                            ->withInput()
                            ->with([
                                'level' => 'danger',
                                'message' =>
                                'Invalid Notification Type'
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
