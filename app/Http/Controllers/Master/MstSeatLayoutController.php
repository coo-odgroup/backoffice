<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MstSeatLayoutController extends Controller
{
    public function mstSeatLayout()
    {
        return view('master.mstSeatLayout');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '')
                ? (int)request('selStatus') : '';

            $query = DB::table('mst_seat_layout as m')
                ->select(
                    'm.id as seat_layoutId',
                    'm.seat_layout',
                    'm.created_at',
                    'm.updated_at',
                    'm.active_status',
                    DB::raw('(SELECT name FROM users WHERE id = m.created_by) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = m.updated_by) as updated_by_name')
                );

            // Search
            if (!empty($txtSearch)) {
                $query->where('m.seat_layout', 'like', "%{$txtSearch}%");
            }

            // Status
            if ($selStatus !== '') {
                $query->where('m.active_status', $selStatus);
            }

            // Clone query for count
            $countQuery = clone $query;
            $recordsTotal = $countQuery->count();

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            // Sorting
            if (!empty(request('order'))) {

                $columns = [
                    2 => 'm.seat_layout',
                    3 => 'm.created_at',
                    4 => 'm.active_status'
                ];

                $order = request('order')[0];
                $orderColumn = $columns[$order['column']] ?? 'm.seat_layout';
                $orderDir = $order['dir'];

                $query->orderBy($orderColumn, $orderDir);

            } else {
                $query->orderBy('m.seat_layout', 'asc');
            }

            if ($length != -1) {
                $query->offset($start)->limit($length);
            }

            $arrRes = $query->get();

            foreach ($arrRes as $val) {

                $val->created_date = $val->created_at
                    ? date('d-M-Y H:i:s', strtotime($val->created_at))
                    : '--';

                $val->updated_date = $val->updated_at
                    ? date('d-M-Y H:i:s', strtotime($val->updated_at))
                    : '--';

                $val->is_active = ($val->active_status == 1) ? 'Active' : 'Inactive';

                $val->enc_seat_layoutId = Crypt::encryptString($val->seat_layoutId);
            }

            $recordsFiltered = $recordsTotal;
            $data = $arrRes;

        } catch (\Throwable $t) {

            Log::error("MstSeatLayoutController@dataTableView Error", [
                'message' => $t->getMessage()
            ]);
        }

        return response()->json([
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    public function add($encId = null)
    {
        $data = [];
        $data['strPage']   = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset']  = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = route('mstSeatLayout.edit', $encId);

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = DB::table('mst_seat_layout')
                    ->select('id', 'seat_layout')
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect()->route('mstSeatLayout.index');
                }

                $data['row'] = $dataResQry;
            } else {

                $id = 0;
                $redirectPage = route('mstSeatLayout.index');
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'mstSeatLayout' => 'required',
                ], [
                    'mstSeatLayout.required' => 'Seat Layout cannot be blank.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();
                $mstSeatLayout = htmlEncode(request('mstSeatLayout'));

                $duplicate = DB::table('mst_seat_layout')
                    ->where('seat_layout', $mstSeatLayout);

                if ($id != 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {

                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Axel Type  already exists'
                    ])->withInput();
                }

                if ($id != 0) {

                    DB::table('mst_seat_layout')
                        ->where('id', $id)
                        ->update([
                            'seat_layout' => $mstSeatLayout,
                            'updated_by' => auth()->id(),
                            'updated_at' => now()
                        ]);
                } else {

                    DB::table('mst_seat_layout')->insert([
                        'seat_layout' => $mstSeatLayout,
                        'created_by'    => auth()->id(),
                        'active_status' => 1,
                        'created_at'    => now()
                    ]);
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash('message', 'Axle Type ' . ($id ? 'updated' : 'created') . ' successfully.');

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'AxleTypeController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addMstSeatLayout', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
