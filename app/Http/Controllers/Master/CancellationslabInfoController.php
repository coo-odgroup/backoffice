<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Cancellationslab;
use App\Models\Master\CancellationslabInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CancellationslabInfoController extends Controller
{
    public function index()
    {
        return view('Master.cancellationslabInfo');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));

            $dataQuery = CancellationSlab::with('slabInfo')
                ->select(
                    'id',
                    'id as slab_id',
                    'slab_name',
                    'description',
                    'created_at',
                    'created_by',
                    'updated_at',
                    'updated_by',
                    'active_status'
                )
                ->selectRaw('(SELECT name FROM users WHERE id = mst_cancellationslab.created_by LIMIT 1) as created_by_name')
                ->selectRaw('(SELECT name FROM users WHERE id = mst_cancellationslab.updated_by LIMIT 1) as updated_by_name');

            // return $dataQuery->get();

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('slab_name', 'like', "%{$txtSearch}%")
                        ->orWhere('description', 'like', "%{$txtSearch}%");
                });
            }

            $count = $dataQuery->count('id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'slab_name', 3 => 'description', 4 => 'created_by', 5 => 'active_status'];

                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 'slab_name';
                $orderType     = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'slab_name';
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
                    $val->created_date = date('d-M-Y H:i:s', strtotime($val->created_at));
                    $val->updated_date = ($val->updated_at != null) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : null;
                    $val->is_active = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_slab_id = Crypt::encryptString($val->slab_id);
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in CancellationslabInfoController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'CancellationslabInfoController',
                'Method'     => 'dataTableView',
                'Error'      => $errorMsg
            ]);

            $recordsTotal     = 0;
            $recordsFiltered  = 0;
            $data            = [];
        }

        return response()->json([
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
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

                $redirectPage = "admin/cancellationslab-info/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = CancellationSlab::with('slabInfo')->select('*', 'id as slab_id');

                $dataResQry = $dataResQry->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("cancellationslab-info");
                }
                $data['row'] = $dataResQry;

                // return $data['row'];
            } else {
                $id = 0;
                $redirectPage = "admin/cancellationslab-info";
            }

            if (request()->isMethod('post')) {

                DB::beginTransaction();

                try {

                    $slab_id   = request('slab_id');
                    $durations = request('duration');
                    $deductions = request('deduction');

                    if ($id > 0) {
                        DB::table('mst_cancellationslab_info')
                            ->where('slab_id', $slab_id)
                            ->delete();
                    }

                    $insertData = [];

                    foreach ($durations as $key => $duration) {

                        $insertData[] = [
                            'slab_id' => $slab_id,
                            'duration' => $duration,
                            'deduction' => $deductions[$key] ?? 0,
                            'active_status' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }

                    DB::table('mst_cancellationslab_info')->insert($insertData);

                    DB::commit();

                    session()->flash('level', 'success');
                    session()->flash('message', 'Cancellation Slab Info ' . (($id != 0) ? 'updated' : 'created') . ' successfully.');

                    return redirect($redirectPage);
                } catch (\Exception $e) {

                    DB::rollBack();

                    return response()->json([
                        'status' => false,
                        'message' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'CancellationslabInfoController',
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
        return view('Master.addCancellationslabInfo', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
