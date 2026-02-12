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
        log::info('CitiesController@cities called');
        return view('master.cities');
    }

    public function addCities()
    {
        return view('master.addCities');
    }

    public function dataTable(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'id',
            2 => 'city_name',
            3 => 'city_alias',
            4 => 'state_name',
            5 => 'created_at',
            6 => 'is_active'
        ];

        $limit  = $request->input('length');
        $start  = $request->input('start');
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir') ?? 'asc';

        // Base Query
        $query = DB::table('cities as c')
            ->leftJoin('states as s', 's.id', '=', 'c.state_id')
            ->select(
                'c.id as city_id',
                'c.city_name',
                'c.alias',
                's.state_name',
                'c.created_at',
                DB::raw("CASE WHEN c.active_status = 1 THEN 'Active' ELSE 'Inactive' END as is_active")
            );

        // Total Records
        $totalData = $query->count();

        // ===========================
        // Apply Filters (POST Search)
        // ===========================

        if ($request->filled('txtservice')) {
            $search = $request->txtservice;
            $query->where(function ($q) use ($search) {
                $q->where('c.city_name', 'like', "%{$search}%")
                ->orWhere('c.alias', 'like', "%{$search}%")
                ->orWhere('s.state_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('selstatus') && $request->selstatus != 0) {
            $query->where('c.active_status', $request->selstatus);
        }

        // Count After Filter
        $totalFiltered = $query->count();

        // ===========================
        // Pagination + Order
        // ===========================

        $cities = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();


        // ===========================
        // Prepare Data for DataTable
        // ===========================

        $data = [];
        $slNo = $start + 1;

        foreach ($cities as $row) {
            $nestedData = [];
            $nestedData['slNo'] = $slNo++;
            $nestedData['city_name'] = $row->city_name;
            $nestedData['city_alias'] = $row->alias ?? '--';
            $nestedData['state_name'] = $row->state_name ?? '--';
            $nestedData['created_date'] = date('d-m-Y', strtotime($row->created_at));
            $nestedData['is_active'] = $row->is_active;
            $nestedData['enc_city_id'] = encrypt($row->city_id);

            $data[] = $nestedData;
        }

        // ===========================
        // Return JSON
        // ===========================

        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ]);
    }
}
