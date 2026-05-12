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

class CronJobController extends Controller
{
    public function index()
    {
        return view('Master.viewCronJob');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $cronName = htmlEncode(Purifier::clean(request('cronName') ));
            $type = request('type') !== null && request('type') !== ''? request('type'): null;
            $scheduler = request('scheduler') !== null && request('scheduler') !== '' ? request('scheduler') : null;
            $execution = request('execution') !== null && request('execution') !== '' ? request('execution') : null;
            $status = request('selStatus') !== null && request('selStatus') !== '' ? (int) request('selStatus') : null;

            $start  = request('start', 0);
            $length = request('length', 10);

            $query = DB::table('odbusmaster.mst_cron_jobs as cj')
                ->select(

                    'cj.id',
                    'cj.name',
                    'cj.slug',
                    'cj.type',
                    'cj.schedule_type',

                    DB::raw('(
                            SELECT annexture_name
                            FROM odbusmaster.mst_annexture
                            WHERE annexture_type_id = 20
                            AND annexture_value = cj.type
                            LIMIT 1
                        ) as type_name'),

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
    
            if (!empty(trim($cronName))) {

                $search = trim($cronName);

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

            $redirectPage = "admin/cron-job";

            if ($id > 0) {

                $redirectPage = "admin/cron-job/edit/" . $encId;

                $data['strPage']   = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = DB::table('odbusmaster.mst_cron_jobs')
                    ->where('id', $id)
                    ->first();

                if (!$row) {
                    return redirect()->route('cron-job.index');
                }

                $data['row'] = $row;
            }

            // ===================== POST =====================
            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [

                    'cronName'  => 'required|string|max:100',
                    'slug'      => 'required|string|max:100',
                    'type'      => 'required',
                    'scheduler' => 'required',
                    'execution' => 'required',

                ]);

                if ($validator->fails()) {
                    return back()
                        ->withErrors($validator)
                        ->withInput();
                }

                DB::beginTransaction();

                $cronName = htmlEncode(ucwords(strtolower(Purifier::clean(request('cronName')))));


                $slug = htmlEncode(strtolower(Purifier::clean(
                    request('slug')))
                    );


                $type = (int) Purifier::clean(
                    request('type')
                );

                $scheduler = (int) Purifier::clean(
                    request('scheduler')
                );

                $interval = Purifier::clean(
                    request('interval')
                );

                $execution = htmlEncode(
                    Purifier::clean(
                        request('execution')
                    )
                );

                $cron = htmlEncode(
                    Purifier::clean(
                        request('cron')
                    )
                );

                $job = htmlEncode(
                    Purifier::clean(
                        request('job')
                    )
                );

                $command = htmlEncode(
                    Purifier::clean(
                        request('command')
                    )
                );

                $runTimes = [];

                if (!empty(request('runTime'))) {

                    foreach (request('runTime') as $time) {

                        $time = trim(
                            htmlEncode(
                                Purifier::clean($time)
                            )
                        );

                        if (!empty($time)) {
                            $runTimes[] = $time;
                        }
                    }
                }

                $insertData = [

                    'name' => $cronName,
                    'slug' => $slug,
                    'type' => $type,
                    'schedule_type' => $scheduler,
                    'interval_minutes' =>!empty($interval)? $interval : null,
                    'run_times_json' =>!empty($runTimes)? json_encode(array_values($runTimes)) : null,
                    'cron_expression' =>!empty($cron) ? $cron : null,
                    'execution_type' => $execution,
                    'job_class' =>!empty($job)? $job : null,
                    'command_name' =>!empty($command) ? $command : null,
                    'active_status' => 1,
                ];

                if ($id > 0) {

                    $oldData = DB::table('odbusmaster.mst_cron_jobs')
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

                        if (json_encode($oldValue) !== json_encode($value)) {

                            $oldChanged[$key] = $oldValue;
                            $newChanged[$key] = $value;
                        }
                    }

                    // AUDIT LOG
                    if (!empty($newChanged)) {

                        app(CommonController::class)->auditLog(
                            'mst_cron_jobs',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    DB::table('odbusmaster.mst_cron_jobs')
                        ->where('id', $id)
                        ->update($newData);
                } else {

                    // ================= INSERT =================

                    $rowData = [
                        ...$insertData,
                        'created_by' => 1,
                        'created_at' => now()
                    ];

                    $insertId = DB::table('odbusmaster.mst_cron_jobs')
                        ->insertGetId($rowData);

                    // AUDIT LOG
                    app(CommonController::class)->auditLog(
                        'mst_cron_jobs',
                        $insertId,
                        'INSERT',
                        [],
                        $rowData
                    );
                }

                DB::commit();

                return redirect($redirectPage)->with([
                    'level'   => 'success',
                    'message' => 'Cron Job ' . ($id ? 'updated' : 'created') . ' successfully'
                ]);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("CronJobController Error", [
                'method' => $data['strPage'],
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addCronJob', compact('data'));
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

    public function cronNotificationRule($encId = null)
    {
        $data = [];

        $data['strPage']   = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset']  = 'Reset';

        return view('Master.addNotificationRule', compact('data'));
    }
}
