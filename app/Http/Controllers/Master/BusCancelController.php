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

class BusCancelController extends Controller
{
    public function index()
    {
        return view('Master.viewBusCancel');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $operator  = request('operator') !== null && request('operator') !== '' ? (int)request('operator') : null;
            $bus       = request('bus') !== null && request('bus') !== '' ? (int)request('bus') : null;
            $status    = request('selStatus') !== null && request('selStatus') !== '' ? (int)request('selStatus') : null;
            $fromDate  = request('fromDate');
            $toDate    = request('toDate');

            $query = DB::connection('mysql_dev')
                ->table('bus_cancelled as bc')

                ->join('bus_cancelled_date as bcd', function ($join) {
                    $join->on('bcd.bus_cancelled_id', '=', 'bc.id')
                        ->where('bcd.active_status', 1);
                })

                ->join('bus as b', 'b.id', '=', 'bc.bus_id')


                ->join('odbusmaster.users as u', function ($join) {
                    $join->on('u.id', '=', 'bc.bus_operator_id')
                        ->where('u.user_role', 9);
                })

                ->leftJoin('odbusmaster.mst_annexture as ma', 'ma.id', '=', 'bc.reason')

                ->select(
                    'bc.id',
                    'bc.bus_id',
                    'bc.bus_operator_id',

                    'u.organization_name as operator_name',

                    'b.name as bus_name',
                    'b.bus_number',

                    'bcd.cancelled_date',

                    'bc.reason',
                    'bc.other_reason',
                    'ma.annexture_name',

                    'bc.active_status',
                    'bc.created_at',
                    'bc.updated_at'
                );

            if (!empty($operator)) {
                $query->where('bc.bus_operator_id', $operator);
            }

            if (!empty($bus)) {
                $query->where('bc.bus_id', $bus);
            }

            if (!empty($fromDate)) {
                $query->whereDate('bcd.cancelled_date', '>=', $fromDate);
            }

            if (!empty($toDate)) {
                $query->whereDate('bcd.cancelled_date', '<=', $toDate);
            }

            if ($status !== null && $status !== '') {
                $query->where('bc.active_status', $status);
            }

            $rows = $query->orderBy('bc.id', 'desc')->get();

            $grouped = [];

            foreach ($rows as $row) {

                $key = $row->bus_id; // 🔥 IMPORTANT (per bus)

                if (!isset($grouped[$key])) {

                    $reasonText = ($row->reason == 77)
                        ? $row->other_reason
                        : $row->annexture_name;

                    $grouped[$key] = [
                        'id' => $row->id,
                        'bus_cancel_id' => $row->id,
                        'enc_bus_cancel_id' => Crypt::encryptString($row->id),

                        'operator' => $row->operator_name ?? '--',

                        'busName' => trim(($row->bus_name ?? '') . ' / ' . ($row->bus_number ?? '')),

                        'route' => '--',

                        'reason' => $reasonText,

                        'cancelDates' => [],

                        'created_date' => $row->created_at
                            ? date('d-M-Y H:i:s', strtotime($row->created_at))
                            : null,

                        'updated_date' => $row->updated_at
                            ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                            : null,

                        'is_active' => $row->active_status == 1 ? 'Active' : 'Inactive',
                    ];
                }

                $grouped[$key]['cancelDates'][] = date('d-M-Y', strtotime($row->cancelled_date));
            }

            foreach ($grouped as &$g) {
                $g['cancelDates'] = implode('<br>', $g['cancelDates']);
            }

            $data = array_values($grouped);

            $recordsTotal = count($data);
            $recordsFiltered = $recordsTotal;
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

            /*
        ===================================================
        EDIT MODE LOAD
        ===================================================
        */
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

                // fetch cancelled dates
                $row->dates = DB::connection('mysql_dev')
                    ->table('bus_cancelled_date')
                    ->where('bus_cancelled_id', $id)
                    ->where('active_status', 1)
                    ->pluck('cancelled_date')
                    ->toArray();

                $data['row'] = $row;
            }

