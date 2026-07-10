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


class ManagePopularRoutesController extends Controller
{
    public function index()
    {
        return view('admin.ManageRouteSEO.managePopularRoutes');
    }

    public function dataTableView()
    {
        $recordsTotal    = 0;
        $recordsFiltered = 0;
        $data            = [];

        try {

            $routeId   = (int) request('route_id');
            $routeType = trim((string) request('route_type'));
            $orderBy   = strtoupper(trim((string) request('order_by')));

            $dataQuery = DB::table('odbusmaster.mst_routes_details as rd')
                ->select(
                    'rd.id',
                    'rd.source_id',
                    'rd.destination_id',
                    'rd.source',
                    'rd.destination',
                    'rd.is_popular_routes',
                    'rd.is_top_routes',
                    DB::raw('COALESCE(rd.sequence_no, 0) as sequence_no')
                )
                ->where('rd.active_status', 1);

            if ($routeId > 0) {
                $dataQuery->where('rd.id', $routeId);
            }

            if ($routeType === 'popular') {
                $dataQuery->where('rd.is_popular_routes', 1);
            }

            if ($routeType === 'top') {
                $dataQuery->where('rd.is_top_routes', 1);
            }

            if ($orderBy === 'ASC') {
                $dataQuery->orderBy('rd.sequence_no', 'asc');
            } elseif ($orderBy === 'DESC') {
                $dataQuery->orderBy('rd.sequence_no', 'desc');
            } else {
                $dataQuery->orderBy('rd.source', 'asc')
                    ->orderBy('rd.destination', 'asc');
            }

            $count = $dataQuery->count();

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int) $start : 0;
            $length = is_numeric($length) ? (int) $length : 10;

            if ($length == -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery->offset($start)->limit($length)->get();
            }

            if ($arrRes->count() > 0) {
                foreach ($arrRes as $val) {
                    $val->route_id          = $val->id;
                    $val->enc_id            = Crypt::encryptString($val->id);
                    $val->route_name        = $val->source . ' to ' . $val->destination;
                    $val->popular           = $val->is_popular_routes;
                    $val->top               = $val->is_top_routes;
                    $val->sequence          = $val->sequence_no;
                }
            }

            $recordsTotal    = $count;
            $recordsFiltered = $count;
            $data            = $arrRes;
        } catch (\Throwable $t) {

            Log::error("Error", [
                'Controller' => 'ManagePopularRoutesController',
                'Method'     => 'dataTableView',
                'Error'      => $t->getMessage()
            ]);

            $recordsTotal    = 0;
            $recordsFiltered = 0;
            $data            = [];
        }

        return response()->json([
            'draw'            => intval(request('draw')),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function add()
    {
        $method = 'Add';

        try {

            if (!request()->isMethod('post')) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid request.'
                ]);
            }

            $routes = request('routes', []);

            if (empty($routes) || !is_array($routes)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'No route data found.'
                ]);
            }

            DB::beginTransaction();

            foreach ($routes as $row) {

                $routeId         = (int) ($row['route_id'] ?? 0);
                $isPopularRoutes = (int) ($row['is_popular_routes'] ?? 0);
                $isTopRoutes     = (int) ($row['is_top_routes'] ?? 0);
                $sequenceNo      = (int) ($row['sequence_no'] ?? 0);

                if ($routeId <= 0) {
                    continue;
                }

                DB::table('odbusmaster.mst_routes_details')
                    ->where('id', $routeId)
                    ->update([
                        'is_popular_routes' => $isPopularRoutes,
                        'is_top_routes'     => $isTopRoutes,
                        'sequence_no'       => $sequenceNo,
                        'updated_at'        => now(),
                        'updated_by'        => 1
                    ]);
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Popular route details updated successfully.'
            ]);
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'ManagePopularRoutesController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return response()->json([
                'status'  => false,
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ]);
        }
    }

    public function updateSequence(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'route_id'          => 'required|integer',
                'sequence_no'       => 'required|integer|min:0',
                'is_popular_routes' => 'nullable|in:0,1',
                'is_top_routes'     => 'nullable|in:0,1',
            ], [
                'route_id.required'    => 'Route ID is required.',
                'sequence_no.required' => 'Sequence is required.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $routeId         = (int) $request->route_id;
            $sequenceNo      = (int) $request->sequence_no;
            $isPopularRoutes = (int) $request->input('is_popular_routes', 0);
            $isTopRoutes     = (int) $request->input('is_top_routes', 0);

            // One route can be either Popular or Top, not both
            if ($isPopularRoutes === 1 && $isTopRoutes === 1) {
                return response()->json([
                    'status'  => false,
                    'message' => 'A route can be either Popular or Top, not both.'
                ]);
            }

            DB::table('odbusmaster.mst_routes_details')
                ->where('id', $routeId)
                ->update([
                    'sequence_no'       => $sequenceNo,
                    'is_popular_routes' => $isPopularRoutes,
                    'is_top_routes'     => $isTopRoutes,
                    'updated_at'        => now(),
                    'updated_by'        => 1
                ]);

            return response()->json([
                'status'  => true,
                'message' => 'Route updated successfully.'
            ]);
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'ManagePopularRoutesController',
                'Method'     => 'updateSequence',
                'Error'      => $t->getMessage()
            ]);

            return response()->json([
                'status'  => false,
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ]);
        }
    }
}
