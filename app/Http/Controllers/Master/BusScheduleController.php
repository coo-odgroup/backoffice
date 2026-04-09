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

            // ================== FETCH SCHEDULE FOR VIEW ==================
            $bus_id = request('bus') ?? old('bus');
            $scheduleDates = [];

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

            $data['scheduleDates'] = $scheduleDates;

            // ================== FORM SUBMIT ==================
            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'operator'       => 'required|integer',
                    'bus'            => 'required|integer',
                    'running_cycle'  => 'required|integer|min:1|max:5',
                    'date'           => 'required|date',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $operator_id   = request('operator');
                $bus_id        = request('bus');
                $running_cycle = (int) request('running_cycle');
                $start_date    = request('date');

                // ✅ Insert into bus_schedule
                $schedule_id = DB::table('odbusdev.bus_schedule')->insertGetId([
                    'operator_id'   => $operator_id,
                    'bus_id'        => $bus_id,
                    'running_cycle' => $running_cycle,
                    'active_status' => 1,
                    'created_at'    => now(),
                    'created_by'    => 1
                ]);

                // ✅ Generate 30 dates
                $dates = [];
                $current = \Carbon\Carbon::parse($start_date);

                for ($i = 0; $i < 30; $i++) {

                    $dates[] = [
                        'bus_schedule_id' => $schedule_id,
                        'entry_date'      => $current->format('Y-m-d'),
                        'created_at'      => now(),
                        'created_by'      => 1
                    ];

                    $current->addDays($running_cycle);
                }

                DB::table('odbusdev.bus_schedule_date')->insert($dates);

                // ✅ Update bus running_cycle
                DB::table('odbusdev.bus')
                    ->where('id', $bus_id)
                    ->update([
                        'running_cycle' => $running_cycle,
                        'updated_at'    => now()
                    ]);

                DB::commit();

                return back()->with([
                    'level' => 'success',
                    'message' => 'Bus schedule created successfully'
                ])->withInput();
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error in BusScheduleController@add", [
                'error' => $t->getMessage()
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
}
