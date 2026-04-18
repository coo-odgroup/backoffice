<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\SeatLayoutName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Validator;

class SeatLayoutController extends Controller
{
    public function index()
    {
        return view('master.seatLayout');
    }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $classSearch = (request('classSearch') !== null && request('classSearch') !== '') ? (int)request('classSearch') : '';

            $dataQuery = DB::table('mst_seat_layout_name as bt')
                ->select(
                    'bt.id as bustype_id',
                    'bt.layout_name',
                    'bt.created_at',
                    'bt.created_by',
                    'bt.updated_at',
                    'bt.updated_by',
                    'bt.active_status',
                    DB::raw('(SELECT name FROM users WHERE id = bt.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = bt.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('bt.bus_type', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($classSearch) && $classSearch != '') {
                $dataQuery->where('bt.class_id', $classSearch);
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('bt.active_status', $selStatus);
            }

            $count = $dataQuery->count('bt.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            // if (!empty(request('order'))) {

            //     $columns = [2 => 'bt.bus_type', 3 => 'bt.created_at', 4 => 'bt.created_by', 5 => 'bt.active_status'];

            //     $orderBy       = request('order');
            //     $orderColumn   = $columns[$orderBy[0]['column']] ?? 'bt.bus_type';
            //     $orderType     = $orderBy[0]['dir'];
            // } else {
            //     $orderColumn = 'bt.bus_type';
            //     $orderType   = 'asc';
            // }

            // $dataQuery = $dataQuery->orderBy($orderColumn, $orderType);

            // Pagination
            if ($length == -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery->limit($length)
                    ->offset($start)
                    ->get();
            }
            // Format Data
            if (count($arrRes) > 0) {

                foreach ($arrRes as $val) {
                    $val->created_date  = date('d-M-Y H:i:s', strtotime($val->created_at));
                    $val->updated_date  = ($val->updated_at != null) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : null;
                    $val->is_active     = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_bustype_id   = Crypt::encryptString($val->bustype_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in BusTypeController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'BusTypeController',
                'Method'     => 'dataTableView',
                'Error'      => $errorMsg
            ]);

            $recordsTotal     = 0;
            $recordsFiltered  = 0;
            $data            = [];
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
        $data['strPage'] = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        try{

             $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

             $data['seats'] = '';

             if ($id > 0) {

                $redirectPage = "admin/seat-layout/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = SeatLayoutName::select('id', 'layout_name','rows','cols','tier')
                                            ->where('id', $id)
                                            ->first();
                                           
                if (empty($dataResQry)) {
                    return redirect("seat-layout");
                }

                $seats = DB::table('mst_seats')
                            ->select('seat_class','berth_type','seat_text','seat_code','row_number','col_number','is_window','is_aisle')
                            ->where('seat_layout_name_id', $id)
                            ->get();
                            

                $data['seats'] = $seats;

                $data['row'] = $dataResQry;

             } else {
                $id = 0;
                $redirectPage = "admin/seat-layout";
             }

            if (request()->isMethod('post')) {

                $exists = SeatLayoutName::where('layout_name', request()->layout_name)->exists();

                if ($exists) {
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Layout Name already exists'
                    ])->withInput();
                }

                DB::beginTransaction();

                $layoutName = htmlEncode(request()->layout_name);
                $rows = (int)(request()->rows);
                $cols = (int)(request()->cols);
                $busTier = (int)(request()->busTier);

                $parentData = [
                    "layout_name"   => $layoutName,
                    "rows"          => $rows,
                    "cols"          => $cols,
                    "tier"          => $busTier,
                    "active_status" => 1,
                    "created_by"    => 1
                ];

                app(CommonController::class)->auditLog(
                    'mst_seat_layout_name',
                    null,
                    'INSERT',
                    [],
                    $parentData
                );

                $seat_layout_name = SeatLayoutName::create($parentData);

                $seats = json_decode(request()->seat_layout_json, true);

                $windowSeatInput = json_decode(request()->window_seat, true) ?? [];
                $windowSeats = array_column($windowSeatInput, 'value');

                $insertSeats = [];

                foreach ($seats as $seat) {

                    $seatText = (string) $seat['seat_text'];

                    $row = [
                        "seat_layout_name_id" => $seat_layout_name->id,
                        "seat_class" => $seat['seat_class'],
                        "berth_type" => $seat['berth_type'],
                        "seat_text" => $seat['seat_text'],
                        "row_number" => $seat['row_number'],
                        "col_number" => $seat['col_number'],
                        "is_window" => in_array($seatText, $windowSeats) ? 1 : 0,
                        "is_aisle" => is_null($seat['seat_text']) ? 1 : 0,
                        "created_by" => 1
                    ];

                    $insertSeats[] = $row;
                }

                DB::table('mst_seats')->insert($insertSeats);

                app(CommonController::class)->auditLog(
                    'mst_seats',
                    $seat_layout_name->id,
                    'INSERT',
                    [],
                    $insertSeats
                );

                DB::commit();

                return back()->with([
                    'level' => 'success',
                    'message' => 'Seat Layout Saved Successfully'
                ]);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'SeatLayoutController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addSeatLayout', compact('data'));
    }
    

    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function checkLayoutName(Request $request)
    {
        $exists = SeatLayoutName::where('layout_name', $request->layout_name)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }

    public function getSeatLyoutPreview(Request $request)
    {
        $seatLayoutId =  Crypt::decryptString($request->id);

        $seats = DB::table('mst_seats')
            ->where('seat_layout_name_id', $seatLayoutId)
            ->orderBy('row_number')
            ->orderBy('col_number')
            ->get();

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

        // BUILD HTML
        $html = '<div class="bus-layout">';

        foreach (['UPPER', 'LOWER'] as $deck) {

            if(empty($layout[$deck])){
                continue;
            }

            $html .= '<div class="berth-row">';
            $html .= '<div class="berth-label">' . ucwords(strtolower($deck)) . ' Berth</div>';
            $html .= '<div class="layout-box" style="grid-template-columns: repeat(' . $maxCols[$deck] . ', 42px);">';

            $skip = [];

            foreach ($layout[$deck] as $rIndex => $row) {
                foreach ($row as $cIndex => $seat) {

                    if(isset($skip[$rIndex][$cIndex])){
                        continue;
                    }

                    // EMPTY
                    if ($seat->seat_class == 0 || $seat->seat_text == null) {
                        $html .= '<div class="empty-seat"></div>';
                    }

                    // VERTICAL (EXIT / TOILET / NORMAL)
                    elseif ($seat->seat_class == 3) {

                        $text = strtoupper($seat->seat_text);

                        if ($text === 'EXIT') {
                            $class = 'vertical_exit_prv';
                        } elseif ($text === 'TOILET') {
                            $class = 'vertical_toilet_prv';
                        } else {
                            $class = 'bus-vertical-sleeper';
                        }

                        $html .= '
                            <label class="seat-wrap vertical-sleeper-wrap">
                                <span class="' . $class . '"></span>
                                <span class="seat-number">' . $seat->seat_text . '</span>
                            </label>
                        ';

                        $skip[$rIndex + 1][$cIndex] = true;
                    }

                    // HORIZONTAL (EXIT / TOILET / NORMAL)
                    elseif ($seat->seat_class == 2) {

                        $text = strtoupper($seat->seat_text);

                        if ($text === 'EXIT') {
                            $class = 'horizontal_exit_prv';
                        } elseif ($text === 'TOILET') {
                            $class = 'horizontal_toilet_prv';
                        } else {
                            $class = 'bus-sleeper';
                        }

                        $html .= '
                            <label class="seat-wrap sleeper-wrap">
                                <span class="' . $class . '"></span>
                                <span class="seat-number">' . $seat->seat_text . '</span>
                            </label>
                        ';
                    }

                    // SINGLE (EXIT / NORMAL)
                    else {

                        $text = strtoupper($seat->seat_text);

                        if ($text === 'EXIT') {
                            $class = 'seat_exit_prv';
                        } else {
                            $class = 'bus-seat';
                        }

                        $html .= '
                            <label class="seat-wrap">
                                <span class="' . $class . '"></span>
                                <span class="seat-number">' . $seat->seat_text . '</span>
                            </label>
                        ';
                    }
                }
            }

            $html .= '</div></div>';
        }

        $html .= '</div>';

        return response($html);
    }
}
