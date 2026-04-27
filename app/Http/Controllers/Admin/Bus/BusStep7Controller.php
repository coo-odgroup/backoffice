<?php

namespace App\Http\Controllers\Admin\Bus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Bus\Bus;
use App\Models\Bus\BusSeats;

class BusStep7Controller extends Controller
{
    protected $createBusUrl;

    public function __construct()
    {
        $this->createBusUrl = '/admin/bus/create/';

        view()->share('createBusUrl', $this->createBusUrl);
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

            $data['step7Res'] = BusSeats::where('bus_id', $busId)->get();
            $data['step7BusRes'] = Bus::where('id', $busId)
                ->value('mst_seat_layout_name_id');

            // return $data['step7Res'];
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

            $obj = Bus::find($busId);
            $obj->mst_seat_layout_name_id = $seat_layout_id;
            $obj->save();

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
}
