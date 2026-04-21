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

class SeatBlockController extends Controller
{
    public function index()
    {
        return view('Master.viewSeatBlock');
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
        $data['strPage']   = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset']  = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {
                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';
            }

            $redirectPage = "admin/seat-block";

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'operator'        => 'required',
                    'bus'             => 'required',
                    'seat_operations' => 'required'
                ], [
                    'operator.required'        => 'Please select operator',
                    'bus.required'             => 'Please select bus',
                    'seat_operations.required' => 'Please select seats'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                $seatOperations = json_decode(request('seat_operations'), true);

                if (empty($seatOperations) || !is_array($seatOperations)) {
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Invalid seat data'
                    ])->withInput();
                }

                DB::connection('mysql_dev')->beginTransaction();

                $userId = session('userid') ?? auth()->id() ?? 1;
                $now    = now();


                $validRows = [];

                foreach ($seatOperations as $seat) {

                    $busSeatId = (int)($seat['bus_seat_id'] ?? 0);

                    if ($busSeatId <= 0) {
                        Log::warning('Skipped invalid bus seat row', $seat);
                        continue;
                    }

                    $operationDate = $seat['operation_date'] ?? null;

                    if (empty($operationDate)) {
                        Log::warning('Skipped missing operation date', $seat);
                        continue;
                    }

                    DB::connection('mysql_dev')
                        ->table('bus_seat_operation')
                        ->where('bus_seat_id', $busSeatId)
                        ->where('operation_date', $operationDate)
                        ->delete();

                    $row = [
                        'bus_seat_id'    => $busSeatId,
                        'seat_code'      => trim((string)($seat['seat_code'] ?? '')),
                        'seat_layout_id' => (int)($seat['seat_layout_id'] ?? 0),
                        'operation_date' => $operationDate,
                        'category'       => (int)($seat['category'] ?? 1), //1=open 2=block
                        'created_at'     => $now,
                        'created_by'     => $userId,
                        'updated_at'     => $now,
                        'updated_by'     => $userId
                    ];

                    $validRows[] = $row;
                }

                if (empty($validRows)) {
                    DB::connection('mysql_dev')->rollBack();

                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'No valid seat rows found.'
                    ])->withInput();
                }


                DB::connection('mysql_dev')
                    ->table('bus_seat_operation')
                    ->insert($validRows);

                foreach ($validRows as $row) {

                    app(CommonController::class)->auditLog(
                        'bus_seat_operation',
                        0,
                        'INSERT',
                        [],
                        $row
                    );
                }

                DB::connection('mysql_dev')->commit();

                session()->flash('level', 'success');
                session()->flash('message', 'Seat block data saved successfully.');

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::connection('mysql_dev')->rollBack();

            Log::error("Error in SeatBlockController@add", [
                'method' => $method,
                'error'  => $t->getMessage(),
                'line'   => $t->getLine()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addSeatBlock', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }


    public function getSeatLayoutByBus(Request $request)
    {
        try {

            $request->validate([
                'bus_id' => 'required|integer'
            ]);

            $bus = DB::connection('mysql_dev')
                ->table('odbusdev.bus')
                ->where('id', $request->bus_id)
                ->first();

            if (!$bus) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bus not found'
                ]);
            }


            $layoutId = (int) $bus->mst_seat_layout_name_id;

            if ($layoutId <= 0) {
                $layoutId = (int) $bus->seat_layout_type_id;
            }

            if ($layoutId <= 0) {
                $layoutId = 1;
            }

            $layout = DB::connection('mysql_dev')
                ->table('odbusmaster.mst_seat_layout_name')
                ->where('id', $layoutId)
                ->where('active_status', 1)
                ->first();

            if (!$layout) {
                return response()->json([
                    'status' => false,
                    'message' => 'Seat layout not found'
                ]);
            }

            $seats = DB::connection('mysql_dev')
                ->table('odbusmaster.mst_seats')
                ->where('seat_layout_name_id', $layoutId)
                ->orderBy('berth_type')
                ->orderBy('row_number')
                ->orderBy('col_number')
                ->get();

            $activeSeats = DB::connection('mysql_dev')
                ->table('odbusdev.bus_seats')
                ->where('bus_id', $bus->id)
                ->where('active_seats', 1)
                ->pluck('seat_code')
                ->map(function ($seat) {
                    return strtoupper(trim(preg_replace('/\s+/', '', $seat)));
                })
                ->toArray();


            $html = $this->buildSeatLayoutHtml(
                $seats,
                $activeSeats,
                $bus->id,
                $layoutId
            );

            return response()->json([
                'status'      => true,
                'layout_id'   => $layout->id,
                'layout_name' => $layout->layout_name,
                'rows'        => $layout->rows,
                'cols'        => $layout->cols,
                'tier'        => $layout->tier,
                'bus_id'      => $bus->id,
                'bus_name'    => $bus->name,
                'bus_number'  => $bus->bus_number,
                'html'        => $html
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function buildSeatLayoutHtml($seats, $activeSeats = [], $busId = 0, $layoutId = 0)
    {
        $layout = [
            'UPPER'  => [],
            'LOWER'  => [],
            'MIDDLE' => []
        ];

        $seatMap = DB::connection('mysql_dev')
            ->table('bus_seats')
            ->where('bus_id', $busId)
            ->get()
            ->keyBy(function ($row) {
                return strtoupper(trim(preg_replace('/\s+/', '', $row->seat_code)));
            });

        foreach ($seats as $seat) {

            if ($seat->berth_type == 1) {
                $deck = 'LOWER';
            } elseif ($seat->berth_type == 2) {
                $deck = 'UPPER';
            } else {
                $deck = 'MIDDLE';
            }

            $layout[$deck][$seat->row_number][$seat->col_number] = $seat;
        }

        foreach ($layout as $deck => $rows) {
            ksort($rows);

            foreach ($rows as $r => $cols) {
                ksort($cols);
                $rows[$r] = $cols;
            }

            $layout[$deck] = $rows;
        }

        $maxCols = ['UPPER' => 0, 'LOWER' => 0, 'MIDDLE' => 0];

        foreach ($layout as $deck => $rows) {
            foreach ($rows as $cols) {
                if (!empty($cols)) {
                    $maxCols[$deck] = max($maxCols[$deck], max(array_keys($cols)));
                }
            }
        }

        $normalizedActiveSeats = array_map(function ($v) {
            return strtoupper(trim(preg_replace('/\s+/', '', (string)$v)));
        }, $activeSeats);

        $html = '<div class="bus-layout">';

        foreach (['UPPER', 'MIDDLE', 'LOWER'] as $deck) {

            if (empty($layout[$deck])) continue;

            $html .= '<div class="berth-row">';
            $html .= '<div class="berth-label">' . ucfirst(strtolower($deck)) . ' Berth</div>';
            $html .= '<div class="layout-box" style="grid-template-columns:repeat(' . $maxCols[$deck] . ',42px)">';

            $skip = [];

            foreach ($layout[$deck] as $rIndex => $row) {

                foreach ($row as $cIndex => $seat) {

                    if (isset($skip[$rIndex][$cIndex])) continue;

                    $seatNo = trim((string)($seat->seat_text ?: $seat->seat_code));

                    if ((int)$seat->seat_class === 0 || $seatNo === '') {
                        $html .= '<div class="empty-seat"></div>';
                        continue;
                    }

                    $currentSeat = strtoupper(trim(preg_replace('/\s+/', '', $seatNo)));
                    $isActive = in_array($currentSeat, $normalizedActiveSeats, true);

                    $selected = $isActive ? 'selected-seat' : 'blocked-seat';
                    $click    = $isActive ? 'onclick="toggleSeat(this)"' : '';
                    $checked  = $isActive ? 'checked' : '';

                    /* GET REAL BUS SEAT ID */
                    $busSeat = DB::connection('mysql_dev')
                        ->table('bus_seats')
                        ->where('bus_id', $busId)
                        ->whereRaw('TRIM(UPPER(seat_code)) = ?', [
                            trim(strtoupper($seatNo))
                        ])
                        ->first();

                    $busSeatId = $busSeat->id ?? 0;

                    if ($seat->seat_class == 3) {

                        $class = 'bus-vertical-sleeper ' . $selected;

                        $html .= '
                    <label class="seat-wrap vertical-sleeper-wrap">
                        <input type="checkbox"
                            class="seat-checkbox"
                            name="seats[]"
                            value="' . $seatNo . '"
                            ' . $checked . '
                            hidden>

                        <span class="' . $class . '"
                              data-busseatid="' . $busSeatId . '"
                              data-layout="' . $layoutId . '"
                              ' . $click . '></span>

                        <span class="seat-number">' . $seatNo . '</span>
                    </label>';

                        $skip[$rIndex + 1][$cIndex] = true;
                    } elseif ($seat->seat_class == 2) {

                        $class = 'bus-sleeper ' . $selected;

                        $html .= '
                    <label class="seat-wrap sleeper-wrap">
                        <input type="checkbox"
                            class="seat-checkbox"
                            name="seats[]"
                            value="' . $seatNo . '"
                            ' . $checked . '
                            hidden>

                        <span class="' . $class . '"
                              data-busseatid="' . $busSeatId . '"
                              data-layout="' . $layoutId . '"
                              ' . $click . '></span>

                        <span class="seat-number">' . $seatNo . '</span>
                    </label>';
                    } else {

                        $class = 'bus-seat ' . $selected;

                        $html .= '
                    <label class="seat-wrap">
                        <input type="checkbox"
                            class="seat-checkbox"
                            name="seats[]"
                            value="' . $seatNo . '"
                            ' . $checked . '
                            hidden>

                        <span class="' . $class . '"
                              data-busseatid="' . $busSeatId . '"
                              data-layout="' . $layoutId . '"
                              ' . $click . '></span>

                        <span class="seat-number">' . $seatNo . '</span>
                    </label>';
                    }
                }
            }

            $html .= '</div></div>';
        }

        $html .= '</div>';

        return $html;
    }

    public function getBlockedSeatHistory(Request $request)
    {
        try {

            $busId = (int)$request->bus_id;

            if ($busId <= 0) {
                return response()->json([
                    'status' => false,
                    'html' => 'Invalid bus'
                ]);
            }

            $rows = DB::connection('mysql_dev')
                ->table('bus_seat_operation')
                ->selectRaw("
                operation_date,
                GROUP_CONCAT(
                    CASE WHEN category = 2 THEN seat_code END
                    ORDER BY seat_code SEPARATOR ', '
                ) as blocked_seats,
                MAX(updated_at) as updated_at,
                MAX(updated_by) as updated_by
            ")
                ->where('category', 2)
                ->whereIn('bus_seat_id', function ($q) use ($busId) {
                    $q->select('id')
                        ->from('bus_seats')
                        ->where('bus_id', $busId);
                })
                ->groupBy('operation_date')
                ->orderByDesc('operation_date')
                ->get();

            $html = '';

            if ($rows->isEmpty()) {
                $html = '<div class="text-center text-muted">No blocked history found</div>';
            } else {

                $html .= '
            <table class="table table-hover table-bordered table-sm align-middle">
                <thead class="table-secondary">
                    <tr>
                        <th>Sl No.</th>
                        <th>Date</th>
                        <th>Seats Blocked</th>
                        <th>Updated By</th>
                    </tr>
                </thead>
                <tbody>';

                $i = 1;

                foreach ($rows as $row) {

                    $html .= '
                <tr>
                    <td>' . $i++ . '</td>
                    <td>' . date('d-M-Y', strtotime($row->operation_date)) . '</td>
                    <td>' . ($row->blocked_seats ?: '-') . '</td>
                    <td>User ' . $row->updated_by . '<br>' .
                        date('d-M-Y H:i:s', strtotime($row->updated_at)) . '</td>
                </tr>';
                }

                $html .= '</tbody></table>';
            }

            return response()->json([
                'status' => true,
                'html'   => $html
            ]);
        } catch (\Throwable $e) {

            Log::error('Blocked Seat History Error', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'html'   => 'Unable to load history'
            ]);
        }
    }
}
