<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;

class SeatOpenController extends Controller
{
    public function index()
    {
        return view('Master.viewSeatOpen');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch   = htmlEncode(request('txtSearch'));
            $operator    = request('operator');
            $bus         = request('bus');
            $source      = request('source');
            $destination = request('destination');
            $fromDate    = request('fromDate');
            $toDate      = request('toDate');
            $reason      = request('reason');

            $query = DB::connection('mysql_dev')
                ->table('bus_seat_operation as bso')
                ->whereNull('bso.deleted_at')
                ->where('bso.category', 1)

                ->leftJoin('bus as b', 'b.id', '=', 'bso.bus_id')
                ->leftJoin('bus_routes_map as brm', 'brm.bus_id', '=', 'b.id')
                ->leftJoin('bus_routes as br', function ($join) {
                    $join->on('br.id', '=', 'brm.bus_route_id')
                        ->whereNull('br.deleted_at');
                })

                ->select(
                    'bso.id',
                    'bso.bus_id',

                    DB::raw("(
                    SELECT organization_name
                    FROM odbusmaster.users
                    WHERE id = b.bus_operator_id
                    LIMIT 1
                ) as operator_name"),

                    'b.name as bus_name',
                    'b.bus_number as bus_registration_no',
                    'br.route_name',
                    'bso.operation_date',
                    'bso.seat_code',
                    'bso.reason',
                    'bso.created_at',

                    DB::raw("(
                    SELECT name
                    FROM odbusmaster.users
                    WHERE id = bso.created_by
                    LIMIT 1
                ) as created_by_name")
                );

            if (!empty($txtSearch)) {

                $query->where(function ($q) use ($txtSearch) {

                    $q->whereRaw("(
                    SELECT organization_name
                    FROM odbusmaster.users
                    WHERE id = b.bus_operator_id
                    LIMIT 1
                ) like ?", ["%{$txtSearch}%"])

                        ->orWhere('b.name', 'like', "%{$txtSearch}%")
                        ->orWhere('b.bus_number', 'like', "%{$txtSearch}%")
                        ->orWhere('bso.seat_code', 'like', "%{$txtSearch}%");
                });
            }

            if (!empty($operator)) {
                $query->where('b.bus_operator_id', $operator);
            }

            if (!empty($bus)) {
                $query->where('bso.bus_id', $bus);
            }

            if (!empty($source)) {
                $query->where('br.boarding_city_id', $source);
            }

            if (!empty($destination)) {
                $query->where('br.dropping_city_id', $destination);
            }

            if (!empty($reason)) {

                $reasonName = DB::connection('mysql_dev')
                    ->table('odbusmaster.mst_annexture')
                    ->where('id', $reason)
                    ->value('annexture_name');

                if (!empty($reasonName)) {
                    $query->where('bso.reason', $reasonName);
                }
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
                ->orderBy('bso.bus_id', 'asc')
                ->orderBy('bso.operation_date', 'asc')
                ->get();

            $grouped = [];

            foreach ($rows as $row) {

                /* GROUP ONLY BY BUS */
                $key = $row->bus_id;

                if (!isset($grouped[$key])) {

                    $grouped[$key] = [
                        'id'            => $row->id,
                        'operator_name' => $row->operator_name ?: '--',
                        'bus_name'      => trim(($row->bus_name ?: '--') . ' / ' . ($row->bus_registration_no ?: '--')),
                        'route_name'    => $row->route_name ?: '--',
                        'block_info'    => [],
                    ];
                }

                $dateKey = date('d-M-Y', strtotime($row->operation_date));

                if (!isset($grouped[$key]['block_info'][$dateKey])) {

                    $grouped[$key]['block_info'][$dateKey] = [
                        'date'       => $dateKey,
                        'seat_list'  => [],
                        'reason'     => $row->reason ?: '--',
                        'created_by' => $row->created_by_name ?: '--',
                        'created_at' => date('d-M-Y H:i:s', strtotime($row->created_at)),
                        'enc_id'     => Crypt::encryptString($row->bus_id . '|' . $row->operation_date),
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
                        'enc_id'     => $r['enc_id'],
                    ];
                }

                $item['block_info'] = $formatted;
            }

            $data = array_values($grouped);

            $recordsTotal = count($data);
            $recordsFiltered = $recordsTotal;
        } catch (\Throwable $t) {

            Log::error("SeatOpenController@DataTable", [
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
                    ->table('bus_seat_operation')
                    ->select('reason')
                    ->where('category', 1)
                    ->where('bus_id', $busId)
                    ->whereDate('operation_date', $opDate)
                    ->whereNull('deleted_at')
                    ->first();

                $busInfo = DB::connection('mysql_dev')
                    ->table('bus')
                    ->select('bus_operator_id', 'id')
                    ->where('id', $busId)
                    ->first();

                $data['editDate'] = $opDate;
                $data['editData'] = (object)[
                    'reason'          => $editRow->reason ?? '',
                    'bus_operator_id' => $busInfo->bus_operator_id ?? '',
                    'bus_id'          => $busInfo->id ?? ''
                ];
            }

            $redirectPage = "admin/seat-open";

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'operator'        => 'required',
                    'bus'             => 'required',
                    'reason'          => 'required',
                    'seat_operations' => 'required'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                $seatOperations = json_decode(request('seat_operations'), true);

                if ($method == 'Edit' && empty($seatOperations)) {

                    DB::connection('mysql_dev')->beginTransaction();

                    $userId = session('userid') ?? auth()->id() ?? 1;
                    $now    = now();

                    $rows = DB::connection('mysql_dev')
                        ->table('bus_seat_operation')
                        ->whereNull('deleted_at')
                        ->where('category', 1)
                        ->where('bus_id', $busId)
                        ->whereDate('operation_date', $opDate)
                        ->get();

                    DB::connection('mysql_dev')
                        ->table('bus_seat_operation')
                        ->whereNull('deleted_at')
                        ->where('category', 1)
                        ->where('bus_id', $busId)
                        ->whereDate('operation_date', $opDate)
                        ->update([
                            'deleted_at' => $now,
                            'deleted_by' => $userId,
                            'updated_at' => $now,
                            'updated_by' => $userId
                        ]);

                    foreach ($rows as $seat) {

                        DB::connection('mysql_dev')
                            ->table('odbuslog.bus_seat_operation_log')
                            ->insert([
                                'bus_seat_operation_id' => $seat->id,
                                'bus_seat_id'           => $seat->bus_seat_id,
                                'bus_id'                => $seat->bus_id,
                                'seat_code'             => $seat->seat_code,
                                'seat_layout_id'        => $seat->seat_layout_id,
                                'operation_date'        => $seat->operation_date,
                                'category'              => $seat->category,
                                'action'                => 3,
                                'old_category'          => 2,
                                'new_category'          => 1,
                                'reason'                => $seat->reason,
                                'created_at'            => $now,
                                'created_by'            => $userId
                            ]);
                    }

                    DB::connection('mysql_dev')->commit();

                    session()->flash('level', 'success');
                    session()->flash('message', 'All opened seats removed successfully.');

                    return redirect($redirectPage);
                }

                if (!is_array($seatOperations)) {
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Invalid seat data'
                    ])->withInput();
                }

                DB::connection('mysql_dev')->beginTransaction();

                $userId   = session('userid') ?? auth()->id() ?? 1;
                $now      = now();
                $busId    = request('bus');
                $reasonId = request('reason');

                $reason = DB::connection('mysql_dev')
                    ->table('odbusmaster.mst_annexture as ma')
                    ->join(
                        'odbusmaster.mst_annexture_type as mat',
                        'mat.id',
                        '=',
                        'ma.annexture_type_id'
                    )
                    ->where('mat.annexture_type', 'REASON')
                    ->where('ma.annexture_value', $reasonId)
                    ->where('ma.active_status', 1)
                    ->value('ma.annexture_name');

                $reason = $reason ?: 'Other';

                $layoutId = $seatOperations[0]['seat_layout_id'] ?? 0;

                $validRows = [];

                foreach ($seatOperations as $seat) {

                    $operationDate = $seat['operation_date'] ?? null;
                    $seatCode      = trim((string)($seat['seat_code'] ?? ''));

                    if (!$operationDate || $seatCode == '') {
                        continue;
                    }


                    $mstSeatId = DB::connection('mysql_dev')
                        ->table('odbusmaster.mst_seats')
                        ->where('seat_layout_name_id', $layoutId)
                        ->where(function ($q) use ($seatCode) {
                            $q->whereRaw('TRIM(CAST(seat_text AS CHAR)) = ?', [$seatCode])
                                ->orWhereRaw('TRIM(CAST(seat_code AS CHAR)) = ?', [$seatCode]);
                        })
                        ->value('id');

                    if (!$mstSeatId) {
                        continue;
                    }


                    $isPermanent = DB::connection('mysql_dev')
                        ->table('bus_seats')
                        ->where('bus_id', $busId)
                        ->where('active_seats', 1)
                        ->whereRaw('TRIM(CAST(seat_code AS CHAR)) = ?', [$seatCode])
                        ->exists();

                    if ($isPermanent) {
                        continue;
                    }

                    $exists = DB::connection('mysql_dev')
                        ->table('bus_seat_operation')
                        ->whereNull('deleted_at')
                        ->where('bus_id', $busId)
                        ->where('category', 1)
                        ->where('seat_code', $seatCode)
                        ->whereDate('operation_date', $operationDate)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    $validRows[] = [
                        'bus_id'         => $busId,
                        'bus_seat_id'    => $mstSeatId,
                        'seat_code'      => $seatCode,
                        'seat_layout_id' => $layoutId,
                        'operation_date' => $operationDate,
                        'category'       => 1,
                        'reason'         => $reason,
                        'created_at'     => $now,
                        'created_by'     => $userId,
                        'updated_at'     => $now,
                        'updated_by'     => $userId
                    ];
                }

                if ($method == 'Edit') {

                   $editDate = $seatOperations[0]['operation_date'] ?? ($opDate ?? null);

                    if ($editDate) {

                        $selectedSeats = collect($seatOperations)
                            ->pluck('seat_code')
                            ->map(fn($v) => trim((string)$v))
                            ->filter()
                            ->values()
                            ->toArray();

                        $query = DB::connection('mysql_dev')
                            ->table('bus_seat_operation')
                            ->where('category', 1)
                            ->where('bus_id', $busId)
                            ->whereDate('operation_date', $editDate)
                            ->whereNull('deleted_at');

                        if (!empty($selectedSeats)) {
                            $query->whereNotIn('seat_code', $selectedSeats);
                        }

                        $removedRows = $query->get();

                        $query->update([
                            'deleted_at' => $now,
                            'deleted_by' => $userId,
                            'updated_at' => $now,
                            'updated_by' => $userId
                        ]);

                        foreach ($removedRows as $seat) {

                            DB::connection('mysql_dev')
                                ->table('odbuslog.bus_seat_operation_log')
                                ->insert([
                                    'bus_seat_operation_id' => $seat->id,
                                    'bus_seat_id'           => $seat->bus_seat_id,
                                    'bus_id'                => $seat->bus_id,
                                    'seat_code'             => $seat->seat_code,
                                    'seat_layout_id'        => $seat->seat_layout_id,
                                    'operation_date'        => $seat->operation_date,
                                    'category'              => $seat->category,
                                    'action'                => 3,
                                    'old_category'          => 2,
                                    'new_category'          => 1,
                                    'reason'                => $seat->reason,
                                    'created_at'            => $now,
                                    'created_by'            => $userId
                                ]);
                        }
                    }
                }

                /*
            Insert only new seats
            */
                if (!empty($validRows)) {

                    foreach ($validRows as $row) {

                        $insertId = DB::connection('mysql_dev')
                            ->table('bus_seat_operation')
                            ->insertGetId($row);

                        /*
        Default:
        New seat opened in Add
        */
                        $action      = 1;
                        $oldCategory = null;
                        $newCategory = 1;

                        /*
        Check previously unopened then reopened
        */
                        $deletedRecord = DB::connection('mysql_dev')
                            ->table('bus_seat_operation')
                            ->where('bus_id', $row['bus_id'])
                            ->where('category', 1)
                            ->where('seat_code', $row['seat_code'])
                            ->whereDate('operation_date', $row['operation_date'])
                            ->whereNotNull('deleted_at')
                            ->orderByDesc('id')
                            ->first();

                        if ($deletedRecord) {

                            /*
            Re-open already unopened seat
            */
                            $action = 4;
                            $oldCategory = 1;
                            $newCategory = 2;
                        } elseif ($method == 'Edit') {

                            /*
            New seat opened in edit
            */
                            $action = 2;
                            $oldCategory = null;
                            $newCategory = 2;
                        }

                        DB::connection('mysql_dev')
                            ->table('odbuslog.bus_seat_operation_log')
                            ->insert([
                                'bus_seat_operation_id' => $insertId,
                                'bus_seat_id'           => $row['bus_seat_id'],
                                'bus_id'                => $row['bus_id'],
                                'seat_code'             => $row['seat_code'],
                                'seat_layout_id'        => $row['seat_layout_id'],
                                'operation_date'        => $row['operation_date'],
                                'category'              => $row['category'],
                                'action'                => $action,
                                'old_category'          => $oldCategory,
                                'new_category'          => $newCategory,
                                'reason'                => $row['reason'],
                                'created_at'            => $now,
                                'created_by'            => $userId
                            ]);

                        app(CommonController::class)->auditLog(
                            'bus_seat_operation',
                            $insertId,
                            'INSERT',
                            [],
                            $row
                        );
                    }
                }

                DB::connection('mysql_dev')->commit();

                session()->flash('level', 'success');
                session()->flash('message', 'Seat open data saved successfully.');

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::connection('mysql_dev')->rollBack();

            Log::error("Error in SeatOpenController@add", [
                'method' => $method,
                'error'  => $t->getMessage(),
                'line'   => $t->getLine()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addSeatOpen', compact('data'));
    }

    public function delete(Request $request)
    {
        try {

            $decoded = Crypt::decryptString($request->id);
            $parts   = explode('|', $decoded);

            if (count($parts) < 2) {
                throw new \Exception('Invalid payload');
            }

            $busId         = $parts[0];
            $operationDate = $parts[1];

            $userId = session('userid') ?? auth()->id() ?? 1;
            $now    = now();

            DB::connection('mysql_dev')->beginTransaction();

            /*
        Get active opened seats first for log
        Same as unopen logic
        */
            $rows = DB::connection('mysql_dev')
                ->table('bus_seat_operation')
                ->whereNull('deleted_at')
                ->where('category', 1)
                ->where('bus_id', $busId)
                ->whereDate('operation_date', $operationDate)
                ->get();

            /*
        Soft delete opened seats
        */
            $updated = DB::connection('mysql_dev')
                ->table('bus_seat_operation')
                ->whereNull('deleted_at')
                ->where('category', 1)
                ->where('bus_id', $busId)
                ->whereDate('operation_date', $operationDate)
                ->update([
                    'deleted_at' => $now,
                    'deleted_by' => $userId,
                    'updated_at' => $now,
                    'updated_by' => $userId
                ]);

            /*
        Log as Unopen
        Action = 3
        old_category = 2
        new_category = 1
        */
            foreach ($rows as $row) {

                DB::connection('mysql_dev')
                    ->table('odbuslog.bus_seat_operation_log')
                    ->insert([
                        'bus_seat_operation_id' => $row->id,
                        'bus_seat_id'           => $row->bus_seat_id,
                        'bus_id'                => $row->bus_id,
                        'seat_code'             => $row->seat_code,
                        'seat_layout_id'        => $row->seat_layout_id,
                        'operation_date'        => $row->operation_date,
                        'category'              => $row->category,
                        'action'                => 3,
                        'old_category'          => 2,
                        'new_category'          => 1,
                        'reason'                => $row->reason,
                        'created_at'            => $now,
                        'created_by'            => $userId
                    ]);

                app(CommonController::class)->auditLog(
                    'bus_seat_operation',
                    $row->id,
                    'DELETE',
                    (array)$row,
                    []
                );
            }

            DB::connection('mysql_dev')->commit();

            return response()->json([
                'status'  => $updated > 0,
                'message' => $updated > 0
                    ? 'Deleted successfully'
                    : 'No matching records found'
            ]);
        } catch (\Throwable $t) {

            DB::connection('mysql_dev')->rollBack();

            Log::error('SeatOpen delete error', [
                'message' => $t->getMessage(),
                'line'    => $t->getLine()
            ]);

            return response()->json([
                'status'  => false,
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

            $busId = (int) $request->bus_id;
            $operationDate = $request->operation_date ?? '';

            $bus = DB::connection('mysql_dev')
                ->table('bus')
                ->where('id', $busId)
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
                ->first();

            $seats = DB::connection('mysql_dev')
                ->table('odbusmaster.mst_seats')
                ->where('seat_layout_name_id', $layoutId)
                ->orderBy('berth_type')
                ->orderBy('row_number')
                ->orderBy('col_number')
                ->get();

            /* GREY seats = permanent active seats */
            $activeSeats = DB::connection('mysql_dev')
                ->table('bus_seats')
                ->where('bus_id', $busId)
                ->where('active_seats', 1)
                ->pluck('seat_code')
                ->map(fn($v) => strtoupper(trim($v)))
                ->toArray();

            /* BLUE seats = already opened for selected date */
            $openedSeats = [];

            if (!empty($operationDate)) {

                $openedSeats = DB::connection('mysql_dev')
                    ->table('bus_seat_operation')
                    ->whereNull('deleted_at')
                    ->where('category', 1)
                    ->where('bus_id', $busId)
                    ->whereDate('operation_date', $operationDate)
                    ->pluck('seat_code')
                    ->map(fn($v) => strtoupper(trim($v)))
                    ->toArray();
            }

            $html = $this->buildSeatLayoutHtml(
                $seats,
                $activeSeats,
                $busId,
                $layoutId,
                $openedSeats
            );

            return response()->json([
                'status'      => true,
                'layout_id'   => $layoutId,
                'layout_name' => $layout->layout_name ?? '',
                'bus_id'      => $busId,
                'bus_name'    => $bus->name,
                'bus_number'  => $bus->bus_number,
                'html'        => $html
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
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

            $v = strtoupper(trim(preg_replace('/\s+/', '', (string) $v)));

            if (is_numeric($v)) {
                $v = (string) ((int) $v);
            }

            return $v;
        }, $activeSeats);

        $normalizedBlockedSeats = array_map(function ($v) {

            $v = strtoupper(trim(preg_replace('/\s+/', '', (string) $v)));

            if (is_numeric($v)) {
                $v = (string) ((int) $v);
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

                    $seatNo = trim((string) ($seat->seat_text ?: $seat->seat_code));

                    if ((int) $seat->seat_class === 0 || $seatNo === '') {
                        $html .= '<div class="empty-seat"></div>';
                        continue;
                    }


                    $seatCodeUpper = strtoupper(trim($seatNo));

                    if (str_contains($seatCodeUpper, 'EXIT')) {

                        $img = ($seat->seat_class == 3)
                            ? asset('assets/seats/exit_vertical.png')
                            : asset('assets/seats/exit_horizontal.png');

                        $wrapperClass = 'seat-wrap';

                        if ($seat->seat_class == 3) {
                            $wrapperClass .= ' vertical-sleeper-wrap';
                        } elseif ($seat->seat_class == 2) {
                            $wrapperClass .= ' sleeper-wrap';
                        }

                        $html .= '
                        <label class="' . $wrapperClass . ' exit-seat">

                            <span class="seat-img-container">
                                <img src="' . $img . '" class="seat-img-full" />
                            </span>

                            <span class="seat-number">EXIT</span>

                        </label>';

                        continue;
                    }

                    
                    $currentSeat = strtoupper(trim(preg_replace('/\s+/', '', $seatNo)));

                    if (is_numeric($currentSeat)) {
                        $currentSeat = (string) ((int) $currentSeat);
                    }

                    $isActive = in_array($currentSeat, $normalizedActiveSeats, true);
                    $isOpened = in_array($currentSeat, $normalizedBlockedSeats, true);

                    /*
                Seat Open Logic

                active seats      = grey locked
                opened seats      = blue
                remaining seats   = white clickable
                */

                    if ($isActive) {

                        $selected = 'disabled';
                        $checked  = '';
                        $click    = '';
                    } elseif ($isOpened) {

                        $selected = 'selected-seat';
                        $checked  = 'checked';
                        $click    = 'onclick="toggleSeat(this)"';
                    } else {

                        $selected = 'openable';
                        $checked  = '';
                        $click    = 'onclick="toggleSeat(this)"';
                    }

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
                              data-seatcode="' . $seatNo . '"
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
                              data-seatcode="' . $seatNo . '"
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
                              data-seatcode="' . $seatNo . '"
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

    public function getOpenedSeatHistory(Request $request)
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
                ->where('category', 1) // Seat Open
                ->where('bus_id', $busId)
                ->selectRaw("
                operation_date,
                GROUP_CONCAT(
                    seat_code
                    ORDER BY seat_code SEPARATOR ', '
                ) as opened_seats,
                MAX(reason) as reason,
                MAX(updated_at) as updated_at,
                MAX(updated_by) as updated_by
            ");

            /* EDIT MODE */
            if (!empty($operationDate)) {
                $query->whereDate('operation_date', $operationDate);
            }

            $rows = $query
                ->groupBy('operation_date')
                ->orderByDesc('operation_date')
                ->get();

            $html = '';

            if ($rows->isEmpty()) {

                $html = '
                <div class="text-center text-muted">
                    No opened history found
                </div>
            ';
            } else {

                $html .= '
            <table class="table table-hover table-bordered table-sm align-middle">
                <thead class="table-secondary">
                    <tr>
                        <th>Sl No.</th>
                        <th>Date</th>
                        <th>Seats Opened</th>
                        <th>Reason</th>
                        <th>Updated By</th>
                    </tr>
                </thead>
                <tbody>
            ';

                $i = 1;

                foreach ($rows as $row) {

                    $html .= '
                <tr>
                    <td>' . $i++ . '</td>

                    <td>' .
                        date('d-M-Y', strtotime($row->operation_date))
                        . '</td>

                    <td>' .
                        ($row->opened_seats ?: '-')
                        . '</td>

                    <td>' .
                        ($row->reason ?: '-')
                        . '</td>

                    <td>User ' .
                        $row->updated_by .
                        '<br>' .
                        date('d-M-Y H:i:s', strtotime($row->updated_at))
                        . '</td>
                </tr>';
                }

                $html .= '
                </tbody>
            </table>';
            }

            return response()->json([
                'status' => true,
                'html'   => $html
            ]);
        } catch (\Throwable $e) {

            Log::error('Opened Seat History Error', [
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
