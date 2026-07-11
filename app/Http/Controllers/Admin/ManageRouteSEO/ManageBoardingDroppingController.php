<?php

namespace App\Http\Controllers\Admin\ManageRouteSEO;

use App\Http\Controllers\Controller;
use App\Models\ManageRouteSEO\ManageBoardingDropping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CommonController;


class ManageBoardingDroppingController extends Controller
{
    public function index()
    {
        return view('admin.ManageRouteSEO.manageBoardingDropping');
    }

    public function dataTableView()
    {
        try {

            $routeId = request('route_id');

            if (empty($routeId)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Route is required.',
                    'data'    => []
                ]);
            }

            $route = DB::table('odbusmaster.mst_routes_details as rd')
                ->select(
                    'rd.id',
                    'rd.source_id',
                    'rd.destination_id',
                    'rd.source',
                    'rd.destination',
                    'rd.distance',
                    'rd.duration_in_hours',
                    'rd.min_fare',
                    'rd.max_fare',
                    'rd.bus_type_comma_separaed',
                    'rd.breadcrumb_schema',
                    'rd.faq_schema'
                )
                ->where('rd.id', $routeId)
                ->first();

            if (empty($route)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Selected route not found.',
                    'data'    => []
                ]);
            }

            $boardingPoints = DB::table('odbusmaster.mst_boarding_droping as bd')
                ->leftJoin('users as uc', 'uc.id', '=', 'bd.created_by')
                ->leftJoin('users as uu', 'uu.id', '=', 'bd.updated_by')
                ->select(
                    'bd.id',
                    'bd.cities_id',
                    'bd.type',
                    'bd.brd_drp_point',
                    'bd.landmark',
                    'bd.latitude',
                    'bd.longitude',
                    'bd.sequence_no',
                    'bd.active_status',
                    'bd.created_at',
                    'bd.created_by',
                    'bd.updated_at',
                    'bd.updated_by',
                    'uc.name as created_by_name',
                    'uu.name as updated_by_name'
                )
                ->where('bd.cities_id', $route->source_id)
                ->where('bd.type', 1)
                ->orderBy('bd.sequence_no', 'asc')
                ->get();

            $droppingPoints = DB::table('odbusmaster.mst_boarding_droping as bd')
                ->leftJoin('users as uc', 'uc.id', '=', 'bd.created_by')
                ->leftJoin('users as uu', 'uu.id', '=', 'bd.updated_by')
                ->select(
                    'bd.id',
                    'bd.cities_id',
                    'bd.type',
                    'bd.brd_drp_point',
                    'bd.landmark',
                    'bd.latitude',
                    'bd.longitude',
                    'bd.sequence_no',
                    'bd.active_status',
                    'bd.created_at',
                    'bd.created_by',
                    'bd.updated_at',
                    'bd.updated_by',
                    'uc.name as created_by_name',
                    'uu.name as updated_by_name'
                )
                ->where('bd.cities_id', $route->destination_id)
                ->where('bd.type', 2)
                ->orderBy('bd.sequence_no', 'asc')
                ->get();

            $savedMappings = DB::table('odbusmaster.mst_route_brd_drp')
                ->select('type', 'brd_drp_id')
                ->where('route_id', $routeId)
                ->get();

            $selectedBoardingIds = $savedMappings
                ->where('type', 1)
                ->pluck('brd_drp_id')
                ->map(fn($id) => (int) $id)
                ->values()
                ->toArray();

            $selectedDroppingIds = $savedMappings
                ->where('type', 2)
                ->pluck('brd_drp_id')
                ->map(fn($id) => (int) $id)
                ->values()
                ->toArray();

            $boardingPoints = $boardingPoints->map(function ($val) {
                $val->enc_id = Crypt::encryptString($val->id);
                $val->created_date = !empty($val->created_at) ? date('d-M-Y H:i:s', strtotime($val->created_at)) : '--';
                $val->updated_date = !empty($val->updated_at) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : '--';
                $val->is_active = ($val->active_status == 1) ? 'Active' : 'Inactive';
                return $val;
            })->values();

            $droppingPoints = $droppingPoints->map(function ($val) {
                $val->enc_id = Crypt::encryptString($val->id);
                $val->created_date = !empty($val->created_at) ? date('d-M-Y H:i:s', strtotime($val->created_at)) : '--';
                $val->updated_date = !empty($val->updated_at) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : '--';
                $val->is_active = ($val->active_status == 1) ? 'Active' : 'Inactive';

                return $val;
            })->values();

            $duration = (float) ($route->duration_in_hours ?? 0);
            $fromHrs  = $duration > 0 ? floor($duration) : '';
            $toHrs    = $duration > 0 ? ceil($duration) : '';

            return response()->json([
                'status' => true,
                'data'   => [
                    'route_id'              => $route->id,
                    'source_city_id'        => $route->source_id,
                    'source_city_name'      => $route->source,
                    'destination_city_id'   => $route->destination_id,
                    'destination_city_name' => $route->destination,

                    'distance'              => $route->distance,
                    'from_hrs'              => $fromHrs,
                    'to_hrs'                => $toHrs,
                    'bus_types'             => $route->bus_type_comma_separaed,
                    'min_fare'              => $route->min_fare,
                    'max_fare'              => $route->max_fare,

                    'breadcrumb_schema'     => $route->breadcrumb_schema,
                    'faq_schema'            => $route->faq_schema,

                    'boarding_points'       => $boardingPoints,
                    'dropping_points'       => $droppingPoints,
                    'selected_boarding_ids' => $selectedBoardingIds,
                    'selected_dropping_ids' => $selectedDroppingIds,
                ]
            ]);
        } catch (\Throwable $t) {

            Log::info("Exception occurred in ManageBoardingDroppingController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace'         => $t->getTraceAsString()
            ]);

            Log::error("Error", [
                'Controller' => 'ManageBoardingDroppingController',
                'Method'     => 'dataTableView',
                'Error'      => $t->getMessage()
            ]);

            return response()->json([
                'status'  => false,
                'message' => config('constants.SERVER_ERROR_MESSAGE'),
                'data'    => []
            ]);
        }
    }

    public function add($encId = null)
    {
        $data = [];
        $data['strPage']   = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset']  = 'Reset';

        try {

            $routeId = (int) request('route_id');

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'route_id'           => 'required|integer',
                    'breadcrumb_schema'  => 'nullable|string',
                    'faq_schema'         => 'nullable|string',
                ], [
                    'route_id.required' => 'Route is required.'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status'  => false,
                        'message' => $validator->errors()->first()
                    ]);
                }

                DB::beginTransaction();

                DB::table('odbusmaster.mst_routes_details')
                    ->where('id', $routeId)
                    ->update([
                        'breadcrumb_schema' => request('breadcrumb_schema'),
                        'faq_schema'        => request('faq_schema'),
                        'updated_at'        => now(),
                        'updated_by'        => 1
                    ]);
                if (!$routeId) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Selected route not found.'
                    ]);
                }

                $boardingIds = array_filter(explode(',', (string) request('hdn_boarding_ids')));
                $droppingIds = array_filter(explode(',', (string) request('hdn_dropping_ids')));

                $boardingIds = array_map(function ($encId) {
                    try {
                        return (int) Crypt::decryptString($encId);
                    } catch (\Throwable $t) {
                        return null;
                    }
                }, $boardingIds);

                $droppingIds = array_map(function ($encId) {
                    try {
                        return (int) Crypt::decryptString($encId);
                    } catch (\Throwable $t) {
                        return null;
                    }
                }, $droppingIds);

                $boardingIds = array_values(array_filter($boardingIds));
                $droppingIds = array_values(array_filter($droppingIds));

                // Hard delete existing boarding/dropping mapping for this route
                DB::table('odbusmaster.mst_route_brd_drp')
                    ->where('route_id', $routeId)
                    ->delete();

                $insertData = [];

                $nextId = (int) DB::table('odbusmaster.mst_route_brd_drp')->max('id');
                $nextId = $nextId > 0 ? $nextId + 1 : 1;

                foreach ($boardingIds as $boardingId) {
                    $insertData[] = [
                        'id'            => $nextId++,
                        'route_id'      => $routeId,
                        'type'          => 1,
                        'brd_drp_id'    => $boardingId,
                        'active_status' => 1,
                        'created_at'    => now(),
                        'created_by'    => 1
                    ];
                }

                foreach ($droppingIds as $droppingId) {
                    $insertData[] = [
                        'id'            => $nextId++,
                        'route_id'      => $routeId,
                        'type'          => 2,
                        'brd_drp_id'    => $droppingId,
                        'active_status' => 1,
                        'created_at'    => now(),
                        'created_by'    => 1
                    ];
                }

                if (!empty($insertData)) {
                    DB::table('odbusmaster.mst_route_brd_drp')->insert($insertData);
                }

                DB::commit();

                return response()->json([
                    'status'  => true,
                    'message' => 'Changes updated successfully.'
                ]);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'ManageBoardingDroppingController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return response()->json([
                'status'  => false,
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ]);
        }

        return view('admin.ManageRouteSEO.manageBoardingDropping', compact('data'));
    }
}
