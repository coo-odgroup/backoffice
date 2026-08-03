<?php

namespace App\Http\Controllers\Master;

use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\SupportTicket;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class SupportTicketController extends Controller
{

    public function supportTicket()
    {
        return view('master.supportTicket');
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

                $redirectPage = "admin/supportTicket/edit/" . $encId;
                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = SupportTicket::where('id', $id)->first();

                if (!$row) {
                    return redirect('supportTicket');
                }

                $data['row'] = $row;
            } else {
                $id = 0;
                $redirectPage = "admin/supportTicket";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [

                    'module_type'      => 'required|max:100',
                    'ticket_code'      => 'required|max:20|unique:support_tickets,ticket_code,' . $id,
                    'title'            => 'required|max:255',
                    'description'      => 'required',

                    'project'          => 'required|max:100',
                    'category'         => 'required|max:100',
                    'severity'         => 'required|max:100',
                    'priority'         => 'required|max:100',

                    'reported_by'      => 'required|integer',
                    'assigned_to'      => 'required|integer',
                    'assigned_by'      => 'required|integer',

                    'due_date'         => 'nullable|date',
                    'estimated_hours'  => 'nullable|numeric',
                    'actual_hours'     => 'nullable|numeric',

                    'browser'          => 'nullable|max:50',
                    'device'           => 'nullable|max:50',
                    'app_version'      => 'nullable|max:100',

                    'attachment_title.*' => 'nullable|max:255',
                    'attachment_type.*'  => 'nullable|max:50',

                    'attachment_file.*' => [

                        'nullable',
                        'file',

                        function ($attribute, $value, $fail) {

                            if (!$value) {
                                return;
                            }

                            if (!($value instanceof UploadedFile)) {
                                return;
                            }

                            $ext = strtolower($value->getClientOriginalExtension());

                            $videoExtensions = [
                                'mp4',
                                'avi',
                                'mov',
                                'mkv',
                                'wmv',
                                'flv',
                                'webm'
                            ];

                            $maxSize = in_array($ext, $videoExtensions)
                                ? 10 * 1024 * 1024
                                : 2 * 1024 * 1024;

                            if ($value->getSize() > $maxSize) {

                                $fail(
                                    in_array($ext, $videoExtensions)
                                        ? 'Video size should not exceed 10 MB.'
                                        : 'File size should not exceed 2 MB.'
                                );
                            }
                        }
                    ]

                ], [

                    'module_type.required' => 'Please select Module.',
                    'ticket_code.required' => 'Ticket Code is required.',
                    'ticket_code.unique'   => 'Ticket Code already exists.',
                    'title.required'       => 'Title is required.',
                    'project.required'     => 'Please select Project.',
                    'category.required'    => 'Please select Category.',
                    'severity.required'    => 'Please select Severity.',
                    'priority.required'    => 'Please select Priority.',
                    'reported_by.required' => 'Please select Reported By.',
                    'assigned_to.required' => 'Please select Assigned To.',
                    'assigned_by.required' => 'Please select Assigned By.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $module          = htmlEncode(trim(Purifier::clean(request('module_type'))));
                $ticketCode      = htmlEncode(trim(Purifier::clean(request('ticket_code'))));
                $title           = htmlEncode(trim(Purifier::clean(request('title'))));
                $description = Purifier::clean(request('description'), 'default');

                $project         = htmlEncode(trim(Purifier::clean(request('project'))));
                $category        = htmlEncode(trim(Purifier::clean(request('category'))));
                $severity        = htmlEncode(trim(Purifier::clean(request('severity'))));
                $priority        = htmlEncode(trim(Purifier::clean(request('priority'))));

                $status = $id > 0
                    ? htmlEncode(trim(Purifier::clean(request('status'))))
                    : 'Open';

                $reportedBy      = (int)request('reported_by');
                $assignedTo      = (int)request('assigned_to');
                $assignedBy      = (int)request('assigned_by');

                $dueDate         = request('due_date');
                $estimatedHours  = request('estimated_hours');
                $actualHours     = request('actual_hours');

                $browser         = htmlEncode(trim(Purifier::clean(request('browser'))));
                $device          = htmlEncode(trim(Purifier::clean(request('device'))));
                $appVersion      = htmlEncode(trim(Purifier::clean(request('app_version'))));


                $duplicate = SupportTicket::where('ticket_code', $ticketCode);

                if ($id > 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {

                    DB::rollBack();

                    return back()->with([
                        'level' => 'danger',
                        'message' => 'Ticket Code already exists.'
                    ])->withInput();
                }

                $fileTitles = [];
                $fileTypes  = [];
                $filePaths  = [];

                if (request()->hasFile('attachment_file')) {

                    foreach (request()->file('attachment_file') as $key => $file) {

                        if (!$file) {
                            continue;
                        }

                        $fileName = Str::random(64) . '.' . strtolower($file->getClientOriginalExtension());

                        $file->storeAs(
                            'public/uploads/SupportTicket',
                            $fileName
                        );

                        $fileTitles[] = request('attachment_title')[$key] ?? '';
                        $fileTypes[] = request('attachment_type')[$key] ?? '';
                        $filePaths[] = 'uploads/SupportTicket/' . $fileName;
                    }
                }

                if ($id > 0) {

                    $oldData = SupportTicket::find($id);
                    if (
                        empty($filePaths) &&
                        empty($fileTitles) &&
                        empty($fileTypes)
                    ) {
                        $fileTitles = json_decode($oldData->file_title, true) ?? [];
                        $fileTypes  = json_decode($oldData->file_type, true) ?? [];
                        $filePaths  = json_decode($oldData->file_path, true) ?? [];
                    }

                    $newData = [

                        'ticket_code'      => $ticketCode,
                        'title'            => $title,
                        'description'      => $description,
                        'module'           => $module,
                        'project_id'       => $project,
                        'severity'         => $severity,
                        'category'         => $category,
                        'priority'         => $priority,
                        'status'           => $status,
                        'assigned_to'      => $assignedTo,
                        'reported_by'      => $reportedBy,
                        'assigned_by'      => $assignedBy,
                        'due_date'         => $dueDate,
                        'estimated_hours'  => $estimatedHours,
                        'actual_hours'     => $actualHours,
                        'browser'          => $browser,
                        'device'           => $device,
                        'app_version'      => $appVersion,
                        'file_title'       => json_encode($fileTitles),
                        'file_type'        => json_encode($fileTypes),
                        'file_path'        => json_encode($filePaths),
                        'active_status'    => 1

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



                    $newData['updated_by'] = 1;

                    $oldData->fill($newData);
                    $oldData->save();

                    if (!empty($newChanged)) {
                        app(CommonController::class)->auditLog(
                            'support_tickets',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }
                } else {

                    $row = [

                        'ticket_code'      => $ticketCode,
                        'title'            => $title,
                        'description'      => $description,

                        'module'           => $module,
                        'project_id'       => $project,
                        'severity'         => $severity,
                        'category'         => $category,
                        'priority'         => $priority,
                        'status'           => $status,

                        'assigned_to'      => $assignedTo,
                        'reported_by'      => $reportedBy,
                        'assigned_by'      => $assignedBy,

                        'due_date'         => $dueDate,
                        'estimated_hours'  => $estimatedHours,
                        'actual_hours'     => $actualHours,

                        'browser'          => $browser,
                        'device'           => $device,
                        'app_version'      => $appVersion,

                        'file_title'       => json_encode($fileTitles),
                        'file_type'        => json_encode($fileTypes),
                        'file_path'        => json_encode($filePaths),

                        'active_status'    => 1,
                        'created_by'       => 1,
                        'created_at'       => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'support_tickets',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    $obj = new SupportTicket();
                    $obj->fill($row);
                    $obj->save();
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Support Ticket ' . ($id > 0 ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error in SupportTicketController@add", [
                'method' => $method,
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addSupportTicket', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function dataTableView()
    {
        $recordsTotal    = 0;
        $recordsFiltered = 0;
        $data            = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int) request('selStatus') : '';
            $module = request('module');
            $priority = request('priority');
            $environment = request('environment');

            $dataQuery = DB::table('support_tickets as st')
                ->leftJoin('users as ru', 'ru.id', '=', 'st.reported_by')
                ->leftJoin('users as au', 'au.id', '=', 'st.assigned_to')
                ->leftJoin('users as abu', 'abu.id', '=', 'st.assigned_by')
                ->select(
                    'st.id',
                    'st.ticket_code',
                    'st.title',
                    'st.module',
                    'st.category',
                    'st.priority',
                    'st.status',
                    'st.active_status',
                    'st.created_at',
                    'st.updated_at',
                    'st.created_by',
                    'st.updated_by',

                    'ru.name as reported_by_name',
                    'au.name as assigned_to_name',
                    'abu.name as assigned_by_name',

                    DB::raw('(SELECT name FROM users WHERE id = st.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = st.updated_by LIMIT 1) as updated_by_name')
                );


            if (!empty($txtSearch)) {

                $dataQuery->where(function ($q) use ($txtSearch) {

                    $q->where('st.ticket_code', 'like', "%{$txtSearch}%")
                        ->orWhere('st.title', 'like', "%{$txtSearch}%")
                        ->orWhere('ru.name', 'like', "%{$txtSearch}%")
                        ->orWhere('au.name', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== '' && $selStatus !== null) {
                $dataQuery->where('st.active_status', (int) $selStatus);
            }

            if (!empty($module)) {
                $dataQuery->where('st.module', $module);
            }

            if (!empty($priority)) {
                $dataQuery->where('st.priority', $priority);
            }

            if (!empty($environment)) {
                $dataQuery->where('st.environment', $environment);
            }

            $recordsTotal = (clone $dataQuery)->count('st.id');
            $recordsFiltered = $recordsTotal;

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);


            if (!empty(request('order'))) {

                $columns = [
                    2 => 'st.ticket_code',
                    3 => 'st.module',
                    4 => 'st.title',
                    5 => 'st.priority',
                    6 => 'st.category',
                    7 => 'st.status',
                    8 => 'st.updated_at',
                    9 => 'st.active_status'
                ];

                $order      = request('order');
                $orderCol = $columns[$order[0]['column']] ?? 'st.ticket_code';
                $orderDir   = $order[0]['dir'] ?? 'asc';
            } else {
                $orderCol = 'st.id';
                $orderDir = 'desc';
            }

            $dataQuery->orderBy($orderCol, $orderDir);


            if ($length === -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery
                    ->offset($start)
                    ->limit($length)
                    ->get();
            }


            foreach ($arrRes as $row) {

                $row->created_date = date('d-M-Y H:i:s', strtotime($row->created_at));

                $row->updated_date = $row->updated_at
                    ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                    : null;

                $row->is_active = $row->active_status ? 'Active' : 'Inactive';

                $row->enc_id = Crypt::encryptString($row->id);

                $moduleMap = [
                    1 => 'Website',
                    2 => 'Admin Console',
                    3 => 'Operator Panel',
                    4 => 'Agent Panel',
                    5 => 'API Client Dashboard',
                    6 => 'Outgoing API',
                    7 => 'Admin API',
                    8 => 'Website API',
                    9 => 'Android',
                    10 => 'IOS'
                ];

                $priorityMap = [
                    1 => 'Low',
                    2 => 'Medium',
                    3 => 'High',
                    4 => 'Critical'
                ];

                $categoryMap = [
                    1 => 'Bug',
                    2 => 'Feature',
                    3 => 'Enhancement',
                    4 => 'Support'
                ];

                $row->module = $moduleMap[$row->module] ?? '--';
                $row->priority = $priorityMap[$row->priority] ?? '--';
                $row->category = $categoryMap[$row->category] ?? '--';
            }

            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error("Exception in RolesController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace'         => $t->getTraceAsString()
            ]);

            $recordsTotal    = 0;
            $recordsFiltered = 0;
            $data             = [];
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
}
