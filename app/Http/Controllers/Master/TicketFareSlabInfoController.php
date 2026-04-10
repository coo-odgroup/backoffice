<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\TicketFareSlabInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;

class TicketFareSlabInfoController extends Controller
{
    public function index()
    {
        return view('Master.ticketFareSlabInfo');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {
            $txtSearch = htmlEncode(request('txtSearch'));
            $status = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $operator = (request('operator') !== null && request('operator') !== '') ? (int)request('operator') : '';

            $query = DB::table('mst_ticket_fare_slab_info as t')
                ->leftJoin('mst_ticket_fare_slab as s', 's.id', '=', 't.slab_id')
                ->leftJoin('users as u', function ($join) {
                    $join->on('u.id', '=', 't.bus_operator_id')
                        ->where('u.user_role', 9);
                })
                ->select(
                    't.id',
                    't.slab_id',
                    't.bus_operator_id',
                    's.slab_name',
                    'u.organization_name as operator_name',
                    't.starting_fare',
                    't.upto_fare',
                    't.commision',
                    't.from_date',
                    't.to_date',
                    't.active_status',
                    't.created_at',
                    't.updated_at',
                    DB::raw('(SELECT name FROM users WHERE id = t.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = t.updated_by LIMIT 1) as updated_by_name')
                );

            if (!empty($txtSearch)) {
                $query->where(function ($q) use ($txtSearch) {
                    $q->where('s.slab_name', 'like', "%{$txtSearch}%")
                        ->orWhere('u.organization_name', 'like', "%{$txtSearch}%")
                        ->orWhere('t.starting_fare', 'like', "%{$txtSearch}%")
                        ->orWhere('t.upto_fare', 'like', "%{$txtSearch}%");
                });
            }

            if ($status !== null && $status !== '') {
                $query->where('t.active_status', $status);
            }

            if ($operator !== null && $operator !== '') {
                $query->where('t.bus_operator_id', $operator);
            }

            $rows = $query->orderBy('t.id', 'desc')->get();


            $grouped = [];

            foreach ($rows as $row) {

                $slabId = $row->slab_id;

                if (!isset($grouped[$slabId])) {
                    $grouped[$slabId] = [
                        'id' => $row->id,
                        'slab_id' => $row->slab_id,
                        'slab_name' => $row->slab_name,
                        'operators' => [],
                        'slab_info' => [],
                        'created_date' => date('d-M-Y H:i:s', strtotime($row->created_at)),
                        'updated_date' => $row->updated_at
                            ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                            : null,
                        'created_by_name' => $row->created_by_name,
                        'updated_by_name' => $row->updated_by_name,
                        'is_active' => $row->active_status == 1 ? 'Active' : 'Inactive',
                        'enc_id' => Crypt::encryptString($row->slab_id),
                    ];
                }


                $operatorName = (!empty($row->bus_operator_id) && !empty($row->operator_name))
                    ? $row->operator_name
                    : '--';

                if (!in_array($operatorName, $grouped[$slabId]['operators'])) {
                    $grouped[$slabId]['operators'][] = $operatorName;
                }

                $key = md5(
                    $row->starting_fare . '|' .
                        $row->upto_fare . '|' .
                        $row->commision . '|' .
                        date('Y-m-d', strtotime($row->from_date)) . '|' .
                        date('Y-m-d', strtotime($row->to_date))
                );

                if (!isset($grouped[$slabId]['slab_info'][$key])) {

                    if (!empty($operator)) {

                        if ($row->bus_operator_id == $operator) {
                            $grouped[$slabId]['slab_info'][$key] = [
                                'starting_fare' => $row->starting_fare,
                                'upto_fare' => $row->upto_fare,
                                'commision' => $row->commision,
                                'from_date' => $row->from_date,
                                'to_date' => $row->to_date,
                            ];
                        }
                    } else {

                        $grouped[$slabId]['slab_info'][$key] = [
                            'starting_fare' => $row->starting_fare,
                            'upto_fare' => $row->upto_fare,
                            'commision' => $row->commision,
                            'from_date' => $row->from_date,
                            'to_date' => $row->to_date,
                        ];
                    }
                }
            }

            foreach ($grouped as $key => &$slab) {

                $slab['slab_info'] = array_values($slab['slab_info']);

                // remove empty slabs after filter
                if (empty($slab['slab_info'])) {
                    unset($grouped[$key]);
                }
            }

            $data = array_values($grouped);

            $recordsTotal = count($data);
            $recordsFiltered = $recordsTotal;
        } catch (\Throwable $t) {

            Log::error("TicketFareSlabInfoController Error", [
                'message' => $t->getMessage()
            ]);

            return response()->json([
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
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
        $data['strPage']   = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset']  = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = "admin/ticketfareslab-info/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';


                $row = DB::table('mst_ticket_fare_slab_info as t')
                    ->leftJoin('users as u', 'u.id', '=', 't.bus_operator_id')
                    ->where('t.slab_id', $id)
                    ->select(
                        't.*',
                        'u.organization_name as operator_name'
                    )
                    ->get();

                if ($row->isEmpty()) {
                    return redirect('ticketfareslab-info');
                }

                $operators = [];
                $slabInfo = [];

                foreach ($row as $r) {

                    if (!empty($r->bus_operator_id) && !empty($r->operator_name)) {

                        $operators[$r->bus_operator_id] = [
                            'id' => $r->bus_operator_id,
                            'name' => $r->operator_name
                        ];
                    }

                    $key = md5(
                        $r->starting_fare . '|' .
                            $r->upto_fare . '|' .
                            $r->commision . '|' .
                            date('Y-m-d', strtotime($r->from_date)) . '|' .
                            date('Y-m-d', strtotime($r->to_date))
                    );

                    if (!isset($slabInfo[$key])) {
                        $slabInfo[$key] = [
                            'starting_fare' => (string)$r->starting_fare,
                            'upto_fare' => (string)$r->upto_fare,
                            'commision' => (string)$r->commision,
                            'from_date' => (!empty($r->from_date) && $r->from_date != '1970-01-01')
                                ? $r->from_date
                                : null,

                            'to_date'   => (!empty($r->to_date) && $r->to_date != '1970-01-01')
                                ? $r->to_date
                                : null,
                        ];
                    }
                }


                $slabInfo = array_values($slabInfo);

                $data['row'] = [
                    'slab_id' => $id,
                    'operators' => array_values($operators),
                    'slabInfo' => $slabInfo
                ];;

                if ($row->isEmpty()) {
                    return redirect('ticketfareslab-info');
                }
            } else {
                $id = 0;
                $redirectPage = "admin/ticketfareslab-info";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());


                $validator = Validator::make(request()->all(), [
                    'slab_id' => 'bail|required|integer',
                    'bus_operator_id' => 'nullable',

                    'starting_fare.*' => 'required|numeric|min:0',
                    'upto_fare.*'     => 'required|numeric',
                    'commision.*'     => 'required|numeric',

                    'from_date.*' => 'nullable|date',
                    'to_date.*'   => 'nullable|date',
                ], [
                    'slab_id.required' => 'Please select slab',
                    'bus_operator_id.required' => 'Please select at least one operator',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $slab_id = (int) request('slab_id');
                $bus_operator_id = request('bus_operator_id');

                $operators = [];

                $operators = [];

                if (!empty($bus_operator_id)) {

                    $operators = array_filter(
                        array_map('intval', explode(',', $bus_operator_id)),
                        function ($val) {
                            return $val > 0;
                        }
                    );

                    if (empty($operators)) {
                        $operators = [null];
                    }
                } else {
                    $operators = [null];
                }


                $starting_fare = request('starting_fare');
                $upto_fare     = request('upto_fare');
                $commision     = request('commision');
                $from_date     = request('from_date');
                $to_date       = request('to_date');

                foreach ($starting_fare as $i => $start) {

                    if ($upto_fare[$i] < $start) {
                        DB::rollBack();
                        return back()->with([
                            'level' => 'danger',
                            'message' => 'To Fare must be greater than or equal to From Fare'
                        ])->withInput();
                    }

                    if (!empty($from_date[$i]) && !empty($to_date[$i]) && $to_date[$i] < $from_date[$i]) {
                        DB::rollBack();
                        return back()->with([
                            'level' => 'danger',
                            'message' => 'To Date must be after From Date'
                        ])->withInput();
                    }
                }

                $oldData = [];

                if ($id > 0) {
                    $oldData = DB::table('mst_ticket_fare_slab_info')
                        ->where('slab_id', $slab_id)
                        ->get()
                        ->map(function ($row) {
                            return [
                                'slab_id'         => $row->slab_id,
                                'bus_operator_id' => $row->bus_operator_id,
                                'starting_fare'   => $row->starting_fare,
                                'upto_fare'       => $row->upto_fare,
                                'commision'       => $row->commision,
                                'from_date'       => $row->from_date,
                                'to_date'         => $row->to_date,
                            ];
                        })
                        ->toArray();
                }

                $newData = [];

                foreach ($operators as $operator) {

                    foreach ($starting_fare as $key => $val) {

                        $newData[] = [
                            'slab_id'         => $slab_id,
                            'bus_operator_id' => (int) $operator,
                            'starting_fare'   => $starting_fare[$key],
                            'upto_fare'       => $upto_fare[$key],
                            'commision'       => $commision[$key],
                            'from_date'       => !empty($from_date[$key]) ? $from_date[$key] : null,
                            'to_date'         => !empty($to_date[$key]) ? $to_date[$key] : null,
                        ];
                    }
                }

                $oldChanged = [];
                $newChanged = [];

                if ($id > 0) {

                    if ($oldData != $newData) {

                        $oldChanged = $oldData;
                        $newChanged = $newData;

                        app(CommonController::class)->auditLog(
                            'mst_ticket_fare_slab_info',
                            $slab_id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }
                } else {

                    app(CommonController::class)->auditLog(
                        'mst_ticket_fare_slab_info',
                        null,
                        'INSERT',
                        [],
                        $newData
                    );
                }

                if ($id > 0) {
                    DB::table('mst_ticket_fare_slab_info')
                        ->where('slab_id', $slab_id)
                        ->delete();
                }

                $insertData = [];

                foreach ($newData as $row) {

                    $insertData[] = [
                        ...$row,
                        'active_status' => 1,
                        'created_at'    => now(),
                        'updated_at'    => ($id > 0) ? now() : null
                    ];
                }

                DB::table('mst_ticket_fare_slab_info')->insert($insertData);

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Ticket Fare Slab Info ' . ($id > 0 ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error in TicketFareSlabInfoController@add", [
                'method' => $method,
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }


        return view('Master.addTicketFareSlabInfo', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function getOperatorSlabData(Request $request)
    {
        try {

            $operator_id = $request->operator_id;
            $slab_id     = $request->slab_id;

            $data = DB::table('mst_ticket_fare_slab_info as t')
                ->leftJoin('mst_ticket_fare_slab as s', 's.id', '=', 't.slab_id')
                ->where('t.bus_operator_id', $operator_id)
                ->where('t.slab_id', $slab_id)
                ->select(
                    's.slab_name',
                    't.starting_fare',
                    't.upto_fare',
                    't.commision',
                    't.from_date',
                    't.to_date'
                )
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'data'   => [],
                'message' => $e->getMessage()
            ]);
        }
    }
}
