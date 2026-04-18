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

                $redirectPage = "admin/ticketfareslab-info/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';


                $row = DB::table('mst_ticket_fare_slab_info as t')
                    ->leftJoin('users as u', 'u.id', '=', 't.bus_operator_id')
                    ->where('t.slab_id', $id)
                    ->select(
                        't.*',
                        'u.organization_name as operator_name'
                    )
                    ->get();

                if ($row->isEmpty()) {
                    return redirect('ticketfareslab-info');
                }

                $operators = [];
                $slabInfo = [];

                foreach ($row as $r) {

                    // operators
                    $operators[$r->bus_operator_id] = [
                        'id' => $r->bus_operator_id,
                        'name' => $r->operator_name
                    ];

                    // slab rows 
                    $key = md5(
                        $r->starting_fare . '|' .
                            $r->upto_fare . '|' .
                            $r->commision . '|' .
                            date('Y-m-d', strtotime($r->from_date)) . '|' .
                            date('Y-m-d', strtotime($r->to_date))
                    );

                    if (!isset($slabInfo[$key])) {
                        $slabInfo[$key] = [
                            'starting_fare' => (string)$r->starting_fare,
                            'upto_fare' => (string)$r->upto_fare,
                            'commision' => (string)$r->commision,
                            'from_date' => date('Y-m-d', strtotime($r->from_date)),
                            'to_date' => date('Y-m-d', strtotime($r->to_date)),
                        ];
                    }
                }


                $slabInfo = array_values($slabInfo);

                $data['row'] = [
                    'slab_id' => $id,
                    'operators' => array_values($operators),
                    'slabInfo' => $slabInfo
                ];;

                if ($row->isEmpty()) {
                    return redirect('ticketfareslab-info');
                }
            } else {
                $id = 0;
                $redirectPage = "admin/ticketfareslab-info";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());


                $validator = Validator::make(request()->all(), [
                    'slab_id' => 'bail|required|integer',
                    'bus_operator_id' => 'nullable',

                    'starting_fare.*' => 'required|numeric|min:0',
                    'upto_fare.*'     => 'required|numeric',
                    'commision.*'     => 'required|numeric',

                    'from_date.*' => 'nullable|date',
                    'to_date.*'   => 'nullable|date',
                ], [
                    'slab_id.required' => 'Please select slab',
                    'bus_operator_id.required' => 'Please select at least one operator',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();


                $slab_id = (int) request('slab_id');
                $bus_operator_id = request('bus_operator_id');

                $operators = !empty($bus_operator_id)
                    ? explode(',', $bus_operator_id)
                    : [0];

                $starting_fare = request('starting_fare');
                $upto_fare     = request('upto_fare');
                $commision     = request('commision');
                $from_date     = request('from_date');
                $to_date       = request('to_date');

                foreach ($starting_fare as $i => $start) {
                    if ($upto_fare[$i] < $start) {
                        DB::rollBack();
                        return back()->with([
                            'level' => 'danger',
                            'message' => 'To Fare must be greater than or equal to From Fare'
                        ])->withInput();
                    }

                    if ($to_date[$i] < $from_date[$i]) {
                        DB::rollBack();
                        return back()->with([
                            'level' => 'danger',
                            'message' => 'To Date must be after From Date'
                        ])->withInput();
                    }
                }

                if ($id > 0) {
                    DB::table('mst_ticket_fare_slab_info')
                        ->where('slab_id', $slab_id)
                        ->delete();
                }


                $insertData = [];

                foreach ($operators as $operator) {

                    foreach ($starting_fare as $key => $val) {

                        $rowData = [
                            'slab_id'         => $slab_id,
                            'bus_operator_id' => (int) $operator,
                            'starting_fare'   => $starting_fare[$key],
                            'upto_fare'       => $upto_fare[$key],
                            'commision'       => $commision[$key],
                            'from_date'       => $from_date[$key],
                            'to_date'         => $to_date[$key],
                            'active_status'   => 1,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];

                        $insertData[] = $rowData;

                        app(CommonController::class)->auditLog(
                            'mst_ticket_fare_slab_info',
                            null,
                            ($id > 0 ? 'UPDATE' : 'INSERT'),
                            [],
                            $rowData
                        );
                    }
                }

                DB::table('mst_ticket_fare_slab_info')->insert($insertData);

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Ticket Fare Slab Info ' . ($id > 0 ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error in TicketFareSlabInfoController@add", [
                'method' => $method,
                'error'  => $t->getMessage()
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
                ->table('bus')
                ->where('id', $request->bus_id)
                ->whereNull('deleted_at')
                ->first();

            if (!$bus) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Bus not found'
                ]);
            }


            $layoutId = $bus->seat_layout_type_id;

            if (!$layoutId) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Seat layout not assigned to this bus'
                ]);
            }

            $layout = DB::connection('mysql_dev')
                ->table('odbusmaster.mst_seat_layout_name')
                ->where('id', $layoutId)
                ->where('active_status', 1)
                ->first();

            if (!$layout) {
                return response()->json([
                    'status'  => false,
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

            if ($seats->count() == 0) {
                return response()->json([
                    'status'  => false,
                    'message' => 'No seat mapping found'
                ]);
            }


            $html = $this->buildSeatLayoutHtml($seats);


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
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Invalid bus id'
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }



    private function buildSeatLayoutHtml($seats)
    {
        $layout = [
            'UPPER' => [],
            'LOWER' => []
        ];

        foreach ($seats as $seat) {
            $deck = $seat->berth_type == 1 ? 'LOWER' : 'UPPER';
            $layout[$deck][$seat->row_number][$seat->col_number] = $seat;
        }

        foreach ($layout as $deck => $rows) {
            ksort($rows);

            foreach ($rows as $rowKey => $cols) {
                ksort($cols);
                $rows[$rowKey] = $cols;
            }

            $layout[$deck] = $rows;
        }

        $maxCols = ['UPPER' => 0, 'LOWER' => 0];

        foreach ($layout as $deck => $rows) {
            foreach ($rows as $cols) {
                if (!empty($cols)) {
                    $maxCols[$deck] = max($maxCols[$deck], max(array_keys($cols)));
                }
            }
        }

        $html = '<div class="bus-layout">';

        foreach (['UPPER', 'LOWER'] as $deck) {

            if (empty($layout[$deck])) continue;

            $html .= '<div class="berth-row">';
            $html .= '<div class="berth-label">' . ucwords(strtolower($deck)) . ' Berth</div>';
            $html .= '<div class="layout-box" style="grid-template-columns:repeat(' . $maxCols[$deck] . ',42px)">';

            $skip = [];

            foreach ($layout[$deck] as $rIndex => $row) {

                foreach ($row as $cIndex => $seat) {

                    if (isset($skip[$rIndex][$cIndex])) continue;

                    if ($seat->seat_class == 0 || empty($seat->seat_text)) {
                        $html .= '<div class="empty-seat"></div>';
                    } elseif ($seat->seat_class == 3) {

                        $text = strtoupper($seat->seat_text);

                        $class = ($text == 'EXIT')
                            ? 'vertical_exit_prv'
                            : (($text == 'TOILET')
                                ? 'vertical_toilet_prv'
                                : 'bus-vertical-sleeper');

                        $html .= '
                    <label class="seat-wrap vertical-sleeper-wrap">
                        <span class="' . $class . '"></span>
                        <span class="seat-number">' . $seat->seat_text . '</span>
                    </label>';

                        $skip[$rIndex + 1][$cIndex] = true;
                    } elseif ($seat->seat_class == 2) {

                        $text = strtoupper($seat->seat_text);

                        $class = ($text == 'EXIT')
                            ? 'horizontal_exit_prv'
                            : (($text == 'TOILET')
                                ? 'horizontal_toilet_prv'
                                : 'bus-sleeper');

                        $html .= '
                    <label class="seat-wrap sleeper-wrap">
                        <span class="' . $class . '"></span>
                        <span class="seat-number">' . $seat->seat_text . '</span>
                    </label>';
                    } else {

                        $class = strtoupper($seat->seat_text) == 'EXIT'
                            ? 'seat_exit_prv'
                            : 'bus-seat';

                        $html .= '
                    <label class="seat-wrap">
                        <span class="' . $class . '"></span>
                        <span class="seat-number">' . $seat->seat_text . '</span>
                    </label>';
                    }
                }
            }

            $html .= '</div></div>';
        }

        $html .= '</div>';

        return $html;
    }
}
