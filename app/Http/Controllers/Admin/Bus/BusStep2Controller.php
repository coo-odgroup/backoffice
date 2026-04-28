<?php

namespace App\Http\Controllers\Admin\Bus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Bus\BusRoutesStops;

class BusStep2Controller extends Controller
{
    protected $createBusUrl;

    public function __construct()
    {
        $this->createBusUrl = '/admin/bus/create/';

        view()->share('createBusUrl', $this->createBusUrl);
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
        $isExist = BusRoutesStops::where('bus_id', $busId)->first();
        if ($param == 'save' && ($param2 == 'back' || $param2 == 'edit') || $isExist) {
            $busRouteStops = BusRoutesStops::with('city')
                ->where('bus_id', $busId)
                ->get();

            $data['step2Res'] = $busRouteStops->map(function ($item) {
                return [
                    (string) $item->city_id,
                    $item->city->city_name
                ];
            })->values();

            $data['existRes'] = 1;
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
}
