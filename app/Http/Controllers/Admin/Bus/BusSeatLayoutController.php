<?php

namespace App\Http\Controllers\Admin\Bus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusSeatLayoutController extends Controller
{
    public function index($id)
    {

        // $seat_layout = DB::table('mst_seats')->where('seat_layout_name_id',1)->get();
        // $seats = DB::table('mst_seats')
        //     ->where('seat_layout_name_id', 5)
        //     ->orderBy('row_number')
        //     ->orderBy('col_number')
        //     ->get();

        // $layout = [
        //     'UPPER' => [],
        //     'LOWER' => []
        // ];

        // foreach ($seats as $seat) {

        //     $deck = $seat->berth_type == 1 ? 'LOWER' : 'UPPER';

        //     $layout[$deck][$seat->row_number][$seat->col_number] = $seat;
        // }


        // return $layout;

        // // return $seat_layout;

        // return view('admin.Bus.wizard.busSeatLayout',compact('layout'));



        $seatLayoutId = $id;


        $seats = DB::table('mst_seats')
            ->where('seat_layout_name_id', $seatLayoutId)
            ->orderBy('row_number')
            ->orderBy('col_number')
            ->get();

        $layout = [
            'UPPER' => [],
            'LOWER' => []
        ];

        foreach ($seats as $seat) {

            $deck = $seat->berth_type == 1 ? 'LOWER' : 'UPPER';

            $layout[$deck][$seat->row_number][$seat->col_number] = $seat;
        }

        foreach ($layout as $deck => $rows) {

            ksort($rows); // sort rows

            foreach ($rows as $rowKey => $cols) {
                ksort($cols); // sort columns
                $rows[$rowKey] = $cols;
            }

            $layout[$deck] = $rows;
        }

        $maxCols = [
            'UPPER' => 0,
            'LOWER' => 0
        ];

        foreach ($layout as $deck => $rows) {
            foreach ($rows as $cols) {
                if (!empty($cols)) {
                    $maxCols[$deck] = max($maxCols[$deck], max(array_keys($cols)));
                }
            }
        }

        return view('admin.Bus.wizard.busSeatLayout', [
            'layout' => $layout,
            'maxCols' => $maxCols
        ]);
    }
}