            /*
        ===================================================
        SUBMIT
        ===================================================
        */
            if (request()->isMethod('post')) {

                DB::beginTransaction();

                $operator_id  = request('operator');
                $bus_ids      = explode(',', request('bus'));
                $year         = request('year');
                $month        = request('month');
                $reason       = request('reason');
                $other_reason = request('other_reason');
                $dates        = request('dates') ?? [];
                $removedDates = json_decode(request('removed_dates'), true) ?? [];

                /*
            ==========================================
            UPDATE
            ==========================================
            */
                if ($id > 0) {

                    DB::connection('mysql_dev')
                        ->table('bus_cancelled')
                        ->where('id', $id)
                        ->update([
                            'bus_operator_id' => $operator_id,
                            'bus_id'          => implode(',', $bus_ids),
                            'year'            => $year,
                            'month'           => $month,
                            'reason'          => $reason,
                            'other_reason'    => ($reason == 77) ? $other_reason : null,
                            'updated_at'      => now()
                        ]);

                    $cancel_id = $id;
                } else {

                    /*
                ==========================================
                INSERT
                ==========================================
                */
                    $cancel_id = DB::connection('mysql_dev')
                        ->table('bus_cancelled')
                        ->insertGetId([
                            'bus_operator_id' => $operator_id,
                            'bus_id'          => implode(',', $bus_ids),
                            'year'            => $year,
                            'month'           => $month,
                            'reason'          => $reason,
                            'other_reason'    => ($reason == 77) ? $other_reason : null,
                            'active_status'   => 1,
                            'created_at'      => now()
                        ]);
                }

                /*
            ==========================================
            SAVE DATES
            ==========================================
            */
                foreach ($dates as $date) {

                    $exists = DB::connection('mysql_dev')
                        ->table('bus_cancelled_date')
                        ->where('bus_cancelled_id', $cancel_id)
                        ->whereDate('cancelled_date', $date)
                        ->first();

                    if ($exists) {

                        DB::connection('mysql_dev')
                            ->table('bus_cancelled_date')
                            ->where('id', $exists->id)
                            ->update([
                                'active_status' => 1,
                                'updated_at'    => now()
                            ]);
                    } else {

                        DB::connection('mysql_dev')
                            ->table('bus_cancelled_date')
                            ->insert([
                                'bus_cancelled_id' => $cancel_id,
                                'cancelled_date'   => $date,
                                'active_status'    => 1,
                                'created_at'       => now()
                            ]);
                    }
                }

                /*
            ==========================================
            REMOVE UNCHECKED
            ==========================================
            */
                foreach ($removedDates as $rd) {

                    DB::connection('mysql_dev')
                        ->table('bus_cancelled_date')
                        ->where('bus_cancelled_id', $cancel_id)
                        ->whereDate('cancelled_date', $rd['date'])
                        ->update([
                            'active_status' => 0,
                            'updated_at'    => now()
                        ]);
                }

                DB::commit();

                return redirect()->route('bus-cancel.index')->with([
                    'level'   => 'success',
                    'message' => 'Bus Cancel ' . ($id ? 'updated' : 'created') . ' successfully'
                ]);
            }
            } catch (\Throwable $t) {

                DB::rollBack();

                return back()->withInput()->with([
                    'level' => 'danger',
                    'message' => $t->getMessage()
                ]);
            }

        return view('Master.addBusCancel', compact('data'));
    }
    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function getBusScheduleDatesByMonth(Request $request)
    {
        try {

            $bus_ids = explode(',', $request->bus_ids);
            $year = $request->year;
            $month = $request->month;

            $startDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';

            $data = [];

            $schedules = DB::connection('mysql_dev')
                ->table('bus_schedule')
                ->whereIn('bus_id', $bus_ids)
                ->where('active_status', 1)
                ->orderByDesc('id')
                ->get()
                ->groupBy('bus_id');

            foreach ($schedules as $bus_id => $rows) {

                $schedule = $rows->first();

                $dates = DB::connection('mysql_dev')
                    ->table('bus_schedule_date')
                    ->where('bus_schedule_id', $schedule->id)
                    ->whereDate('entry_date', '>=', $startDate)
                    ->orderBy('entry_date')
                    ->pluck('entry_date');

                if ($dates->isEmpty()) continue;

                $bus = DB::connection('mysql_dev')
                    ->table('bus')
                    ->where('id', $bus_id)
                    ->first();

                $data[$bus_id] = [
                    'bus_name' => $bus->name ?? '',
                    'bus_number' => $bus->bus_number ?? '',
                    'dates' => $dates
                ];
            }

            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getCancelledBusData(Request $request)
    {
        try {

            $bus_ids = explode(',', $request->bus_ids);

            $startDate = $request->year . '-' . str_pad($request->month, 2, '0', STR_PAD_LEFT) . '-01';

            $data = DB::connection('mysql_dev')
                ->table('bus_cancelled as bc')
                ->join('bus_cancelled_date as bcd', 'bcd.bus_cancelled_id', '=', 'bc.id')
                ->join('odbusdev.bus as b', 'b.id', '=', 'bc.bus_id')
                ->leftJoin('odbusmaster.mst_annexture as ma', 'ma.id', '=', 'bc.reason')
                ->whereIn('bc.bus_id', $bus_ids)
                ->where('bcd.active_status', 1)
                ->select(
                    'bc.bus_id',
                    'b.name as bus_name',
                    'b.bus_number',
                    'bcd.cancelled_date',
                    'bc.reason',
                    'bc.other_reason',
                    'ma.annexture_name',
                    'bc.created_at'
                )
                ->orderBy('bcd.cancelled_date')
                ->get();

            $grouped = [];

            foreach ($data as $row) {

                $reasonText = ($row->reason == 77)
                    ? $row->other_reason
                    : $row->annexture_name;

                $grouped[$row->bus_id]['bus_name'] = $row->bus_name;
                $grouped[$row->bus_id]['bus_number'] = $row->bus_number;
                $grouped[$row->bus_id]['reason'] = $reasonText;
                $grouped[$row->bus_id]['created_at'] = $row->created_at;

                $grouped[$row->bus_id]['dates'][] = $row->cancelled_date;
            }

            return response()->json([
                'status' => true,
                'data' => $grouped
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
