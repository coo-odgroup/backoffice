<?php

namespace App\Http\Controllers\Admin\Bus;

use App\Http\Controllers\Controller;
use App\Models\Bus\Bus;
use App\Models\Bus\BusRoutesStops;
use App\Models\Bus\BusRoutesMap;
use App\Models\Bus\BusBoardingDropping;
use App\Models\Bus\BusRouteFares;
use App\Models\Bus\BusContacts;
use App\Models\Bus\BusSeats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use SebastianBergmann\Environment\Console;

class BusWizardController extends Controller
{
    protected $createBusUrl;

    public function __construct()
    {
        $this->createBusUrl = '/admin/bus/create/';

        view()->share('createBusUrl', $this->createBusUrl);
    }

    public function index()
    {
        return view('admin.bus.wizard.bus');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $operator = (request('operator') !== null && request('operator') !== '') ? (int)request('operator') : '';
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $source = (request('source') !== null && request('source') !== '') ? (int)request('source') : '';
            $destination = (request('destination') !== null && request('destination') !== '') ? (int)request('destination') : '';

            $dataQuery = Bus::with([
                'operator:id,name,organization_name',
                'brand:id,brand_name',
                'model:id,model_name',
                'axleType:id,axle_type',
                'createdBy:id,name',
                'updatedBy:id,name',
                'routemap:id,bus_id,bus_route_id',
                'routemap.route:id,boarding_city_id,dropping_city_id',
                'routemap.route.boardingcity:id,city_name',
                'routemap.route.droppingcity:id,city_name'
            ])
                ->select(
                    'id',
                    'id as bus_id',
                    'bus_operator_id',
                    'name as bus_name',
                    'via',
                    'bus_number',
                    'gen_bus_type',
                    'created_at',
                    'created_by',
                    'updated_at',
                    'updated_by',
                    'active_status',
                    'brand_id',
                    'model_id',
                    'axle_type_id'
                );

            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('name', 'like', "%{$txtSearch}%")
                        ->orWhere('bus_number', 'like', "%{$txtSearch}%")
                        ->orWhere('via', 'like', "%{$txtSearch}%")
                        ->orWhereHas('operator', function ($q2) use ($txtSearch) {
                            $q2->where('organization_name', 'like', "%{$txtSearch}%");
                        });
                });
            }

            if (!empty($operator)) {
                $dataQuery->whereHas('operator', function ($q2) use ($operator) {
                    $q2->where('id', $operator);
                });
            }

            if ($selStatus !== '' && isset($selStatus)) {
                $dataQuery->where('active_status', $selStatus);
            }

            if (!empty($source) || !empty($destination)) {

                $dataQuery->where(function ($q) use ($source, $destination) {

                    if (!empty($source)) {
                        $q->orWhereHas('routemap.route.boardingcity', function ($q3) use ($source) {
                            $q3->where('id', $source);
                        });
                    }

                    if (!empty($destination)) {
                        $q->orWhereHas('routemap.route.droppingcity', function ($q4) use ($destination) {
                            $q4->where('id', $destination);
                        });
                    }
                });
            }

            $count = (clone $dataQuery)->count();

            $start = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            $columns = [
                2 => 'name',
                3 => 'created_by',
                4 => 'active_status'
            ];

            if (!empty(request('order'))) {
                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'name';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'name';
                $orderType = 'asc';
            }

            $dataQuery->orderBy($orderColumn, $orderType);

            if ($length == -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery->offset($start)
                    ->limit($length)
                    ->get();
            }

            if ($arrRes->count() > 0) {
                foreach ($arrRes as $val) {
                    $val->created_date = date('d-M-Y H:i:s', strtotime($val->created_at));
                    $val->updated_date = $val->updated_at ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : null;
                    $val->is_active = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_bus_id = Crypt::encryptString($val->bus_id);
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in BusWizardController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'BusWizardController',
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

    public function preview($bus_id = null, $param = null, $param2 = null)
    {
        // return $param;
        $data = [];
        $data['strPage'] = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        $busId = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;

        $bus_record = Bus::with('operator', 'brand', 'model', 'axleType', 'service', 'seatType', 'seatLayout', 'cancellationslab.slabInfo')->where('id', $busId)->first();

        $amennity_records = DB::table('odbusdev.bus_amenities as ba')
            ->join('mst_amenities as a', 'a.id', '=', 'ba.amenities_id')
            ->join('mst_amenity_categories as c', 'c.id', '=', 'a.category_id')
            ->where('ba.active_status', 1)
            ->where('ba.bus_id', $busId)
            ->select('c.category_name', 'a.amenity_name', 'a.icon')
            ->get()
            ->groupBy('category_name');

        $busRoutesStops = BusRoutesStops::with('city')->where('bus_id', $busId)->orderBy('stop_order', 'ASC')->get();
        $busBoardingDropping = BusBoardingDropping::with(['city', 'station'])
            ->where('bus_id', $busId)
            ->get()
            ->groupBy(fn($item) => $item->city->city_name)
            ->map(function ($group) {
                return $group->map(function ($item) {
                    return [
                        'location' => $item->station->brd_drp_point ?? '--',
                        'time' => $item->timing,
                        'type' => $item->type,
                    ];
                })->values();
            })
            ->toArray();
        $busRouteFares = BusRouteFares::with('source', 'destination')->where('bus_id', $busId)->get();
        $busContacts = BusContacts::where('bus_id', $busId)->get();

        $seat_layout_id = Bus::where('id', $busId)
            ->select('mst_seat_layout_name_id')
            ->first()
            ->mst_seat_layout_name_id;

        $seatLayout = $this->genSeatLayout($seat_layout_id, $busId);

        $data['bus_id'] = $busId;
        $data['enc_bus_id'] = $bus_id;
        $data['param'] = $param;
        $data['param2'] = $param2;

        $page = "preview";

        if ($param == "view") {
            $page = "busview";
        }

        return view('admin.bus.wizard.'.$page, compact('data', 'bus_record', 'amennity_records', 'busRoutesStops', 'busBoardingDropping', 'busRouteFares', 'busContacts', 'seatLayout'));
    }

    public function genSeatLayout($seatLayoutId, $busId)
    {
        $busSeats = BusSeats::where('bus_id', $busId)->get();

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
        $html = '<div class="bpv-seat-box"><div class="seat-left"><div class="bus-layout">';

        $windowSeatCount = 0;
        $aisleSeatCount = 0;

        foreach (['UPPER', 'LOWER'] as $deck) {

            if (empty($layout[$deck])) {
                continue;
            }

            $html .= '<div class="berth-row">';
            $html .= '<div class="berth-label">' . ucwords(strtolower($deck)) . ' Berth</div>';
            $html .= '<div class="layout-box" style="display:grid;grid-template-columns: repeat(' . $maxCols[$deck] . ', 42px);gap:5px;">';

            $skip = [];

            foreach ($layout[$deck] as $rIndex => $row) {
                foreach ($row as $cIndex => $seat) {

                    $selectedCalss = "";

                    foreach ($busSeats as $s_val) {
                        if ($seat->id == $s_val->seat_id) {
                            $selectedCalss = "selected";
                            break;
                        }
                    }

                    if ($seat->is_window == 1) {
                        $windowSeatCount++;
                    }

                    if ($seat->is_aisle == 1) {
                        $aisleSeatCount++;
                    }

                    if (isset($skip[$rIndex][$cIndex])) {
                        continue;
                    }

                    // EMPTY
                    if ($seat->seat_class == 0 || $seat->seat_text == null) {
                        $html .= '<div class="empty-seat"></div>';
                    }

                    // VERTICAL
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
                            <span class="' . $class . ' ' . $selectedCalss . '"></span>
                            <span class="seat-number">' . $seat->seat_text . '</span>
                        </label>
                    ';

                        $skip[$rIndex + 1][$cIndex] = true;
                    }

                    // HORIZONTAL
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
                            <span class="' . $class . ' ' . $selectedCalss . '"></span>
                            <span class="seat-number">' . $seat->seat_text . '</span>
                        </label>
                    ';
                    }

                    // SINGLE
                    else {

                        $text = strtoupper($seat->seat_text);

                        if ($text === 'EXIT') {
                            $class = 'seat_exit_prv';
                        } else {
                            $class = 'bus-seat';
                        }

                        $html .= '
                        <label class="seat-wrap">
                            <span class="' . $class . ' ' . $selectedCalss . '"></span>
                            <span class="seat-number">' . $seat->seat_text . '</span>
                        </label>
                    ';
                    }
                }
            }

            $html .= '</div></div>';
        }

        $html .= '</div></div></div>
        <div class="bpv-seat-info">
            <div><span>Window Seats:</span>
                <p>' . $windowSeatCount . '</p>
            </div>
            <div><span>Aisle Seats:</span>
                <p>' . $aisleSeatCount . '</p>
            </div>
        </div>';

        return $html;
    }

    public function getBoardingDropping(Request $request)
    {
        $type = $request->type;
        $city_id = $request->city_id;

        $data = DB::table('mst_boarding_droping')
            ->where('type', $type)
            ->where('cities_id', $city_id)
            ->get(['id', 'brd_drp_point']);

        return response()->json($data);
    }

    public function getListingTime(Request $request)
    {
        $city_id = (int) $request->city_id;

        $data = BusRoutesStops::where('city_id', $city_id)
            ->select('listing_time')
            ->first();

        return response()->json([
            'listing_time' => $data->listing_time ?? null
        ]);
    }

    public function getSeatsByLayout(Request $request)
    {
        $seatLayoutId = $request->layout_id;

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

            ksort($rows); // sort rows

            foreach ($rows as $rowKey => $cols) {
                ksort($cols); // sort columns
                $rows[$rowKey] = $cols;
            }

            $layout[$deck] = $rows;
        }

        $maxCols = [
            'UPPER' => 0,
            'LOWER' => 0
        ];

        foreach ($layout as $deck => $rows) {
            foreach ($rows as $cols) {
                if (!empty($cols)) {
                    $maxCols[$deck] = max($maxCols[$deck], max(array_keys($cols)));
                }
            }
        }

        return response()->json([
            'layout'  => $layout,
            'maxCols' => $maxCols
        ]);
    }

    public function copy($bus_id = null)
    {
        $busId = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;

        DB::beginTransaction();

        try {
            $bus = Bus::with([
                'amenities',
                'seats',
                'stops'
            ])->findOrFail($busId);

            $routes = BusRoutesMap::with('route')
                ->where('bus_id', $busId)
                ->get();

            // $bd = BoardingDropping::where('bus_id', $busId)->get();
            $bRouteFare = BusRouteFares::where('bus_id', $busId)->get();

            // return $bRouteFare;

            // Copy Bus
            $newBus = $bus->replicate();
            $newBus->created_by = 1;
            $newBus->save();

            // Copy Seats
            foreach ($bus->seats as $seat) {
                $newSeat = $seat->replicate();
                $newSeat->bus_id = $newBus->id;
                $newSeat->save();
            }

            // Copy Routes
            foreach ($routes as $route) {
                $newRoute = $route->replicate();
                $newRoute->bus_id = $newBus->id;
                $newRoute->save();
            }

            // Copy Amenities
            foreach ($bus->amenities as $amenity) {
                $newAmenity = $amenity->replicate();
                $newAmenity->bus_id = $newBus->id;
                $newAmenity->save();
            }

            // Copy Stops
            foreach ($bus->stops as $stop) {
                $newStop = $stop->replicate();
                $newStop->bus_id = $newBus->id;
                $newStop->save();
            }

            // Copy Route Fare
            foreach ($bRouteFare as $fare) {
                $newRoutes = $fare->replicate();
                $newRoutes->bus_id = $newBus->id;
                $newRoutes->save();
            }

            DB::commit();

            $enc_bus_id = (!empty($newBus->id)) ? Crypt::encryptString($newBus->id) : 0;

            return redirect()->route('bus.step1', [
                'encId' => $enc_bus_id,
                'param' => 'save'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function viewBusRecord(Request $request)
    {
        $id = Crypt::decryptString($request->id);

        $records = Bus::where('id', $id)->first();

        return response()->json($records ?? []);
    }
}
