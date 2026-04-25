<?php

namespace App\Http\Controllers\Admin\Bus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Bus\Bus;
use App\Models\Bus\BusContacts;

class BusStep6Controller extends Controller
{
    protected $createBusUrl;

    public function __construct()
    {
        $this->createBusUrl = '/admin/bus/create/';

        view()->share('createBusUrl', $this->createBusUrl);
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
}
