<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
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

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = request('selStatus');
            $operator = request('operator');
            $bus      = request('bus');
            $source      = request('source');
            $destination = request('destination');
            $fromDate = request('fromDate');
            $toDate   = request('toDate');
            $reason   = request('reason');


            $query = DB::connection('mysql_dev')
                ->table('bus_seat_operation as bso')
                ->whereNull('bso.deleted_at')

                ->join('bus_seats as bs', 'bs.id', '=', 'bso.bus_seat_id')
                ->join('bus as b', 'b.id', '=', 'bs.bus_id')

                ->leftJoin('odbusmaster.users as u', 'u.id', '=', 'b.bus_operator_id')

                ->leftJoin('bus_routes_map as brm', function ($join) {
                    $join->on('brm.bus_id', '=', 'b.id');
                })

                ->leftJoin('bus_routes as br', function ($join) {
                    $join->on('br.id', '=', 'brm.bus_route_id')
                        ->whereNull('br.deleted_at');
                })

                ->select(
                    'bso.id',
                    'bs.bus_id',

                    'u.organization_name as operator_name',

                    'b.name as bus_name',
                    'b.bus_number as bus_registration_no',

                    'br.route_name as route_name',

                    'bso.operation_date',
                    'bso.seat_code',
                    'bso.category',
                    'bso.reason',
                    'bso.created_at',
                    'bso.updated_at',

                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = bso.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = bso.updated_by LIMIT 1) as updated_by_name')
                )

                ->whereRaw("
        bso.id IN (
            SELECT MAX(id)
            FROM bus_seat_operation
            WHERE deleted_at IS NULL
            GROUP BY bus_seat_id, operation_date
        )
    ");

            if (!empty($txtSearch)) {

                $query->where(function ($q) use ($txtSearch) {

                    $q->where('u.organization_name', 'like', "%{$txtSearch}%")
                        ->orWhere('b.name', 'like', "%{$txtSearch}%")
                        ->orWhere('b.bus_number', 'like', "%{$txtSearch}%")
                        ->orWhere('bso.seat_code', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== null && $selStatus !== '') {
                $query->where('bso.category', $selStatus == 1 ? 2 : 1);
            }

            if (!empty($reason)) {

                $reasonName = DB::connection('mysql_dev')
                    ->table('odbusmaster.mst_annexture')
                    ->where('id', $reason)
                    ->value('annexture_name');

                if (!empty($reasonName)) {
                    $query->where('bso.reason', trim($reasonName));
                }
            }

            if (!empty($operator)) {
                $query->where('b.bus_operator_id', $operator);
            }

            if (!empty($source)) {
                $query->where('br.boarding_city_id', $source);
            }

            if (!empty($destination)) {
                $query->where('br.dropping_city_id', $destination);
            }

            if (!empty($bus)) {
                $query->where('bs.bus_id', $bus);
            }

            $today = date('Y-m-d');


            if (empty($fromDate) && empty($toDate)) {

                $query->whereDate('bso.operation_date', '>=', $today);
            } else {


                if (!empty($fromDate)) {
                    $query->whereDate('bso.operation_date', '>=', $fromDate);
                }

                if (!empty($toDate)) {
                    $query->whereDate('bso.operation_date', '<=', $toDate);
                }
            }

            $rows = $query
                ->orderBy('bso.operation_date', 'asc')
                ->orderBy('bs.bus_id', 'asc')
                ->get();

            $grouped = [];

            foreach ($rows as $row) {

                $key = $row->bus_id;

                if (!isset($grouped[$key])) {

                    $grouped[$key] = [
                        'id'            => $row->id,
                        'operator_name' => $row->operator_name ?: '--',
                        'bus_name'      => trim($row->bus_name . ' / ' . $row->bus_registration_no),
                        'route_name'    => $row->route_name ?: '--',
                        'block_info'    => [],
                        'enc_id' => Crypt::encryptString($row->bus_id),
                    ];
                }

                $dateKey = date('d-M-Y', strtotime($row->operation_date));

                if (!isset($grouped[$key]['block_info'][$dateKey])) {

                    $grouped[$key]['block_info'][$dateKey] = [
                        'date'       => $dateKey,
                        'seat_list'  => [],
                        'reason' => $row->reason ?: '--',
                        'created_by' => $row->updated_by_name ?: $row->created_by_name ?: '--',
                        'created_at' => date('d-M-Y H:i:s', strtotime($row->created_at)),
                        'enc_id' => Crypt::encryptString($row->bus_id . '|' . $row->operation_date)
                    ];
                }

                $grouped[$key]['block_info'][$dateKey]['seat_list'][] = $row->seat_code;
            }


            foreach ($grouped as &$item) {

                $formatted = [];

                foreach ($item['block_info'] as $r) {

                    $formatted[] = [
                        'date'       => $r['date'],
                        'seat_code'  => implode(', ', $r['seat_list']),
                        'reason'     => $r['reason'],
                        'created_by' => $r['created_by'],
                        'created_at' => $r['created_at'],
                        'enc_id'     => $r['enc_id']
                    ];
                }

                $item['block_info'] = $formatted;
            }

            $data = array_values($grouped);

            $recordsTotal = count($data);
            $recordsFiltered = $recordsTotal;
        } catch (\Throwable $t) {

            Log::error("SeatBlockController@DataTable", [
                'message' => $t->getMessage(),
                'line'    => $t->getLine()
            ]);

            return response()->json([
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => []
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
            $value = (!empty($encId)) ? Crypt::decryptString($encId) : '';

            if (!empty($value)) {

                $arr = explode('|', $value);

                $busId  = $arr[0] ?? 0;
                $opDate = $arr[1] ?? '';

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $editRow = DB::connection('mysql_dev')
                    ->table('bus_seat_operation as bso')
                    ->join('bus_seats as bs', 'bs.id', '=', 'bso.bus_seat_id')
                    ->join('bus as b', 'b.id', '=', 'bs.bus_id')
                    ->select(
                        'bso.reason',
                        'b.bus_operator_id',
                        'bs.bus_id'
                    )
                    ->where('bs.bus_id', $busId)
                    ->whereDate('bso.operation_date', $opDate)
                    ->whereNull('bso.deleted_at')
                    ->orderByDesc('bso.id')
                    ->first();

                $data['editDate'] = $opDate;
                $data['editData'] = $editRow;
            }

            $redirectPage = "admin/seat-block";

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'operator'        => 'required',
                    'bus'             => 'required',
                    'reason'          => 'required',
                    'seat_operations' => 'required'
                ], [
                    'operator.required'        => 'Please select operator',
                    'bus.required'             => 'Please select bus',
                    'reason.required'          => 'Please select reason',
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

                $userId   = session('userid') ?? auth()->id() ?? 1;
                $now      = now();
                $reasonId = request('reason');

                $reason = DB::connection('mysql_dev')
                    ->table('odbusmaster.mst_annexture as ma')
                    ->join('odbusmaster.mst_annexture_type as mat', 'mat.id', '=', 'ma.annexture_type_id')
                    ->where('ma.id', $reasonId)
                    ->where('mat.annexture_type', 'REASON')
                    ->where('ma.active_status', 1)
                    ->value('ma.annexture_name');

                $reason = $reason ?: 'Other';

                $validRows = [];

                foreach ($seatOperations as $seat) {

                    $busSeatId      = (int)($seat['bus_seat_id'] ?? 0);
                    $operationDate  = $seat['operation_date'] ?? null;

                    if ($busSeatId <= 0 || empty($operationDate)) {
                        continue;
                    }

                    $seatCode = trim((string)($seat['seat_code'] ?? ''));
                    $layoutId = (int)($seat['seat_layout_id'] ?? 0);
                    $category = (int)($seat['category'] ?? 2);

                    /* ONLY CHECK ACTIVE ROWS */
                    $existing = DB::connection('mysql_dev')
                        ->table('bus_seat_operation')
                        ->where('bus_seat_id', $busSeatId)
                        ->whereDate('operation_date', $operationDate)
                        ->whereNull('deleted_at')
                        ->orderByDesc('id')
                        ->first();

                    if ($existing) {
                        continue;
                    } else {

                        $validRows[] = [
                            'bus_seat_id'    => $busSeatId,
                            'seat_code'      => $seatCode,
                            'seat_layout_id' => $layoutId,
                            'operation_date' => $operationDate,
                            'category'       => $category,
                            'reason'         => $reason,
                            'created_at'     => $now,
                            'created_by'     => $userId,
                            'updated_at'     => $now,
                            'updated_by'     => $userId
                        ];
                    }
                }

                if (!empty($validRows)) {

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

    public function delete(Request $request)
    {
        try {

            $decoded = Crypt::decryptString($request->id);

            $parts = explode('|', $decoded);

            if (count($parts) < 2) {
                throw new \Exception('Invalid delete payload');
            }

            $busId = $parts[0];
            $operationDate = $parts[1];

            $userId = session('userid') ?? auth()->id() ?? 1;

            $updated = DB::connection('mysql_dev')
                ->table('bus_seat_operation')
                ->whereNull('deleted_at')
                ->whereDate('operation_date', $operationDate)
                ->whereIn('bus_seat_id', function ($q) use ($busId) {
                    $q->select('id')
                        ->from('bus_seats')
                        ->where('bus_id', $busId);
                })
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => $userId,
                    'updated_at' => now(),
                    'updated_by' => $userId
                ]);

            return response()->json([
                'status' => true,
                'message' => 'Deleted successfully'
            ]);
        } catch (\Throwable $t) {

            Log::error('SeatBlock delete error', [
                'message' => $t->getMessage(),
                'line' => $t->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Delete failed'
            ], 500);
        }
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

            $operationDate = $request->operation_date ?? null;

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

            $blockedSeats = [];

            if (!empty($operationDate)) {

                $blockedSeats = DB::connection('mysql_dev')
                    ->table('bus_seat_operation')
                    ->whereNull('deleted_at')
                    ->where('category', 2)
                    ->whereDate('operation_date', $operationDate)
                    ->whereIn('bus_seat_id', function ($q) use ($bus) {
                        $q->select('id')
                            ->from('bus_seats')
                            ->where('bus_id', $bus->id);
                    })
                    ->pluck('seat_code')
                    ->map(function ($seat) {
                        return strtoupper(trim(preg_replace('/\s+/', '', $seat)));
                    })
                    ->toArray();
            }


            $html = $this->buildSeatLayoutHtml(
                $seats,
                $activeSeats,
                $bus->id,
                $layoutId,
                $blockedSeats
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

    private function buildSeatLayoutHtml($seats, $activeSeats = [], $busId = 0, $layoutId = 0, $blockedSeats = [])
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

            $v = strtoupper(trim(preg_replace('/\s+/', '', (string)$v)));

            if (is_numeric($v)) {
                $v = (string)(int)$v;
            }

            return $v;
        }, $activeSeats);

        $normalizedBlockedSeats = array_map(function ($v) {

            $v = strtoupper(trim(preg_replace('/\s+/', '', (string)$v)));

            if (is_numeric($v)) {
                $v = (string)(int)$v;
            }

            return $v;
        }, $blockedSeats);

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

                    if (is_numeric($currentSeat)) {
                        $currentSeat = (string)(int)$currentSeat;
                    }

                    $isActive  = in_array($currentSeat, $normalizedActiveSeats, true);
                    $isBlocked = in_array($currentSeat, $normalizedBlockedSeats, true);

                    if ($isBlocked) {

                        $selected = 'blocked';
                        $checked  = '';
                        $click    = 'onclick="toggleSeat(this)"';
                    } elseif ($isActive) {

                        $selected = 'selected-seat';
                        $checked  = 'checked';
                        $click    = 'onclick="toggleSeat(this)"';
                    } else {

                        $selected = 'disabled';
                        $checked  = '';
                        $click    = '';
                    }


                    $busSeat = DB::connection('mysql_dev')
                        ->table('bus_seats')
                        ->where('bus_id', $busId)
                        ->whereRaw('TRIM(UPPER(seat_code)) = ?', [
                            trim(strtoupper($seatNo))
                        ])
                        ->first();

                    $busSeatId = $seatMap[$currentSeat]->id ?? 0;

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

            $busId = (int) $request->bus_id;
            $operationDate = $request->operation_date;

            if ($busId <= 0) {
                return response()->json([
                    'status' => false,
                    'html'   => 'Invalid bus'
                ]);
            }

            $query = DB::connection('mysql_dev')
                ->table('bus_seat_operation')
                ->whereNull('deleted_at')
                ->where('category', 2)
                ->whereIn('bus_seat_id', function ($q) use ($busId) {
                    $q->select('id')
                        ->from('bus_seats')
                        ->where('bus_id', $busId);
                })
                ->selectRaw("
                operation_date,
                GROUP_CONCAT(
                    seat_code
                    ORDER BY seat_code SEPARATOR ', '
                ) as blocked_seats,
                MAX(updated_at) as updated_at,
                MAX(updated_by) as updated_by
            ");

            /* EDIT MODE -> only selected date */
            if (!empty($operationDate)) {
                $query->whereDate('operation_date', $operationDate);
            }

            $rows = $query
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

    public function getBusCancelledDates(Request $request)
    {
        $busId = $request->bus_id;

        $dates = DB::connection('mysql_dev')
            ->table('bus_cancelled_date as bcd')
            ->join('bus_cancelled as bc', 'bc.id', '=', 'bcd.bus_cancelled_id')
            ->where('bc.bus_id', $busId)
            ->where('bc.active_status', 1)
            ->where('bcd.active_status', 1)
            ->whereNull('bc.deleted_at')
            ->whereNull('bcd.deleted_at')
            ->pluck('bcd.cancelled_date')
            ->map(function ($d) {
                return date('Y-m-d', strtotime($d));
            })
            ->toArray();

        return response()->json([
            'status' => true,
            'data'   => $dates
        ]);
    }
}
