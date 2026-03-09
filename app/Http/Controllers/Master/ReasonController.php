<?php

namespace App\Http\Controllers\Master;

use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Reason;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ReasonController extends Controller
{

    public function reason()
    {
        return view('master.reason');
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

                $redirectPage = "admin/reason/edit/" . $encId;
                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = Reason::select(
                    'id',
                    'reason',
                )->where('id', $id)->first();

                log::info($row);

                if (!$row) {
                    return redirect('reason');
                }

                $data['row'] = $row;

            } else {
                $redirectPage = "admin/reason";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'reason'    => 'required|max:500',
                ], [
                    'roleType.required' => 'Role Type cannot be left blank.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $reason    = trim(Purifier::clean(request('reason')));

                $obj = ($id > 0) ? Reason::find($id) : new Reason();

                $obj->reason           = htmlEncode($reason);
                $obj->active_status   = 1;

                if ($id > 0) {
                    $obj->updated_by = 1;
                } else {
                    $obj->created_by = 1;
                }

                $obj->save();

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Reason ' . ($id > 0 ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error in ReasonController@add", [
                'method' => $method,
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addReason', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function dataTableView()
    {
        $recordsTotal    = 0;
        $recordsFiltered = 0;
        $data            = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '')? (int) request('selStatus'): '';

            $dataQuery = DB::table('mst_reason as r')
                ->select(
                    'r.id as reason_id',
                    'r.reason',
                    'r.active_status',
                    'r.created_at',
                    'r.updated_at',
                    'r.created_by',
                    'r.updated_by',
                    DB::raw('(SELECT name FROM users WHERE id = r.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = r.updated_by LIMIT 1) as updated_by_name')
                );

                
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('r.code', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== '' && $selStatus !== null) {
                $dataQuery->where('r.active_status', (int) $selStatus);
            }

            
            $recordsTotal = $dataQuery->count('r.id');
            $recordsFiltered = $recordsTotal;

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            
            if (!empty(request('order'))) {

            
                $columns = [
                    2 => 'r.reason',
                    4 => 'r.created_at',
                    5 => 'r.active_status'
                ];

                $order      = request('order');
                $orderCol   = $columns[$order[0]['column']] ?? 'r.reason`';
                $orderDir   = $order[0]['dir'] ?? 'asc';

            } else {
                $orderCol = 'r.id';
                $orderDir = 'desc';
            }

            $dataQuery->orderBy($orderCol, $orderDir);

            
            if ($length === -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery
                    ->offset($start)
                    ->limit($length)
                    ->get();
            }

            
            foreach ($arrRes as $row) {
                $row->created_date = date('d-M-Y H:i:s', strtotime($row->created_at));
                $row->updated_date = $row->updated_at
                    ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                    : null;

                $row->is_active = ($row->active_status == 1) ? 'Active' : 'Inactive';
                $row->enc_reason_id = Crypt::encryptString($row->reason_id);
            }

            $data = $arrRes;

        } catch (\Throwable $t) {

            Log::error("Exception in RolesController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace'         => $t->getTraceAsString()
            ]);

            $recordsTotal    = 0;
            $recordsFiltered = 0;
            $data             = [];
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
}
