<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Districts;
use App\Models\States;

class CommonController extends Controller
{
    public function getStateList(Request $request)
    {
        $states = States::where('active_status', 1)
                    ->orderBy('state_name')
                    ->get(['id', 'state_name']);


        return response()->json([
            'status' => true,
            'data'   => $states
        ]);
    }

    public function getDistrictList(Request $request) {

        $stateId = $request->state_id;

        $districts = Districts::where('state_id', $stateId)
                              ->where('active_status', 1)
                              ->orderBy('district_name')
                              ->get(['id', 'district_name']);

        return response()->json([
            'status' => true,
            'data'   => $districts
        ]);
    }
}
