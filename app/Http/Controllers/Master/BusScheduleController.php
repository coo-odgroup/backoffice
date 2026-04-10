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
            $selStatus = request('selStatus');
            $operator = request('operator');
            $bus = request('bus');

            $query = DB::connection('odbusdev')
                ->table('bus_schedule as bs')

                ->select(
                    'bs.id',
                    'bs.operator_id',
                    'bs.bus_id',
                    DB::raw('(SELECT name FROM bus WHERE id = bs.bus_id LIMIT 1) as BUS_name'),
                    DB::raw('(SELECT bus_number FROM bus WHERE id = bs.bus_id LIMIT 1) as bus_number'),
                    DB::raw('(SELECT organization_name FROM odbusmaster.users WHERE id = bs.operator_id AND user_role = 9 LIMIT 1) as operator_name'),
                    'bs.active_status',
                    'bs.created_at',
                    'bs.updated_at',

                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = bs.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = bs.updated_by LIMIT 1) as updated_by_name')
                );

            // if (!empty($txtSearch)) {
            //     $query->where(function ($q) use ($txtSearch) {
            //         $q->where('b.bus_name', 'like', "%{$txtSearch}%")
            //             ->orWhere('b.bus_number', 'like', "%{$txtSearch}%")
            //             ->orWhere('u.organization_name', 'like', "%{$txtSearch}%");
            //     });
            // }

            // //  FILTERS
            // if (!empty($operator)) {
            //     $query->where('bs.operator_id', $operator);
            // }

            // if (!empty($bus)) {
            //     $query->where('bs.bus_id', $bus);
            // }

            // if ($selStatus !== null && $selStatus !== '') {
            //     $query->where('bs.active_status', $selStatus);
            // }

            $rows = $query->orderBy('bs.id', 'desc')->get();

            foreach ($rows as $key => $row) {

                $data[] = [
                    'id' => $row->id,

                    'operator_name' => $row->operator_name ?? '--',

                    'bus_name' => trim(($row->bus_name ?? '') . ' / ' . ($row->bus_number ?? '')),

                    'created_date' => $row->created_at
                        ? date('d-M-Y H:i:s', strtotime($row->created_at))
                        : null,

                    'updated_date' => $row->updated_at
                        ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                        : null,

                    'created_by_name' => $row->created_by_name ?? '--',
                    'updated_by_name' => $row->updated_by_name ?? '--',

                    'is_active' => $row->active_status == 1 ? 'Active' : 'Inactive',

                    'enc_brand_id' => Crypt::encryptString($row->id),

                    'enc_bustype_id' => Crypt::encryptString($row->bus_id),
                    'layout_name' => $row->bus_name ?? 'Bus'
                ];
            }

            $recordsTotal = count($data);
            $recordsFiltered = $recordsTotal;
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

                $schedule_id = DB::table('odbusdev.bus_schedule')->insertGetId([
                    'operator_id'   => $operator_id,
                    'bus_id'        => $bus_id,
                    'running_cycle' => $running_cycle,
                    'active_status' => 1,
                    'created_by'    => 1
                ]);

                $dates = [];
                $current = \Carbon\Carbon::parse($start_date);

                for ($i = 0; $i < 30; $i++) {

                    $dates[] = [
                        'bus_schedule_id' => $schedule_id,
                        'entry_date'      => $current->format('Y-m-d'),
                        'created_by'      => 1
                    ];

                    $current->addDays($running_cycle);
                }

                DB::table('odbusdev.bus_schedule_date')->insert($dates);

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

    public function getScheduleDates(Request $request)
    {
        $bus_id = $request->bus_id;
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

        //  Build HTML here (no blade file)
        if (!empty($scheduleDates)) {

            $chunkSize = ceil(count($scheduleDates) / 3);
            $chunks = array_chunk($scheduleDates, $chunkSize);

            $html = '<div class="row">';

            foreach ($chunks as $chunk) {
                $html .= '<div class="col-4">';

                foreach ($chunk as $date) {
                    $html .= '<div class="date-tile text-center mb-2">'
                        . \Carbon\Carbon::parse($date)->format('d-M-Y') .
                        '</div>';
                }

                $html .= '</div>';
            }

            $html .= '</div>';
        } else {
            $html = '<div class="text-center text-muted">Bus is not scheduled</div>';
        }

        return response($html);
    }
}
