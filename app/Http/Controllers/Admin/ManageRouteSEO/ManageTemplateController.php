<?php

namespace App\Http\Controllers\Admin\ManageRouteSEO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CommonController;


class ManageTemplateController extends Controller
{
    public function index()
    {
        return view('admin.ManageRouteSEO.manageTemplate');
    }

    public function dataTableView()
    {
        try {
            $routeId = (int) request('route_id');

            if ($routeId <= 0) {
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
                    'rd.first_bus_timing',
                    'rd.last_bus_timing',
                    'rd.min_fare',
                    'rd.max_fare',
                    'rd.bus_count',
                    'rd.operators_count',
                    
                    'rd.breadcrumb_schema',
                    'rd.faq_schema'
                )
                ->where('rd.id', $routeId)
                ->first();

            if (!$route) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Selected route not found.',
                    'data'    => []
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | Operators List HTML
        |--------------------------------------------------------------------------
        | mst_routes_operators.route_id -> operator_id
        | operator_id -> users.id -> users.name
        | if name missing, use Demo Operator
        */
            $operatorNames = DB::table('odbusmaster.mst_routes_operators as ro')
                ->leftJoin('users as u', 'u.id', '=', 'ro.operator_id')
                ->where('ro.route_id', $route->id)
                ->where('ro.active_status', 1)
                ->select(
                    DB::raw("COALESCE(NULLIF(TRIM(u.name), ''), 'Demo Operator') as operator_name")
                )
                ->pluck('operator_name')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            if (empty($operatorNames)) {
                $operatorNames = ['Demo Operator'];
            }

            $operatorsListHtml = collect($operatorNames)
                ->map(fn($name) => '<li>' . e($name ?: 'Demo Operator') . '</li>')
                ->implode('');

            /*
        |--------------------------------------------------------------------------
        | Bus Types HTML
        |--------------------------------------------------------------------------
        */
            $busTypes = DB::table('odbusmaster.mst_routes_bus_types')
                ->where('route_id', $route->id)
                ->where('active_status', 1)
                ->pluck('bus_description')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            $busTypesHtml = collect($busTypes)
                ->map(fn($item) => '<li>' . e($item) . '</li>')
                ->implode('');

            /*
        |--------------------------------------------------------------------------
        | Boarding Points HTML (type = 1)
        |--------------------------------------------------------------------------
        */
            $boardingPoints = DB::table('odbusmaster.mst_route_brd_drp as rbd')
                ->join('odbusmaster.mst_boarding_droping as bd', 'bd.id', '=', 'rbd.brd_drp_id')
                ->where('rbd.route_id', $route->id)
                ->where('rbd.type', 1)
                ->where('rbd.active_status', 1)
                ->pluck('bd.brd_drp_point')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            $boardingPointsHtml = collect($boardingPoints)
                ->map(fn($item) => '<li>' . e($item) . '</li>')
                ->implode('');

            /*
        |--------------------------------------------------------------------------
        | Dropping Points HTML (type = 2)
        |--------------------------------------------------------------------------
        */
            $droppingPoints = DB::table('odbusmaster.mst_route_brd_drp as rbd')
                ->join('odbusmaster.mst_boarding_droping as bd', 'bd.id', '=', 'rbd.brd_drp_id')
                ->where('rbd.route_id', $route->id)
                ->where('rbd.type', 2)
                ->where('rbd.active_status', 1)
                ->pluck('bd.brd_drp_point')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            $droppingPointsHtml = collect($droppingPoints)
                ->map(fn($item) => '<li>' . e($item) . '</li>')
                ->implode('');

            /*
        |--------------------------------------------------------------------------
        | Source / Destination City Content
        |--------------------------------------------------------------------------
        */
            $sourceContent = DB::table('odbusmaster.mst_city_content')
                ->where('city_id', $route->source_id)
                ->where('active_status', 1)
                ->value('content') ?? '';

            $destinationContent = DB::table('odbusmaster.mst_city_content')
                ->where('city_id', $route->destination_id)
                ->where('active_status', 1)
                ->value('content') ?? '';

            /*
        |--------------------------------------------------------------------------
        | Duration split if you still need from_hrs / to_hrs for FAQ placeholders
        |--------------------------------------------------------------------------
        */
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

                    // main route values
                    'distance'              => $route->distance,
                    'duration_in_hours'     => $route->duration_in_hours,
                    'from_hrs'              => $fromHrs,
                    'to_hrs'                => $toHrs,
                    'first_bus_timing'      => $route->first_bus_timing,
                    'last_bus_timing'       => $route->last_bus_timing,
                    'min_fare'              => $route->min_fare,
                    'max_fare'              => $route->max_fare,
                    'bus_count'             => $route->bus_count,
                    'operators_count'       => $route->operators_count,

                    // saved editable content
                    'content'               =>  '',
                    'meta_title'            =>  '',
                    'meta_description'      =>  '',
                    'breadcrumb_schema'     => $route->breadcrumb_schema ?? '',
                    'faq_schema'            => $route->faq_schema ?? '',

                    // generated helper values for content replacement
                    'operators_list_html'   => $operatorsListHtml,
                    'bus_types_html'        => $busTypesHtml,
                    'boarding_points_html'  => $boardingPointsHtml,
                    'dropping_points_html'  => $droppingPointsHtml,
                    'source_content'        => $sourceContent,
                    'destination_content'   => $destinationContent,
                ]
            ]);
        } catch (\Throwable $t) {
            Log::info("Exception occurred in ManageTemplateController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace'         => $t->getTraceAsString()
            ]);

            Log::error("Error", [
                'Controller' => 'ManageTemplateController',
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
            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'route_id'           => 'required|integer',
                    'content'            => 'nullable',
                    'meta_title'         => 'nullable|string',
                    'meta_description'   => 'nullable|string',
                    'breadcrumb_schema'  => 'nullable',
                    'faq_schema'         => 'nullable',
                ], [
                    'route_id.required' => 'Route is required.'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status'  => false,
                        'message' => $validator->errors()->first()
                    ]);
                }

                $routeId = (int) request('route_id');

                DB::beginTransaction();

                $route = DB::table('odbusmaster.mst_routes_details')
                    ->select('id')
                    ->where('id', $routeId)
                    ->first();

                if (!$route) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => false,
                        'message' => 'Selected route not found.'
                    ]);
                }

                DB::table('odbusmaster.mst_routes_details')
                    ->where('id', $routeId)
                    ->update([
                        'content'            => request('content'),
                        'meta_title'         => request('meta_title'),
                        'meta_description'   => request('meta_description'),
                        'breadcrumb_schema'  => request('breadcrumb_schema'),
                        'faq_schema'         => request('faq_schema'),
                        'updated_at'         => now(),
                        'updated_by'         => 1
                    ]);

                DB::commit();

                return response()->json([
                    'status'  => true,
                    'message' => 'Template details updated successfully.'
                ]);
            }
        } catch (\Throwable $t) {
            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'ManageTemplateController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return response()->json([
                'status'  => false,
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ]);
        }

        return view('admin.ManageRouteSEO.manageTemplate', compact('data'));
    }
}
