<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\SeatLayoutName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SeatLayoutController extends Controller
{
    public function index()
    {
        return view('master.seatLayout');
    }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $classSearch = (request('classSearch') !== null && request('classSearch') !== '') ? (int)request('classSearch') : '';

            $dataQuery = DB::table('mst_seat_layout_name as bt')
                ->select(
                    'bt.id as bustype_id',
                    'bt.layout_name',
                    'bt.created_at',
                    'bt.created_by',
                    'bt.updated_at',
                    'bt.updated_by',
                    'bt.active_status',
                    DB::raw('(SELECT name FROM users WHERE id = bt.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = bt.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('bt.bus_type', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($classSearch) && $classSearch != '') {
                $dataQuery->where('bt.class_id', $classSearch);
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('bt.active_status', $selStatus);
            }

            $count = $dataQuery->count('bt.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            // if (!empty(request('order'))) {

            //     $columns = [2 => 'bt.bus_type', 3 => 'bt.created_at', 4 => 'bt.created_by', 5 => 'bt.active_status'];

            //     $orderBy       = request('order');
            //     $orderColumn   = $columns[$orderBy[0]['column']] ?? 'bt.bus_type';
            //     $orderType     = $orderBy[0]['dir'];
            // } else {
            //     $orderColumn = 'bt.bus_type';
            //     $orderType   = 'asc';
            // }

            // $dataQuery = $dataQuery->orderBy($orderColumn, $orderType);

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
                    $val->created_date  = date('d-M-Y H:i:s', strtotime($val->created_at));
                    $val->updated_date  = ($val->updated_at != null) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : null;
                    $val->is_active     = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_bustype_id   = Crypt::encryptString($val->bustype_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in BusTypeController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'BusTypeController',
                'Method'     => 'dataTableView',
                'Error'      => $errorMsg
            ]);

            $recordsTotal     = 0;
            $recordsFiltered  = 0;
            $data            = [];
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    // public function add($encId = null)
    // {

    //     $data = [];
    //     $data['strPage'] = $method = 'Add';
    //     $data['strSubmit'] = 'Submit';
    //     $data['strReset'] = 'Reset';


    //     if (request()->isMethod('post')) {

    //         $windowSeatInput = json_decode(request()->window_seat, true);

    //         $duplicate = SeatLayoutName::where(['layout_name' => request()->layout_name])->get();


    //         if ($duplicate->count() > 0) {
    //             return back()->with([
    //                 'level'     => 'danger',
    //                 'message'   => 'Layout Name Type already exist'
    //             ])->withInput();
    //         } else {
    //             $data = [
    //                 "layout_name" => request()->layout_name,
    //                 "active_status" => 1,
    //                 "created_by" => 1,
    //                 "updated_by" => 1,
    //             ];

    //             // SeatLayoutName::create($data);
    //         }

    //         $seat_layout_name = SeatLayoutName::where('layout_name', request()->layout_name)->firstOrFail();
    //         $seats = json_decode(request()->seat_layout_json, true);

    //         $windowSeatInput = json_decode(request()->window_seat, true);

    //         $windowSeats = [];

    //         if (!empty($windowSeatInput)) {
    //             $windowSeats = array_column($windowSeatInput, 'value');
    //         }

    //         $seats = array_map(function ($seat) use ($seat_layout_name, $windowSeats) {

    //             $seatText = (string)$seat['seat_text'];

    //             $seat['seat_layout_name_id'] = $seat_layout_name->id;

    //             $seat['is_window'] = in_array($seatText, $windowSeats) ? 1 : 0;

    //             $seat['is_aisle'] = is_null($seat['seat_text']) ? 1 : 0;

    //             return $seat;
    //         }, $seats);

    //         try {
    //             DB::table('mst_seats')->insert($seats);
    //             DB::commit();
    //         } catch (\Exception $e) {
    //             DB::rollBack();
    //             return $e->getMessage();
    //         }
    //     }

    //     // try {

    //     //     $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

    //     //     if ($id > 0) {

    //     //         $redirectPage = "admin/bustype/edit/" . $encId;
    //     //         $data['strPage'] = $method = 'Edit';
    //     //         $data['strSubmit'] = 'Update';
    //     //         $data['strReset'] = 'Cancel';

    //     //         $dataResQry = BusType::select('id', 'class_id', 'bus_type');

    //     //         $dataResQry = $dataResQry->where('id', $id)->first();

    //     //         if (empty($dataResQry)) {
    //     //             return redirect("bustype");
    //     //         }
    //     //         $data['row'] = $dataResQry;
    //     //     } else {
    //     //         $id = 0;
    //     //         $redirectPage = "admin/bustype";
    //     //     }

    //     //     if (request()->isMethod('post')) {

    //     //         request()->replace(request()->all());

    //     //         $validator = Validator::make(request()->all(), [
    //     //             'classType' => 'required',
    //     //             'busType' => 'bail|required'
    //     //         ], [
    //     //             'classType.required' => 'Class Name cannot be left blank.',
    //     //             'busType.required' => 'Bus Type Name cannot be left blank.'
    //     //         ]);

    //     //         if ($validator->fails()) {
    //     //             return back()->withErrors($validator)->withInput();
    //     //         } else {
    //     //             DB::beginTransaction();

    //     //             $classType = (int)request('classType');
    //     //             $busType = htmlEncode(request('busType'));

    //     //             $duplicate = BusType::select('id')->where(['bus_type' => $busType]);

    //     //             if ($id != 0) {
    //     //                 $duplicate->where('id', '!=', $id);
    //     //             }

    //     //             if ($duplicate->exists()) {
    //     //                 return back()->with([
    //     //                     'level'     => 'danger',
    //     //                     'message'   => 'Bus Type already exist'
    //     //                 ])->withInput();
    //     //             } else {
    //     //                 $obj = ($id != 0) ? BusType::find($id) : new BusType();
    //     //                 $obj->class_id = $classType;
    //     //                 $obj->bus_type = $busType;
    //     //                 $obj->created_by = 1;
    //     //                 $obj->active_status = 1;

    //     //                 if ($id != 0) {
    //     //                     $obj->updated_by = 1;
    //     //                 }

    //     //                 $obj->save();

    //     //                 session()->flash('level', 'success');
    //     //                 session()->flash('message', 'Bus Type ' . (($id != 0) ? 'updated' : 'created') . ' successfully.');
    //     //             }

    //     //             DB::commit();
    //     //             return redirect($redirectPage);
    //     //         }
    //     //     }
    //     // } catch (\Throwable $t) {
    //     //     Log::error("Error", [
    //     //         'Controller' => 'BusTypeController',
    //     //         'Method'     => $method,
    //     //         'Error'      => $t->getMessage()
    //     //     ]);

    //     //     DB::rollBack();

    //     //     $errorMsg = config('constantbt.SERVER_ERROR_MESSAGE');

    //     //     return back()->with([
    //     //         'level'     => 'danger',
    //     //         'message'   => $errorMsg
    //     //     ])->withInput();
    //     // }
    //     return view('Master.addSeatLayout', compact('data'));
    // }

    public function add($encId = null)
    {
        $data = [];
        $data['strPage'] = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        if (request()->isMethod('post')) {

            $exists = SeatLayoutName::where('layout_name', request()->layout_name)->exists();

            if ($exists) {
                return back()->with([
                    'level'   => 'danger',
                    'message' => 'Layout Name already exists'
                ])->withInput();
            }

            DB::beginTransaction();

            try {

                $seat_layout_name = SeatLayoutName::create([
                    "layout_name"  => request()->layout_name,
                    "active_status" => 1,
                    "created_by"   => 1,
                    "updated_by"   => 1,
                ]);

                $seats = json_decode(request()->seat_layout_json, true);

                $windowSeatInput = json_decode(request()->window_seat, true) ?? [];
                $windowSeats = array_column($windowSeatInput, 'value');

                $seats = array_map(function ($seat) use ($seat_layout_name, $windowSeats) {

                    $seatText = (string)$seat['seat_text'];

                    return [
                        "seat_layout_name_id" => $seat_layout_name->id,
                        "seat_class" => $seat['seat_class'],
                        "berth_type" => $seat['berth_type'],
                        "seat_text" => $seat['seat_text'],
                        "row_number" => $seat['row_number'],
                        "col_number" => $seat['col_number'],
                        "is_window" => in_array($seatText, $windowSeats) ? 1 : 0,
                        "is_aisle" => is_null($seat['seat_text']) ? 1 : 0,
                        "created_by" => 1,
                        "created_at" => now(),
                        "updated_at" => now(),
                    ];
                }, $seats);

                DB::table('mst_seats')->insert($seats);

                DB::commit();

                return back()->with([
                    'level' => 'success',
                    'message' => 'Seat Layout Saved Successfully'
                ]);
            } catch (\Exception $e) {

                DB::rollBack();

                return back()->with([
                    'level' => 'danger',
                    'message' => $e->getMessage()
                ]);
            }
        }

        return view('Master.addSeatLayout', compact('data'));
    }

    public function checkLayoutName(Request $request)
    {
        $exists = SeatLayoutName::where('layout_name', $request->layout_name)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
