<?php

namespace App\Http\Controllers\Admin\Bus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Bus\BusRoutesStops;
use App\Models\Bus\BusRouteFares;
use App\Models\Bus\BusRoutesMap;

class BusStep5Controller extends Controller
{
    protected $createBusUrl;

    public function __construct()
    {
        $this->createBusUrl = '/admin/bus/create/';

        view()->share('createBusUrl', $this->createBusUrl);
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
}
