<?php

namespace App\Http\Controllers\Admin\Bus;

use App\Http\Controllers\Controller;
use App\Models\Bus\Bus;
use App\Models\Bus\BusAmenity;
use App\Models\Bus\BusRoutes;
use App\Models\Bus\BusRoutesStops;
use App\Models\Bus\BusRoutesMap;
use App\Models\Bus\BusBoardingDropping;
use App\Models\Bus\BusRouteFares;
use App\Models\Bus\BusContacts;
use App\Models\Bus\BusSeats;
use App\Models\Master\Amenity;
use App\Models\Master\AmenityCategory;
use App\Models\Master\BoardingDropping;
use App\Models\Master\Cities;
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
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';

            $dataQuery = Bus::with([
                'operator:id,name,organization_name',
                'brand:id,brand_name',
                'model:id,model_name',
                'axleType:id,axle_type',
                'createdBy:id,name',
                'updatedBy:id,name',
                'routemap.route.boardingcity:id,city_name',
                'routemap.route.droppingcity:id,city_name'
            ])
                ->select(
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

                        // relation search
                        ->orWhereHas('operator', function ($q2) use ($txtSearch) {
                            $q2->where('name', 'like', "%{$txtSearch}%")
                                ->orWhere('organization_name', 'like', "%{$txtSearch}%");
                        });
                });
            }

            if ($selStatus !== '' && isset($selStatus)) {
                $dataQuery->where('active_status', $selStatus);
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

    public function step1($bus_id = null, $param = null, $param2 = null)
    {
        $data = [];
        $data['strPage'] = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        $data['categories'] = AmenityCategory::with(['amenities' => function ($q) {
            $q->where('active_status', 1);
        }])
            ->whereHas('amenities', function ($q) {
                $q->where('active_status', 1);
            })
            ->get();

        $busId = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;
        $data['bus_id'] = $busId;
        $data['enc_bus_id'] = $bus_id;
        $data['param'] = $param;
        $data['param2'] = $param2;

        // Edit or Back or Continue
        $step1Res = [];
        $step1AmenityRes = [];
        if ($busId != 0) {
            $step1Res = Bus::where('id', $busId)->first();
            $step1AmenityRes = BusAmenity::with('amenity')
                ->where('bus_id', $busId)
                ->get()
                ->map(function ($item) {
                    return [
                        (string) $item->amenity->id,
                        $item->amenity->amenity_name
                    ];
                })
                ->values();
        }

        return view('admin.bus.wizard.step1', compact('data', 'step1Res', 'step1AmenityRes'));
    }

    public function postStep1(Request $request)
    {
        try {

            DB::beginTransaction();

            $request->validate([
                'name' => 'required',
                'bus_number' => 'required',
                'bus_operator_id' => 'required',
                'slab' => 'required',
            ]);

            $bus_operator_id = (int)request('bus_operator_id');
            $cancellationslabs_id = (int)request('slab');
            $name = htmlEncode(request('name'));
            $bus_number = htmlEncode(request('bus_number'));
            $via = htmlEncode(request('via'));
            $max_seat_book = htmlEncode(request('max_seat_book'));

            $gen_bus_type = request('gen_bus_type');
            $gen_bus_type = preg_replace('/\s+/', ' ', $gen_bus_type);
            $gen_bus_type = trim($gen_bus_type);

            $is_irctc_model = (int)request('is_irctc_model');
            $brand_id = (int)request('brand_id');
            $axle_type_id = (int)request('axle_type_id');
            $model_id = (int)request('model_id');
            $service_id = (int)request('service_id');
            $ac_type_id = (int)request('ac_type_id');
            $seat_type_id = (int)request('seat_type_id');
            $seat_layout_type_id = (int)request('seat_layout_type_id');

            $busId = (int)request('bus_id');

            // Bus Save
            $obj = ($busId != 0) ? Bus::find($busId) : new Bus();
            $obj->bus_operator_id = $bus_operator_id;
            $obj->cancellationslabs_id = $cancellationslabs_id;
            $obj->name = $name;
            $obj->bus_number = $bus_number;
            $obj->via = $via;
            $obj->max_seat_book = $max_seat_book;
            $obj->gen_bus_type = $gen_bus_type;
            $obj->is_irctc_model = $is_irctc_model;
            $obj->brand_id = $brand_id;
            $obj->axle_type_id = $axle_type_id;
            $obj->model_id = $model_id;
            $obj->service_id = $service_id;
            $obj->ac_type_id = $ac_type_id;
            $obj->seat_type_id = $seat_type_id;
            $obj->seat_layout_type_id = $seat_layout_type_id;
            $obj->active_status = 1;

            if ($busId != 0) {
                $obj->updated_by = 1;
            }

            $obj->save();

            $bus_id = $obj->id;

            if ($busId != 0) {
                $bus_id = $busId;
            }

            // Amenities Section
            $amenities_ids = request('amenities_id', []);

            $category_map = Amenity::whereIn('id', $amenities_ids)
                ->pluck('category_id', 'id'); // key = amenity_id

            $amenitiesData = [];

            foreach ($amenities_ids as $amenities_id) {

                if (!isset($category_map[$amenities_id])) {
                    continue;
                }

                $amenitiesData[] = [
                    'bus_id' => $bus_id,
                    'category_id' => $category_map[$amenities_id],
                    'amenities_id' => $amenities_id,
                    'active_status' => 1,
                    'created_by' => 1
                ];
            }

            if ($busId != 0) {
                BusAmenity::where('bus_id', $bus_id)->delete();
            }

            if (!empty($amenitiesData)) {
                BusAmenity::insert($amenitiesData);
            }

            DB::commit();

            session()->flash('level', 'success');
            session()->flash('message', 'Bus info created Successfully.');

            $enc_bus_id = (!empty($bus_id)) ? Crypt::encryptString($bus_id) : 0;
            $param2 = $request->param2;
            if ($param2 == 'edit') {
                return redirect()->route('bus.index');
            } else {
                return redirect()->route('bus.step2', [
                    'encId' => $enc_bus_id,
                    'param' => 'save'
                ]);
            }
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Bus creation failed: ' . $e->getMessage());

            session()->flash('level', 'error');
            session()->flash('message', 'Something went wrong! Please try again.');

            return redirect()->back()->withInput();
        }
    }

    public function step2($bus_id = null, $param = null, $param2 = null)
    {
        $data = [];
        $data['strPage'] = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        $busId = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;
        $data['bus_id'] = $busId;
        $data['enc_bus_id'] = $bus_id;
        $data['param'] = $param;
        $data['param2'] = $param2;

        // Edit or Back or Continue
        // $ifExist = BusRoutesStops::where('bus_id', $busId)->first();
        // return $ifExist;
        if ($param == 'save' && ($param2 == 'back' || $param2 == 'edit')) {
            $busRouteStops = BusRoutesStops::with('city')
                ->where('bus_id', $busId)
                ->get();

            $data['step2Res'] = $busRouteStops->map(function ($item) {
                return [
                    (string) $item->city_id,
                    $item->city->city_name
                ];
            })->values();
        }

        return view('admin.bus.wizard.step2', compact('data'));
    }

    public function postStep2(Request $request)
    {
        session()->flash('level', 'success');
        session()->flash('message', 'Cities Selected and Sorted Successfully.');
        $enc_bus_id = (!empty($request->bus_id)) ? Crypt::encryptString($request->bus_id) : 0;

        $param2 = $request->param2;
        if ($param2 == 'edit') {
            return redirect()->route('bus.step3', [
                'encId' => $enc_bus_id,
                'param' => 'save',
                'param2' => 'edit'
            ]);
        } else {
            return redirect()->route('bus.step3', [
                'encId' => $enc_bus_id,
                'param' => 'save'
            ]);
        }
    }

    public function step3($bus_id = null, $param = null, $param2 = null)
    {
        $data = [];
        $data['strPage'] = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        $busId = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;
        $data['bus_id'] = $busId;
        $data['enc_bus_id'] = $bus_id;
        $data['param'] = $param;
        $data['param2'] = $param2;

        // Edit or Back or Continue
        $isExist = BusRoutesStops::where('bus_id', $busId)->exists();

        if (($param == 'save' && ($param2 == 'back' || $param2 == 'edit')) || $isExist) {
            $data['step3Res'] = BusRoutesStops::where('bus_id', $busId)->get();

            $busRouteStops = BusRoutesStops::with('city')
                ->where('bus_id', $busId)
                ->get();

            $data['stopRes'] = $busRouteStops->map(function ($item) {
                return [
                    (string) $item->city_id,
                    $item->city->city_name
                ];
            })->values();

            $data['existRes'] = 1;
        }

        return view('admin.bus.wizard.step3', compact('data'));
    }

    public function postStep3(Request $request)
    {
        try {

            DB::beginTransaction();

            $cities = $request->cities ?? [];
            $boarding = $request->boarding ?? [];
            $dropping = $request->dropping ?? [];
            $listing_time = $request->time ?? [];
            $bus_id = $request->bus_id;
            $param2 = $request->param2;

            if (empty($cities)) {
                throw new \Exception("Cities data is missing.");
            }

            $keys = array_keys($cities);
            $boarding_city_id = $keys[0];
            $dropping_city_id = end($keys);

            $route_name = $cities[$boarding_city_id] . ' - ' . $cities[$dropping_city_id];
            $route_signature_string = implode('-', $keys);
            $route_signature = md5($route_signature_string);

            $busR = BusRoutesMap::where('bus_id', $bus_id)->first();
            if (!empty($busR)) {
                $bus_route_id = $busR->bus_route_id;
                $bus_routes_map_id = $busR->id;
            } else {
                $bus_route_id = 0;
                $bus_routes_map_id = 0;
            }

            // Route Section
            $route = ($bus_route_id != 0) ? BusRoutes::find($bus_route_id) : new BusRoutes();
            $route->route_name = $route_name;
            $route->boarding_city_id = $boarding_city_id;
            $route->dropping_city_id = $dropping_city_id;
            $route->route_signature = $route_signature;

            if ($bus_route_id != 0) {
                $route->updated_by = 1;
            } else {
                $route->active_status = 1;
                $route->created_by = 1;
            }
            $route->save();

            $route_id = $route->id;

            // BusRoutesMap Section
            $routeMap = ($bus_routes_map_id != 0) ? BusRoutesMap::find($bus_routes_map_id) : new BusRoutesMap();
            $routeMap->bus_id = $bus_id;
            $routeMap->bus_route_id = $bus_route_id ? $bus_route_id : $route_id;
            if ($bus_routes_map_id != 0) {
                $routeMap->updated_by = 1;
            } else {
                $routeMap->created_by = 1;
            }
            $routeMap->save();

            // BusRoutesStops Section
            if ($param2 == 'back' || $param2 == 'edit') {
                BusRoutesStops::where('bus_id', $bus_id)->delete();
            }

            $routeStops = [];
            $stop_order = 1;
            foreach ($cities as $cityId => $cityName) {
                $routeStops[] = [
                    'bus_route_id' => $route_id,
                    'bus_id' => $bus_id,
                    'city_id' => $cityId,
                    'stop_order' => $stop_order,
                    'is_boarding' => isset($boarding[$cityId]) ? 1 : 0,
                    'is_dropping' => isset($dropping[$cityId]) ? 1 : 0,
                    'listing_time' => $listing_time[$cityId] ?? null,
                    'created_by' => 1
                ];
                $stop_order++;
            }

            BusRoutesStops::insert($routeStops);

            DB::commit();

            session()->flash('level', 'success');
            session()->flash('message', 'Boarding & Dropping Timings Created Successfully.');

            $enc_bus_id = (!empty($bus_id)) ? Crypt::encryptString($bus_id) : 0;

            if ($param2 == 'edit') {
                return redirect()->route('bus.step4', [
                    'encId' => $enc_bus_id,
                    'param' => 'save',
                    'param2' => 'edit'
                ]);
            } else {
                return redirect()->route('bus.step4', [
                    'encId' => $enc_bus_id,
                    'param' => 'save'
                ]);
            }
        } catch (\Exception $e) {

            DB::rollBack();

            // Optional: log error
            Log::error('Route creation failed: ' . $e->getMessage());

            session()->flash('level', 'error');
            session()->flash('message', 'Something went wrong! Please try again.');

            return redirect()->back()->withInput();
        }
    }

    public function step4($bus_id = null, $param = null, $param2 = null)
    {
        $data = [];
        $data['strPage'] = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        $busId = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;
        $data['bus_id'] = $busId;
        $data['enc_bus_id'] = $bus_id;
        $data['param'] = $param;
        $data['param2'] = $param2;

        // Edit or Back or Continue
        $isExist = BusBoardingDropping::where('bus_id', $busId)->exists();
        if (($param == 'save' && $param2 == 'back') || $isExist) {
            $data['existRes'] = 1;

            $busRouteStops = BusRoutesStops::with('city')
                ->where('bus_id', $busId)
                ->get();

            $data['stopRes'] = $busRouteStops->map(function ($item) {
                return [
                    (string) $item->city_id,
                    $item->city->city_name
                ];
            })->values();
        }

        $data['step4Res'] = BusBoardingDropping::with('city', 'stop')->where('bus_id', $busId)->get();

        return view('admin.bus.wizard.step4', compact('data'));
    }

    public function postStep4(Request $request)
    {
        $busId = $request->bus_id;
        $stations = $request->stations ?? [];
        $param2 = $request->param2;

        $insertData = [];

        foreach ($stations as $cityId => $stops) {

            foreach ($stops as $stop) {

                // Only insert if checked
                if (!isset($stop['checked'])) {
                    continue;
                }

                $insertData[] = [
                    'bus_id' => $busId,
                    'type' => (int) $stop['type'],
                    'city_id' => (int) $cityId,
                    'stop_id' => (int) $stop['stop_id'],
                    'timing' => $stop['time'],
                    'active_status' => 1,
                    'created_by' => 1,
                ];
            }
        }

        if ($param2 == 'back' || $param2 == 'edit') {
            BusBoardingDropping::where('bus_id', $busId)->delete();
        }

        // Insert in batch
        if (!empty($insertData)) {
            BusBoardingDropping::insert($insertData);
        }

        session()->flash('level', 'success');
        session()->flash('message', 'Stoppage added Successfully.');

        $enc_bus_id = (!empty($busId)) ? Crypt::encryptString($busId) : 0;

        if ($param2 == 'edit') {
            return redirect()->route('bus.step5', [
                'encId' => $enc_bus_id,
                'param' => 'save',
                'param2' => 'edit'
            ]);
        } else {
            return redirect()->route('bus.step5', [
                'encId' => $enc_bus_id,
                'param' => 'save'
            ]);
        }
    }

    public function step5($bus_id = null, $param = null, $param2 = null)
    {
        $data = [];
        $data['strPage'] = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        $busId = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;

        $res = BusRoutesStops::with([
            'bus:id,name',
            'route:id,route_name',
            'city:id,city_name'
        ])
            ->where('bus_id', $busId)
            ->get();

        $resCollect = collect($res); // your JSON data

        // Get boarding cities
        $boardingCities = $resCollect->where('is_boarding', 1);

        // Get dropping cities
        $droppingCities = $resCollect->where('is_dropping', 1);

        $result = [];

        foreach ($boardingCities as $board) {
            foreach ($droppingCities as $drop) {

                if ($board['stop_order'] < $drop['stop_order']) {

                    $result[] = [
                        'source' => $board['city']['city_name'],
                        'source_id' => $board['city']['id'],
                        'destination' => $drop['city']['city_name'],
                        'destination_id' => $drop['city']['id'],
                        'city_id' => $board['city']['id']
                    ];
                }
            }
        }

        $data['schedule_data'] = $result;
        $data['bus_id'] = $busId;
        $data['enc_bus_id'] = $bus_id;
        $data['param'] = $param;
        $data['param2'] = $param2;

        // Edit or Back or Continue
        $isExist = BusRouteFares::where('bus_id', $busId)->exists();
        if (($param == 'save' && $param2 == 'back') || $isExist) {
            $data['existRes'] = 1;
        }

        $data['step5Res'] = BusRouteFares::where('bus_id', $busId)->get();

        return view('admin.bus.wizard.step5', compact('data'));
    }

    public function postStep5(Request $request)
    {
        try {
            $data = [];
            $count = count($request->from_stop_id);
            $busId = $request->bus_id;
            $param2 = $request->param2;
            $existRes = $request->existRes;

            $busData = BusRoutesMap::where('bus_id', $busId)->first();
            $bus_route_id = $busData ? $busData->bus_route_id : null;

            for ($i = 0; $i < $count; $i++) {

                $data[] = [
                    'bus_id' => $request->bus_id,
                    'bus_route_id' => $bus_route_id,
                    'from_stop_id' => $request->from_stop_id[$i],
                    'from_journey_day' => $request->from_journey_day[$i] ?? 1,
                    'to_stop_id' => $request->to_stop_id[$i],
                    'to_journey_day' => $request->to_journey_day[$i] ?? 1,
                    'seat_fare' => $request->seat_fare[$i],
                    'upper_sleeper_fare' => $request->upper_sleeper_fare[$i],
                    'lower_sleeper_fare' => $request->lower_sleeper_fare[$i],
                    'seize_time' => $request->seize_time[$i],
                    'close_time' => $request->close_time[$i],
                    'active_status' => $request->active_status[$i] ?? 0,
                    'created_at' => now(),
                    'created_by' => 1,
                ];
            }

            if ($param2 == 'back' || $param2 == 'edit' || $existRes == 1) {
                BusRouteFares::where('bus_id', $busId)->delete();
            }

            // Insert in batch
            BusRouteFares::insert($data);

            session()->flash('level', 'success');
            session()->flash('message', 'Routes added Successfully.');
        } catch (\Exception $e) {

            Log::error('postStep5 Batch insert error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            session()->flash('level', 'success');
            session()->flash('message', 'Failed to save routes. Please try again.');
        }

        $enc_bus_id = (!empty($busId)) ? Crypt::encryptString($busId) : 0;

        if ($param2 == 'edit') {
            return redirect()->route('bus.index');
        } else {
            return redirect()->route('bus.step6', [
                'encId' => $enc_bus_id,
                'param' => 'save'
            ]);
        }
    }

    public function step6($bus_id = null, $param = null, $param2 = null)
    {
        $data = [];
        $data['strPage'] = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        $busId = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;
        $busData = Bus::where('id', $busId)->first();

        $data['bus_number'] = $busData ? $busData->bus_number : null;
        $data['bus_id'] = $busId;
        $data['enc_bus_id'] = $bus_id;
        $data['param'] = $param;
        $data['param2'] = $param2;

        // Edit or Back or Continue
        $isExist = BusContacts::where('bus_id', $busId)->exists();
        if (($param == 'save' && $param2 == 'back') || $isExist) {
            $data['existRes'] = 1;
        }

        $data['step6Res'] = BusContacts::where('bus_id', $busId)->get();

        return view('admin.bus.wizard.step6', compact('data'));
    }

    public function postStep6(Request $request)
    {
        try {
            $contacts = $request->contacts ?? [];
            $busId = $request->bus_id;
            $param2 = $request->param2;
            $existRes = $request->existRes;

            $insertData = [];

            foreach ($contacts as $k => $contact) {
                $insertData[] = [
                    'bus_id' => $busId,
                    'type' => $k,
                    'phone' => $contact['phone'] ?? null,
                    'booking_sms_send' => isset($contact['booking_sms_send']) ? 1 : 0,
                    'cancel_sms_send' => isset($contact['cancel_sms_send']) ? 1 : 0,
                    'booking_wp_send' => isset($contact['booking_wp_send']) ? 1 : 0,
                    'cancel_wp_send' => isset($contact['cancel_wp_send']) ? 1 : 0,
                ];
            }

            if ($param2 == 'back' || $param2 == 'edit' || $existRes == 1) {
                BusContacts::where('bus_id', $busId)->delete();
            }

            // Batch Insert
            BusContacts::insert($insertData);

            session()->flash('level', 'success');
            session()->flash('message', 'Bus Contacts added Successfully.');
        } catch (\Exception $e) {

            Log::error('postStep6 Batch insert error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            session()->flash('level', 'success');
            session()->flash('message', 'Failed to save contacts. Please try again.');
        }

        $enc_bus_id = (!empty($busId)) ? Crypt::encryptString($busId) : 0;
        if ($param2 == 'edit') {
            return redirect()->route('bus.index');
        } else {
            return redirect()->route('bus.step7', [
                'encId' => $enc_bus_id,
                'param' => 'save'
            ]);
        }
    }

    public function step7($bus_id = null, $param = null, $param2 = null)
    {
        $data = [];
        $data['strPage'] = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        $busId = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;
        $data['seat_layout'] = DB::table('mst_seat_layout_name')->get();
        $data['bus_id'] = $busId;
        $data['enc_bus_id'] = $bus_id;
        $data['param'] = $param;
        $data['param2'] = $param2;

        // Edit or Back or Continue
        $isExist = BusSeats::where('bus_id', $busId)->exists();
        if (($param == 'save' && $param2 == 'back') || $isExist) {
            $data['existRes'] = 1;
        }

        return view('admin.bus.wizard.step7', compact('data'));
    }

    public function postStep7(Request $request)
    {
        try {

            $busId = $request->bus_id;
            $seat_layout_id = $request->seat_layout_id;
            $seat_codes = $request->seat_code;
            $seat_ids = $request->seat_id;
            $param2 = $request->param2;
            $existRes = $request->existRes;

            $insertData = [];

            foreach ($seat_ids as $k => $seat) {
                $insertData[] = [
                    'seat_id' => $seat,
                    'bus_id' => $busId,
                    'type' => 0,
                    'seat_layout_id' => $seat_layout_id,
                    'seat_code' => $seat_codes[$k],
                    'created_at' => now(),
                    'created_by' => 1
                ];
            }

            if ($param2 == 'back' || $param2 == 'edit' || $existRes == 1) {
                BusSeats::where('bus_id', $busId)->delete();
            }

            BusSeats::insert($insertData);

            session()->flash('level', 'success');
            session()->flash('message', 'Seat Layout Created Successfully.');
        } catch (\Exception $e) {

            Log::error('postStep7 Batch insert error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            session()->flash('level', 'success');
            session()->flash('message', 'Failed to save Seat Layout. Please try again.');
        }

        $enc_bus_id = (!empty($busId)) ? Crypt::encryptString($busId) : 0;
        if ($param2 == 'edit') {
            return redirect()->route('bus.index');
        } else {
            return redirect()->route('bus.preview', [
                'encId' => $enc_bus_id,
                'param' => 'save'
            ]);
        }
    }

    public function preview($bus_id = null, $param = null, $param2 = null)
    {
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
        $busBoardingDropping = BusBoardingDropping::with('city', 'stop')->where('bus_id', $busId)->get();
        $busRouteFares = BusRouteFares::with('source', 'destination')->where('bus_id', $busId)->get();
        $busContacts = BusContacts::where('bus_id', $busId)->get();

        $seat_layout_id = BusSeats::where('bus_id', $busId)
            ->select('seat_layout_id')
            ->distinct()
            ->first()
            ->seat_layout_id;

        $seatLayout = $this->genSeatLayout($seat_layout_id);

        $data['bus_id'] = $busId;
        $data['enc_bus_id'] = $bus_id;
        $data['param'] = $param;
        $data['param2'] = $param2;

        return view('admin.bus.wizard.preview', compact('data', 'bus_record', 'amennity_records', 'busRoutesStops', 'busBoardingDropping', 'busRouteFares', 'busContacts', 'seatLayout'));
    }

    public function genSeatLayout($seatLayoutId = null)
    {
        $busSeats = BusSeats::where('seat_layout_id', $seatLayoutId)->get();

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
}
