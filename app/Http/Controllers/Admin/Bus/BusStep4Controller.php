<?php

namespace App\Http\Controllers\Admin\Bus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Bus\BusRoutesStops;
use App\Models\Bus\BusBoardingDropping;

class BusStep4Controller extends Controller
{
    protected $createBusUrl;

    public function __construct()
    {
        $this->createBusUrl = '/admin/bus/create/';

        view()->share('createBusUrl', $this->createBusUrl);
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

        $data['step4Res'] = BusBoardingDropping::with(['city.boardingdroppings'])->where('bus_id', $busId)->get();

        return view('admin.bus.wizard.step4', compact('data'));
    }

    public function postStep4(Request $request)
    {
        $busId = $request->bus_id;
        $stations = $request->stations ?? [];
        $param2 = $request->param2;
        $existRes = $request->existRes;

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

        if ($param2 == 'back' || $param2 == 'edit' || $existRes == 1) {
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
}
