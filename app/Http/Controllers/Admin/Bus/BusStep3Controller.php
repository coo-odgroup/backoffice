<?php

namespace App\Http\Controllers\Admin\Bus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Bus\BusRoutes;
use App\Models\Bus\BusRoutesStops;
use App\Models\Bus\BusRoutesMap;

class BusStep3Controller extends Controller
{
    protected $createBusUrl;

    public function __construct()
    {
        $this->createBusUrl = '/admin/bus/create/';

        view()->share('createBusUrl', $this->createBusUrl);
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

            // Check existing route map
            $busR = BusRoutesMap::where('bus_id', $bus_id)->first();

            $bus_route_id = $busR->bus_route_id ?? 0;
            $bus_routes_map_id = $busR->id ?? 0;

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
            $routeMap = ($bus_routes_map_id != 0)
                ? BusRoutesMap::find($bus_routes_map_id)
                : new BusRoutesMap();

            $routeMap->bus_id = $bus_id;

            $routeMap->bus_route_id = $route_id;

            if ($bus_routes_map_id != 0) {
                $routeMap->updated_by = 1;
            } else {
                $routeMap->created_by = 1;
            }

            $routeMap->save();

            // BusRoutesStops Section
            if ($param2 == 'back' || $param2 == 'edit') {
                BusRoutesStops::where('bus_id', $bus_id)
                    ->where('bus_route_id', $route_id)
                    ->delete();
            }

            $routeStops = [];
            $stop_order = 1;

            foreach ($cities as $cityId => $cityName) {
                $routeStops[] = [
                    'bus_route_id' => $route_id,
                    'bus_id' => $bus_id,
                    'city_id' => $cityId,
                    'stop_order' => $stop_order,
                    'is_boarding' => !empty($boarding[$cityId]) ? 1 : 0,
                    'is_dropping' => !empty($dropping[$cityId]) ? 1 : 0,
                    'listing_time' => $listing_time[$cityId] ?? null,
                    'created_by' => 1,
                    'created_at' => now(),
                ];
                $stop_order++;
            }

            // Insert stops
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

            Log::error('Route creation failed: ' . $e->getMessage());

            session()->flash('level', 'error');
            session()->flash('message', 'Something went wrong! Please try again.');

            return redirect()->back()->withInput();
        }
    }
}
