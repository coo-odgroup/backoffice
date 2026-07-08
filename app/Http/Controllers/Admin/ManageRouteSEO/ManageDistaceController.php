<?php

namespace App\Http\Controllers\Admin\ManageRouteSEO;

use App\Http\Controllers\Controller;
use App\Models\ManageRouteSEO\ManageDistace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CommonController;


class ManageDistaceController extends Controller
{
    public function index()
    {
        return view('admin.ManageRouteSEO.manageDistance');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {
            $routeId    = request('route_id');
            $locationId = request('selCity');

            $rows = collect();


            if (!empty($routeId)) {

                $mainRoute = DB::table('odbusmaster.mst_routes_details as rd')
                    ->leftJoin('users as uc', 'uc.id', '=', 'rd.created_by')
                    ->leftJoin('users as uu', 'uu.id', '=', 'rd.updated_by')
                    ->select(
                        'rd.id',
                        'rd.source_id',
                        'rd.destination_id',
                        'rd.source',
                        'rd.destination',
                        'rd.distance',
                        'rd.active_status',
                        'rd.created_at',
                        'rd.created_by',
                        'rd.updated_at',
                        'rd.updated_by',
                        'uc.name as created_by_name',
                        'uu.name as updated_by_name'
                    )
                    ->where('rd.id', $routeId)
                    ->first();

                if ($mainRoute) {

                    // reverse main route
                    $mainReverse = DB::table('odbusmaster.mst_routes_details as rd')
                        ->select('rd.id', 'rd.source_id', 'rd.destination_id', 'rd.source', 'rd.destination', 'rd.distance')
                        ->where('rd.source_id', $mainRoute->destination_id)
                        ->where('rd.destination_id', $mainRoute->source_id)
                        ->first();

                    $mainRow = new \stdClass();
                    $mainRow->id = $mainRoute->id;
                    $mainRow->route_id_1 = Crypt::encryptString($mainRoute->id);
                    $mainRow->route_id_2 = !empty($mainReverse->id) ? Crypt::encryptString($mainReverse->id) : null;
                    $mainRow->route_name_1 = $mainRoute->source . ' to ' . $mainRoute->destination;
                    $mainRow->distance_1 = !empty($mainRoute->distance) ? $mainRoute->distance : '';
                    $mainRow->route_name_2 = $mainReverse ? ($mainReverse->source . ' to ' . $mainReverse->destination) : '--';
                    $mainRow->distance_2 = $mainReverse && !empty($mainReverse->distance) ? $mainReverse->distance : '--';
                    $mainRow->active_status = $mainRoute->active_status;
                    $mainRow->created_at = $mainRoute->created_at;
                    $mainRow->created_by = $mainRoute->created_by;
                    $mainRow->updated_at = $mainRoute->updated_at;
                    $mainRow->updated_by = $mainRoute->updated_by;
                    $mainRow->created_by_name = $mainRoute->created_by_name;
                    $mainRow->updated_by_name = $mainRoute->updated_by_name;
                    $rows->push($mainRow);
                    $mainPairKey = min($mainRoute->source_id, $mainRoute->destination_id) . '_' . max($mainRoute->source_id, $mainRoute->destination_id);
                    $processedPairs = [$mainPairKey => true];

                    // sub routes
                    $subRoutes = DB::table('odbusmaster.mst_route_map as rm')
                        ->join('odbusmaster.mst_routes_details as rd', 'rd.id', '=', 'rm.route_id')
                        ->leftJoin('users as uc', 'uc.id', '=', 'rd.created_by')
                        ->leftJoin('users as uu', 'uu.id', '=', 'rd.updated_by')
                        ->select(
                            'rd.id',
                            'rd.source_id',
                            'rd.destination_id',
                            'rd.source',
                            'rd.destination',
                            'rd.distance',
                            'rd.active_status',
                            'rd.created_at',
                            'rd.created_by',
                            'rd.updated_at',
                            'rd.updated_by',
                            'uc.name as created_by_name',
                            'uu.name as updated_by_name'
                        )
                        ->where('rm.parent_route_id', $routeId)
                        ->where('rm.is_main_route', 0)
                        ->get();

                    // if location also selected along with route, filter subroutes
                    if (!empty($locationId)) {
                        $subRoutes = $subRoutes->filter(function ($row) use ($locationId) {
                            return $row->source_id == $locationId || $row->destination_id == $locationId;
                        })->values();
                    }

                    $subRoutes = $subRoutes->sortByDesc(function ($row) {
                        return $this->extractDistanceValue($row->distance);
                    })->values();
                    foreach ($subRoutes as $sub) {
                        $subPairKey = min($sub->source_id, $sub->destination_id) . '_' . max($sub->source_id, $sub->destination_id);

                        // skip if this pair is already added (main route or earlier subroute reverse)
                        if (isset($processedPairs[$subPairKey])) {
                            continue;
                        }

                        $subReverse = DB::table('odbusmaster.mst_routes_details as rd')
                            ->select('rd.id', 'rd.source_id', 'rd.destination_id', 'rd.source', 'rd.destination', 'rd.distance')
                            ->where('rd.source_id', $sub->destination_id)
                            ->where('rd.destination_id', $sub->source_id)
                            ->first();

                        $row = new \stdClass();
                        $row->id = $sub->id;
                        $row->route_id_1 = Crypt::encryptString($sub->id);
                        $row->route_id_2 = !empty($subReverse->id) ? Crypt::encryptString($subReverse->id) : null;
                        $row->route_name_1 = $sub->source . ' to ' . $sub->destination;
                        $row->distance_1 = !empty($sub->distance) ? $sub->distance : '';
                        $row->route_name_2 = $subReverse ? ($subReverse->source . ' to ' . $subReverse->destination) : '--';
                        $row->distance_2 = $subReverse && !empty($subReverse->distance) ? $subReverse->distance : '--';
                        $row->active_status = $sub->active_status;
                        $row->created_at = $sub->created_at;
                        $row->created_by = $sub->created_by;
                        $row->updated_at = $sub->updated_at;
                        $row->updated_by = $sub->updated_by;
                        $row->created_by_name = $sub->created_by_name;
                        $row->updated_by_name = $sub->updated_by_name;

                        $rows->push($row);
                        $processedPairs[$subPairKey] = true;
                    }
                }
            } elseif (!empty($locationId)) {

                $mainRoutes = DB::table('odbusmaster.mst_routes_details as rd')
                    ->leftJoin('users as uc', 'uc.id', '=', 'rd.created_by')
                    ->leftJoin('users as uu', 'uu.id', '=', 'rd.updated_by')
                    ->select(
                        'rd.id',
                        'rd.source_id',
                        'rd.destination_id',
                        'rd.source',
                        'rd.destination',
                        'rd.distance',
                        'rd.active_status',
                        'rd.created_at',
                        'rd.created_by',
                        'rd.updated_at',
                        'rd.updated_by',
                        'uc.name as created_by_name',
                        'uu.name as updated_by_name'
                    )
                    ->where('rd.is_main_route', 1)
                    ->where(function ($q) use ($locationId) {
                        $q->where('rd.source_id', $locationId)
                            ->orWhere('rd.destination_id', $locationId);
                    })
                    ->orderBy('rd.id', 'asc')
                    ->get();

                $processedPairs = [];

                foreach ($mainRoutes as $route) {
                    $pairKey = min($route->source_id, $route->destination_id) . '_' . max($route->source_id, $route->destination_id);

                    if (isset($processedPairs[$pairKey])) {
                        continue;
                    }

                    $reverse = DB::table('odbusmaster.mst_routes_details as rd')
                        ->select('rd.id', 'rd.source_id', 'rd.destination_id', 'rd.source', 'rd.destination', 'rd.distance')
                        ->where('rd.source_id', $route->destination_id)
                        ->where('rd.destination_id', $route->source_id)
                        ->first();

                    $row = new \stdClass();
                    $row->id = $route->id;
                    $row->route_id_1 = Crypt::encryptString($route->id);
                    $row->route_id_2 = !empty($reverse->id) ? Crypt::encryptString($reverse->id) : null;

                    $row->route_name_1 = $route->source . ' to ' . $route->destination;
                    $row->distance_1   = !empty($route->distance) ? $route->distance : '';
                    $row->route_name_2 = $reverse ? ($reverse->source . ' to ' . $reverse->destination) : '--';
                    $row->distance_2   = $reverse && !empty($reverse->distance) ? $reverse->distance : '--';

                    $row->active_status   = $route->active_status;
                    $row->created_at      = $route->created_at;
                    $row->created_by      = $route->created_by;
                    $row->updated_at      = $route->updated_at;
                    $row->updated_by      = $route->updated_by;
                    $row->created_by_name = $route->created_by_name;
                    $row->updated_by_name = $route->updated_by_name;

                    $rows->push($row);

                    $processedPairs[$pairKey] = true;
                }
            } else {
                return response()->json([
                    'recordsTotal'    => 0,
                    'recordsFiltered' => 0,
                    'data'            => [],
                ]);
            }

            $recordsTotal = $rows->count();
            $recordsFiltered = $recordsTotal;

            foreach ($rows as $val) {
                $val->created_date = !empty($val->created_at) ? date('d-M-Y H:i:s', strtotime($val->created_at)) : '--';
                $val->updated_date = !empty($val->updated_at) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : '--';
                $val->is_active    = ($val->active_status == 1) ? 'Active' : 'Inactive';
                $val->enc_id       = Crypt::encryptString($val->id);
            }

            $data = $rows->values();
        } catch (\Throwable $t) {
            Log::info("Exception occurred in ManageDistaceController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            Log::error("Error", [
                'Controller' => 'ManageDistaceController',
                'Method'     => 'dataTableView',
                'Error'      => $t->getMessage()
            ]);

            $recordsTotal = 0;
            $recordsFiltered = 0;
            $data = [];
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    private function extractDistanceValue($distance)
    {
        if (empty($distance)) {
            return 0;
        }

        preg_match('/\d+/', (string) $distance, $match);
        return isset($match[0]) ? (int) $match[0] : 0;
    }

    public function updateDistance(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'enc_id'   => 'required',
                'distance' => 'required',
                'type'     => 'required|in:distance_1,distance_2'
            ], [
                'distance.required' => 'Distance is required.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $id = Crypt::decryptString($request->enc_id);

            $row = DB::table('odbusmaster.mst_routes_details')->where('id', $id)->first();

            if (!$row) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Route not found.'
                ]);
            }

            DB::table('odbusmaster.mst_routes_details')
                ->where('id', $id)
                ->update([
                    'distance'   => trim($request->distance),
                    'updated_at' => now(),
                    'updated_by' => session('admin_user_id') ?? auth()->id() ?? 1
                ]);

            return response()->json([
                'status'  => true,
                'message' => 'Distance updated successfully.'
            ]);
        } catch (\Throwable $t) {
            Log::error("ManageDistaceController@updateDistance Error", [
                'message' => $t->getMessage(),
                'trace'   => $t->getTraceAsString()
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while updating distance.'
            ]);
        }
    }

    public function exportCsv(Request $request)
    {
        try {
            $routeId    = $request->route_id;
            $locationId = $request->selCity;

            $rows = $this->getRouteDistanceListing($routeId, $locationId);

            if ($rows->isEmpty()) {
                return back()->with('error', 'No data found to export.');
            }

            $fileName = 'route_distance_' . date('Ymd_His') . '.csv';

            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ];

            $callback = function () use ($rows) {
                $file = fopen('php://output', 'w');

                // BOM for Excel UTF-8 support
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                fputcsv($file, [
                    'Sl No',
                    'Location 1',
                    'Distance 1',
                    'Location 2',
                    'Distance 2'
                ]);

                $sl = 1;
                foreach ($rows as $row) {
                    fputcsv($file, [
                        $sl++,
                        $row->route_name_1 ?? '',
                        $row->distance_1 ?? '',
                        $row->route_name_2 ?? '',
                        $row->distance_2 ?? '',
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Throwable $e) {
            Log::error("ManageDistaceController@exportCsv Error", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to export CSV.');
        }
    }

    public function importCsv(Request $request)
    {
        try {
            $request->validate([
                'csv_file' => 'required|mimes:csv,txt'
            ], [
                'csv_file.required' => 'Please upload a CSV file.',
                'csv_file.mimes'    => 'Only CSV file is allowed.'
            ]);

            $file = $request->file('csv_file');
            $routeId    = $request->route_id;
            $locationId = $request->selCity;

            $isLocationOnlyImport = empty($routeId) && !empty($locationId);

            if (($handle = fopen($file->getRealPath(), 'r')) === false) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unable to read uploaded file.'
                ]);
            }

            DB::beginTransaction();

            $rowNumber    = 0;
            $updatedCount = 0;
            $updatedBy    = session('admin_user_id') ?? auth()->id() ?? 1;

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;

                if ($rowNumber == 1) {
                    continue;
                }

                $location1 = isset($row[1]) ? trim($row[1]) : '';
                $distance1 = isset($row[2]) ? trim($row[2]) : '';
                $location2 = isset($row[3]) ? trim($row[3]) : '';
                $distance2 = isset($row[4]) ? trim($row[4]) : '';

                if ($location1 === '' && $location2 === '') {
                    continue;
                }

                // ---------------- route 1 ----------------
                if ($location1 !== '' && $distance1 !== '') {
                    $parts1 = array_map('trim', explode(' to ', $location1));
                    if (count($parts1) === 2) {
                        [$source1, $destination1] = $parts1;

                        $query1 = DB::table('odbusmaster.mst_routes_details')
                            ->where('source', $source1)
                            ->where('destination', $destination1);



                        if (!empty($locationId)) {
                            $query1->where(function ($q) use ($locationId) {
                                $q->where('source_id', $locationId)
                                    ->orWhere('destination_id', $locationId);
                            });
                        }

                        $route1 = $query1->first();


                        if ($route1) {
                            $affected1 = DB::table('odbusmaster.mst_routes_details')
                                ->where('id', $route1->id)
                                ->update([
                                    'distance'   => $distance1,
                                    'updated_at' => now(),
                                    'updated_by' => $updatedBy
                                ]);

                            Log::info('CSV import route1 update', [
                                'rowNumber' => $rowNumber,
                                'route_id'  => $route1->id,
                                'distance'  => $distance1,
                                'affected'  => $affected1
                            ]);

                            if ($affected1 > 0) {
                                $updatedCount++;
                            }
                        }
                    }
                }

                // ---------------- route 2 ----------------
                if (!$isLocationOnlyImport && $location2 !== '' && $location2 !== '--' && $distance2 !== '') {
                    $parts2 = array_map('trim', explode(' to ', $location2));
                    if (count($parts2) === 2) {
                        [$source2, $destination2] = $parts2;

                        $query2 = DB::table('odbusmaster.mst_routes_details')
                            ->where('source', $source2)
                            ->where('destination', $destination2);

                        if (!empty($locationId)) {
                            $query2->where(function ($q) use ($locationId) {
                                $q->where('source_id', $locationId)
                                    ->orWhere('destination_id', $locationId);
                            });
                        }

                        $route2 = $query2->first();

                        if ($route2) {
                            $affected2 = DB::table('odbusmaster.mst_routes_details')
                                ->where('id', $route2->id)
                                ->update([
                                    'distance'   => $distance2,
                                    'updated_at' => now(),
                                    'updated_by' => $updatedBy
                                ]);

                            Log::info('CSV import route2 update', [
                                'rowNumber' => $rowNumber,
                                'route_id'  => $route2->id,
                                'distance'  => $distance2,
                                'affected'  => $affected2
                            ]);

                            if ($affected2 > 0) {
                                $updatedCount++;
                            }
                        }
                    }
                }
            }

            fclose($handle);
            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => "CSV uploaded successfully. {$updatedCount} rows updated."
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error("ManageDistaceController@importCsv Error", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    private function getRouteDistanceListing($routeId = null, $locationId = null)
    {
        $rows = collect();

        if (!empty($routeId)) {

            $mainRoute = DB::table('odbusmaster.mst_routes_details as rd')
                ->leftJoin('users as uc', 'uc.id', '=', 'rd.created_by')
                ->leftJoin('users as uu', 'uu.id', '=', 'rd.updated_by')
                ->select(
                    'rd.id',
                    'rd.source_id',
                    'rd.destination_id',
                    'rd.source',
                    'rd.destination',
                    'rd.distance',
                    'rd.active_status',
                    'rd.created_at',
                    'rd.created_by',
                    'rd.updated_at',
                    'rd.updated_by',
                    'uc.name as created_by_name',
                    'uu.name as updated_by_name'
                )
                ->where('rd.id', $routeId)
                ->first();

            if ($mainRoute) {

                $mainReverse = DB::table('odbusmaster.mst_routes_details as rd')
                    ->select('rd.id', 'rd.source_id', 'rd.destination_id', 'rd.source', 'rd.destination', 'rd.distance')
                    ->where('rd.source_id', $mainRoute->destination_id)
                    ->where('rd.destination_id', $mainRoute->source_id)
                    ->first();

                $mainRow = new \stdClass();
                $mainRow->id = $mainRoute->id;

                // for inline edit
                $mainRow->route_id_1 = Crypt::encryptString($mainRoute->id);
                $mainRow->route_id_2 = !empty($mainReverse->id) ? Crypt::encryptString($mainReverse->id) : null;

                // for CSV export/import
                $mainRow->raw_id_1 = $mainRoute->id;
                $mainRow->raw_id_2 = $mainReverse->id ?? null;

                $mainRow->route_name_1 = $mainRoute->source . ' to ' . $mainRoute->destination;
                $mainRow->distance_1 = !empty($mainRoute->distance) ? $mainRoute->distance : '';
                $mainRow->route_name_2 = $mainReverse ? ($mainReverse->source . ' to ' . $mainReverse->destination) : '--';
                $mainRow->distance_2 = $mainReverse && !empty($mainReverse->distance) ? $mainReverse->distance : '--';
                $rows->push($mainRow);

                $subRoutes = DB::table('odbusmaster.mst_route_map as rm')
                    ->join('odbusmaster.mst_routes_details as rd', 'rd.id', '=', 'rm.route_id')
                    ->leftJoin('users as uc', 'uc.id', '=', 'rd.created_by')
                    ->leftJoin('users as uu', 'uu.id', '=', 'rd.updated_by')
                    ->select(
                        'rd.id',
                        'rd.source_id',
                        'rd.destination_id',
                        'rd.source',
                        'rd.destination',
                        'rd.distance',
                        'rd.active_status',
                        'rd.created_at',
                        'rd.created_by',
                        'rd.updated_at',
                        'rd.updated_by',
                        'uc.name as created_by_name',
                        'uu.name as updated_by_name'
                    )
                    ->where('rm.parent_route_id', $routeId)
                    ->where('rm.is_main_route', 0)
                    ->get();

                if (!empty($locationId)) {
                    $subRoutes = $subRoutes->filter(function ($row) use ($locationId) {
                        return $row->source_id == $locationId || $row->destination_id == $locationId;
                    })->values();
                }

                $subRoutes = $subRoutes->sortByDesc(function ($row) {
                    return $this->extractDistanceValue($row->distance);
                })->values();

                foreach ($subRoutes as $sub) {
                    $subReverse = DB::table('odbusmaster.mst_routes_details as rd')
                        ->select('rd.id', 'rd.source_id', 'rd.destination_id', 'rd.source', 'rd.destination', 'rd.distance')
                        ->where('rd.source_id', $sub->destination_id)
                        ->where('rd.destination_id', $sub->source_id)
                        ->first();

                    $row = new \stdClass();
                    $row->id = $sub->id;

                    // for inline edit
                    $row->route_id_1 = Crypt::encryptString($sub->id);
                    $row->route_id_2 = !empty($subReverse->id) ? Crypt::encryptString($subReverse->id) : null;

                    // for CSV export/import
                    $row->raw_id_1 = $sub->id;
                    $row->raw_id_2 = $subReverse->id ?? null;

                    $row->route_name_1 = $sub->source . ' to ' . $sub->destination;
                    $row->distance_1 = !empty($sub->distance) ? $sub->distance : '';
                    $row->route_name_2 = $subReverse ? ($subReverse->source . ' to ' . $subReverse->destination) : '--';
                    $row->distance_2 = $subReverse && !empty($subReverse->distance) ? $subReverse->distance : '--';
                    $rows->push($row);
                }
            }
        } elseif (!empty($locationId)) {

            $mainRoutes = DB::table('odbusmaster.mst_routes_details as rd')
                ->leftJoin('users as uc', 'uc.id', '=', 'rd.created_by')
                ->leftJoin('users as uu', 'uu.id', '=', 'rd.updated_by')
                ->select(
                    'rd.id',
                    'rd.source_id',
                    'rd.destination_id',
                    'rd.source',
                    'rd.destination',
                    'rd.distance',
                    'rd.active_status',
                    'rd.created_at',
                    'rd.created_by',
                    'rd.updated_at',
                    'rd.updated_by',
                    'uc.name as created_by_name',
                    'uu.name as updated_by_name'
                )
                ->where('rd.is_main_route', 1)
                ->where(function ($q) use ($locationId) {
                    $q->where('rd.source_id', $locationId)
                        ->orWhere('rd.destination_id', $locationId);
                })
                ->orderBy('rd.id', 'asc')
                ->get();

            $processedPairs = [];

            foreach ($mainRoutes as $route) {
                // same pair key for A->B and B->A
                $pairKey = min($route->source_id, $route->destination_id) . '_' . max($route->source_id, $route->destination_id);

                // if already shown, skip this reverse duplicate
                if (isset($processedPairs[$pairKey])) {
                    continue;
                }

                $reverse = DB::table('odbusmaster.mst_routes_details as rd')
                    ->select('rd.id', 'rd.source_id', 'rd.destination_id', 'rd.source', 'rd.destination', 'rd.distance')
                    ->where('rd.source_id', $route->destination_id)
                    ->where('rd.destination_id', $route->source_id)
                    ->first();

                $row = new \stdClass();
                $row->id = $route->id;
                $row->route_id_1 = Crypt::encryptString($route->id);
                $row->route_id_2 = !empty($reverse->id) ? Crypt::encryptString($reverse->id) : null;

                $row->route_name_1 = $route->source . ' to ' . $route->destination;
                $row->distance_1   = !empty($route->distance) ? $route->distance : '';
                $row->route_name_2 = $reverse ? ($reverse->source . ' to ' . $reverse->destination) : '--';
                $row->distance_2   = $reverse && !empty($reverse->distance) ? $reverse->distance : '--';

                $row->active_status    = $route->active_status;
                $row->created_at       = $route->created_at;
                $row->created_by       = $route->created_by;
                $row->updated_at       = $route->updated_at;
                $row->updated_by       = $route->updated_by;
                $row->created_by_name  = $route->created_by_name;
                $row->updated_by_name  = $route->updated_by_name;

                $rows->push($row);

                // mark this A<->B pair as already added
                $processedPairs[$pairKey] = true;
            }
        }

        return $rows->values();
    }
}
