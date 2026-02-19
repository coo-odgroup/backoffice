<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\BusType;
use App\Models\Master\Districts;
use App\Models\Master\SeatType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SeatingTypeController extends Controller
{
    public function seatingType()
    {
        return view('master.seatingtype');
    }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';

            $dataQuery = DB::table('mst_seat_type as s')
                ->leftJoin('users as u', 'u.id', '=', 's.created_by')
                ->select(
                    's.id as seat_type_id',
                    's.seat_type',
                    's.created_at',
                    's.created_by',
                    'u.name as created_by_name',
                    's.active_status'
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('s.seat_type', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('s.active_status', $selStatus);
            }

            $count = $dataQuery->count('s.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 's.seat_type', 3 => 's.created_at', 4 => 's.created_by', 5 => 's.active_status'];

                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 's.seat_type';
                $orderType     = $orderBy[0]['dir'];
            } else {
                $orderColumn = 's.seat_type';
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
                    $val->created_date  = date('d-M-Y H:i:s', strtotime($val->created_at));
                    $val->is_active     = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_seat_type_id   = Crypt::encryptString($val->seat_type_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in SeatingTypeController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'SeatingTypeController',
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

    public function add($encId = null)
    {

        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = "admin/seatingtype/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = SeatType::select('id', 'seat_type');

                $dataResQry = $dataResQry->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("seatingtype");
                }
                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/seatingtype";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'txtSeatType' => 'bail|required'
                ], [
                    'txtSeatType.required' => 'Seat Type cannot be left blank.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();

                    $txtSeatType  = htmlEncode(request('txtSeatType'));

                    $duplicate = SeatType::select('id')->where(['seat_type' => $txtSeatType]);

                    // return $txtSeatType;exit;

                    if ($id != 0) {
                        $duplicate->where('id', '!=', $id);
                    }

                    if ($duplicate->exists()) {
                        return back()->with([
                            'level'     => 'danger',
                            'message'   => 'Seat Type already exist'
                        ])->withInput();
                    } else {
                        $obj = ($id != 0) ? SeatType::find($id) : new SeatType();
                        $obj->seat_type = $txtSeatType;
                        $obj->created_by = 1;
                        $obj->active_status = 1;

                        if ($id != 0) {
                            $obj->updated_by = 1;
                        }

                        $obj->save();

                        session()->flash('level', 'success');
                        session()->flash('message', 'Seat Type ' . (($id != 0) ? 'updated' : 'created') . ' successfully.');
                    }

                    DB::commit();
                    return redirect($redirectPage);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'SeatingTypeController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            DB::rollBack();

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            return back()->with([
                'level'     => 'danger',
                'message'   => $errorMsg
            ])->withInput();
        }
        return view('Master.addSeatingtype', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
