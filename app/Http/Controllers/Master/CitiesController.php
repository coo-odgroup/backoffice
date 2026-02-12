<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CitiesController extends Controller
{

    public function cities()
    {
        return view('master.cities');
    }

    public function addCities()
    {
        return view('master.addCities');
    }

    // public function dataTable(Request $request)
    // {
    //     $recordsTotal     = 0;
    //     $recordsFiltered  = 0;
    //     $data             = [];


    //     $columns = [
    //         0 => 'id',
    //         1 => 'id',`
    //         2 => 'city_name',
    //         3 => 'city_alias',
    //         4 => 'state_name',
    //         5 => 'created_at',
    //         6 => 'is_active'
    //     ];

    //     $limit  = $request->input('length');
    //     $start  = $request->input('start');
        
    //     $orderColumnIndex = $request->input('order.0.column');
    //     $orderColumn = $columns[$orderColumnIndex] ?? 'id';
    //     $orderDir = $request->input('order.0.dir') ?? 'asc';

    //     // Base Query
    //     $query = DB::table('cities as c')
    //         ->leftJoin('states as s', 's.id', '=', 'c.state_id')
    //         ->select(
    //             'c.id as city_id',
    //             'c.city_name',
    //             'c.alias',
    //             's.state_name',
    //             'c.created_at',
    //             DB::raw("CASE WHEN c.active_status = 1 THEN 'Active' ELSE 'Inactive' END as is_active")
    //         );

    //     // Total Records
    //     $totalData = $query->count();

    //     // ===========================
    //     // Apply Filters (POST Search)
    //     // ===========================

    //     if ($request->filled('txtservice')) {
    //         $search = $request->txtservice;
    //         $query->where(function ($q) use ($search) {
    //             $q->where('c.city_name', 'like', "%{$search}%")
    //             ->orWhere('c.alias', 'like', "%{$search}%")
    //             ->orWhere('s.state_name', 'like', "%{$search}%");
    //         });
    //     }

    //     if ($request->filled('selstatus') && $request->selstatus != 0) {
    //         $query->where('c.active_status', $request->selstatus);
    //     }

    //     // Count After Filter
    //     $totalFiltered = $query->count();

    //     // Order first
    //     $query->orderBy($orderColumn, $orderDir);

    //     // Apply pagination only if not "All"
    //     if ($limit != -1) {
    //         $query->offset($start)->limit($limit);
    //     }

    //     $cities = $query->get(); 

    //     $data = [];
    //     $slNo = $start + 1;

    //     foreach ($cities as $row) {
    //         $nestedData = [];
    //         $nestedData['slNo'] = $slNo++;
    //         $nestedData['city_name'] = $row->city_name;
    //         $nestedData['city_alias'] = $row->alias ?? '--';
    //         $nestedData['state_name'] = $row->state_name ?? '--';
    //         $nestedData['created_date'] = date('d-m-Y', strtotime($row->created_at));
    //         $nestedData['is_active'] = $row->is_active;
    //         $nestedData['enc_city_id'] = encrypt($row->city_id);

    //         $data[] = $nestedData;
    //     }

    //     // ===========================
    //     // Return JSON
    //     // ===========================

    //     return response()->json([
    //         "draw"            => intval($request->input('draw')),
    //         "recordsTotal"    => intval($totalData),
    //         "recordsFiltered" => intval($totalFiltered),
    //         "data"            => $data
    //     ]);
    // }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {
            
            $txtSearch= htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $selState = (int) request('selState');
            $selDistrict = (int) request('selDistrict');

            log::info('CitiesController@dataTableView called', [
                'txtSearch' => $txtSearch,
                'selStatus' => $selStatus,
                'selState' => $selState,
                'selDistrict' => $selDistrict
            ]);

            $dataQuery = DB::table('cities as c')
                ->leftJoin('states as s', 's.id', '=', 'c.state_id')
                ->select(
                    'c.id as city_id',
                    'c.city_name',
                    'c.alias',
                    's.state_name',
                    'c.created_at',
                    'c.active_status'
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('c.city_name', 'like', "%{$txtSearch}%")
                    ->orWhere('c.alias', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($selStatus) && $selStatus != '') {
                log::info('Applying status filter', ['selStatus' => $selStatus]);
                $dataQuery->where('c.active_status', $selStatus);
            }

            if ($selState > 0) {
                $dataQuery->where('c.state_id', $selState);
            }

            if ($selDistrict > 0) {
                $dataQuery->where('c.district_id', $selDistrict);
            }

            $count = $dataQuery->count('c.id');

            $start  = request('start');
            $length = request('length');

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 's.state_name', 3 => 'c.city_name', 4 => 'c.alias', 5 => 'c.synonymn', 6 => 'c.created_at', 7 => 'c.active_status'];

                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 'c.city_name';
                $orderType     = $orderBy[0]['dir'];

            } else {
                $orderColumn = 'c.city_name';
                $orderType   = 'asc';
            }

            $dataQuery = $dataQuery->orderBy($orderColumn, $orderType);

            // Pagination
            if ($length == -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery->limit($length)
                                    ->offset($start)
                                    ->get();
            }

            // Format Data
            if (count($arrRes) > 0) {

                foreach ($arrRes as $val) {

                    $val->city_alias    = $val->alias ?? '--';
                    $val->created_date  = date('d-m-Y', strtotime($val->created_at));
                    $val->is_active     = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_city_id   = encrypt($val->city_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;

        } catch (\Throwable $t) {

            Log::error("Error", [
                'Controller' => 'CityController',
                'Method'     => 'dataTableView',
                'Error'      => $t->getMessage()
            ]);

            $recordsTotal     = 0;
            $recordsFiltered  = 0;
            $data             = [];
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

}
