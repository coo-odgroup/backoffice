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
use App\Models\Master\Amenity;
use App\Models\Master\AmenityCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BusWizardController extends Controller
{
    public function step1()
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        $data['categories'] = AmenityCategory::with(['amenities' => function ($q) {
            $q->where('active_status', 1);
        }])
            ->whereHas('amenities', function ($q) {
                $q->where('active_status', 1);
            })
            ->get();
        return view('admin.bus.wizard.step1', compact('data'));
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

            // Bus Save
            $obj = new Bus();
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
            $obj->save();

            $bus_id = $obj->id;

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
                    'created_at' => now(),
                    'created_by' => 1
                ];
            }

            if (!empty($amenitiesData)) {
                BusAmenity::insert($amenitiesData);
            }

            DB::commit();

            session()->flash('level', 'success');
            session()->flash('message', 'Bus info created successfully.');

            $enc_bus_id = (!empty($bus_id)) ? Crypt::encryptString($bus_id) : 0;
            return redirect()->route('bus.step2', ['encId' => $enc_bus_id]);
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Bus creation failed: ' . $e->getMessage());

            session()->flash('level', 'error');
            session()->flash('message', 'Something went wrong! Please try again.');

            return redirect()->back()->withInput();
        }
    }

    public function step2($bus_id = null)
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        $bus_id = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;
        $data['bus_id'] = $bus_id;
        return view('admin.bus.wizard.step2', compact('data'));
    }

    public function postStep2(Request $request)
    {
        session()->flash('level', 'success');
        session()->flash('message', 'Cities selected and sorted successfully.');
        $enc_bus_id = (!empty($request->bus_id)) ? Crypt::encryptString($request->bus_id) : 0;
        return redirect()->route('bus.step3', ['encId' => $enc_bus_id]);
    }

    public function step3($bus_id = null)
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        $bus_id = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;
        $data['bus_id'] = $bus_id;
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

            if (empty($cities)) {
                throw new \Exception("Cities data is missing.");
            }

            $keys = array_keys($cities);
            $boarding_city_id = $keys[0];
            $dropping_city_id = end($keys);

            $route_name = $cities[$boarding_city_id] . ' - ' . $cities[$dropping_city_id];
            $route_signature_string = implode('-', $keys);
            $route_signature = md5($route_signature_string);

            // Route Section
            $route = new BusRoutes();
            $route->route_name = $route_name;
            $route->boarding_city_id = $boarding_city_id;
            $route->dropping_city_id = $dropping_city_id;
            $route->route_signature = $route_signature;
            $route->active_status = 1;
            $route->created_by = 1;
            $route->save();

            $route_id = $route->id;

            // BusRoutesMap Section
            $routeMap = new BusRoutesMap();
            $routeMap->bus_id = $bus_id;
            $routeMap->bus_route_id = $route_id;
            $routeMap->created_by = 1;
            $routeMap->save();

            // BusRoutesStops Section
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
            session()->flash('message', 'Boarding & Dropping managed successfully.');

            $enc_bus_id = (!empty($bus_id)) ? Crypt::encryptString($bus_id) : 0;
            return redirect()->route('bus.step4', ['encId' => $enc_bus_id]);
        } catch (\Exception $e) {

            DB::rollBack();

            // Optional: log error
            Log::error('Route creation failed: ' . $e->getMessage());

            session()->flash('level', 'error');
            session()->flash('message', 'Something went wrong! Please try again.');

            return redirect()->back()->withInput();
        }
    }

    public function step4($bus_id = null)
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        $bus_id = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;
        $data['bus_id'] = $bus_id;
        return view('admin.bus.wizard.step4', compact('data'));
    }

    public function postStep4(Request $request)
    {
        $busId = $request->bus_id;
        $stations = $request->stations ?? [];

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

        // Insert in batch
        if (!empty($insertData)) {
            BusBoardingDropping::insert($insertData);
        }

        session()->flash('level', 'success');
        session()->flash('message', 'Stoppage added successfully.');

        $enc_bus_id = (!empty($busId)) ? Crypt::encryptString($busId) : 0;
        return redirect()->route('bus.step5', ['encId' => $enc_bus_id]);
    }

    public function step5($bus_id = null)
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        $bus_id = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;

        $res = BusRoutesStops::with([
            'bus:id,name',
            'route:id,route_name',
            'city:id,city_name'
        ])
            ->where('bus_id', $bus_id)
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

        // return $result;

        $data['schedule_data'] = $result;
        $data['bus_id'] = $bus_id;
        return view('admin.bus.wizard.step5', compact('data'));
    }

    public function postStep5(Request $request)
    {
        try {
            $data = [];
            $count = count($request->from_stop_id);
            $busId = $request->bus_id;

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

            BusRouteFares::insert($data);

            session()->flash('level', 'success');
            session()->flash('message', 'Routes added successfully.');
        } catch (\Exception $e) {

            Log::error('Batch insert error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            session()->flash('level', 'success');
            session()->flash('message', 'Failed to save routes. Please try again.');
        }

        $enc_bus_id = (!empty($busId)) ? Crypt::encryptString($busId) : 0;
        return redirect()->route('bus.step6', ['encId' => $enc_bus_id]);
    }

    public function step6($bus_id = null)
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        $bus_id = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;
        $busData = Bus::where('id', $bus_id)->first();

        $data['bus_number'] = $busData ? $busData->bus_number : null;
        $data['bus_id'] = $bus_id;
        return view('admin.bus.wizard.step6', compact('data'));
    }

    public function postStep6(Request $request)
    {
        try {
            $contacts = $request->contacts ?? [];
            $busId = $request->bus_id;

            $insertData = [];

            foreach ($contacts as $contact) {
                $insertData[] = [
                    'bus_id' => $busId,
                    'type' => 1,
                    'phone' => $contact['phone'] ?? null,
                    'booking_sms_send' => isset($contact['booking_sms_send']) ? 1 : 0,
                    'cancel_sms_send' => isset($contact['cancel_sms_send']) ? 1 : 0,
                    'booking_wp_send' => isset($contact['booking_wp_send']) ? 1 : 0,
                    'cancel_wp_send' => isset($contact['cancel_wp_send']) ? 1 : 0,
                ];
            }

            BusContacts::insert($insertData);

            session()->flash('level', 'success');
            session()->flash('message', 'Bus Contacts added successfully.');
        } catch (\Exception $e) {

            Log::error('Batch insert error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            session()->flash('level', 'success');
            session()->flash('message', 'Failed to save contacts. Please try again.');
        }

        $enc_bus_id = (!empty($busId)) ? Crypt::encryptString($busId) : 0;
        return redirect()->route('bus.step7', ['encId' => $enc_bus_id]);
    }

    public function step7($bus_id = null)
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        $bus_id = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;
        $data['seat_layout'] = DB::table('mst_seat_layout_name')->get();
        $data['bus_id'] = $bus_id;
        return view('admin.bus.wizard.step7', compact('data'));
    }

    public function preview($bus_id = null)
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        return view('admin.bus.wizard.preview', compact('data'));
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
}
