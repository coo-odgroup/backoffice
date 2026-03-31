<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class MasterLogController extends Controller
{
    /**
     * Default admin landing page
     */
    public function index()
    {
        return view('admin.masterLog');
    }


    public function logDataTableView()
    {
        $recordsTotal    = 0;
        $recordsFiltered = 0;
        $data            = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $fromDate  = request('from_date');
            $toDate    = request('to_date');
            $action    = request('action');

            $baseQuery = DB::table('audit_logs_master as a');

            // 🔢 COUNTS (WITHOUT FILTER)
            $total   = (clone $baseQuery)->count();
            $creates = (clone $baseQuery)->where('action', 'INSERT')->count();
            $updates = (clone $baseQuery)->where('action', 'UPDATE')->count();
            $deletes = (clone $baseQuery)->whereIn('action', ['DELETE', 'SOFT_DELETE'])->count();

            $dataQuery = DB::table('audit_logs_master as a')
                ->select(
                    'a.id',
                    'a.table_name',
                    'a.record_id',
                    'a.action',
                    'a.old_data',
                    'a.new_data',
                    'a.created_by',
                    'a.created_at',
                    DB::raw('(SELECT name FROM users WHERE id = a.created_by LIMIT 1) as created_by_name')
                );

            // 🔍 SEARCH
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('a.table_name', 'like', "%{$txtSearch}%")
                    ->orWhere('a.record_id', 'like', "%{$txtSearch}%")
                    ->orWhere('a.action', 'like', "%{$txtSearch}%");
                });
            }

            // 📅 DATE FILTER
            if (!empty($fromDate)) {
                $dataQuery->whereDate('a.created_at', '>=', $fromDate);
            }

            if (!empty($toDate)) {
                $dataQuery->whereDate('a.created_at', '<=', $toDate);
            }

            // 🎯 ACTION FILTER
            if (!empty($action)) {
                $dataQuery->where('a.action', $action);
            }

            // 📊 FILTERED COUNT
            $recordsTotal    = $dataQuery->count('a.id');
            $recordsFiltered = $recordsTotal;

            // 📄 PAGINATION
            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            // 🔽 ORDERING
            if (!empty(request('order'))) {

                $columns = [
                    1 => 'a.table_name',
                    2 => 'a.record_id',
                    3 => 'a.action',
                    4 => 'created_by_name',
                    5 => 'a.created_at'
                ];

                $order    = request('order');
                $orderCol = $columns[$order[0]['column']] ?? 'a.created_at';
                $orderDir = $order[0]['dir'] ?? 'desc';

            } else {
                $orderCol = 'a.created_at';
                $orderDir = 'desc';
            }

            $dataQuery->orderBy($orderCol, $orderDir);

            // 📥 FETCH DATA
            if ($length === -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery
                    ->offset($start)
                    ->limit($length)
                    ->get();
            }

            // 🔄 FORMAT DATA
            foreach ($arrRes as $row) {

                $row->created_date = date('d-M-Y H:i:s', strtotime($row->created_at));

                // 🎨 ACTION BADGE
                $row->action_badge = match ($row->action) {
                    'INSERT' => '<span class="log-badge green">create</span>',
                    'UPDATE' => '<span class="log-badge blue">update</span>',
                    'DELETE', 'SOFT_DELETE' => '<span class="log-badge red">delete</span>',
                    'STATUS_CHANGE' => '<span class="log-badge orange">status</span>',
                    default => '<span class="log-badge">'.$row->action.'</span>',
                };

                // 🔍 CHANGE SUMMARY (OLD → NEW)
                $old = json_decode($row->old_data, true);
                $new = json_decode($row->new_data, true);

                $row->change_summary = '';

                if ($old && $new) {
                    foreach ($new as $key => $value) {
                        if (isset($old[$key]) && $old[$key] != $value) {
                            $row->change_summary .= "<b>$key</b>: {$old[$key]} → {$value}<br>";
                        }
                    }
                }

                // 🔐 ENCRYPT ID
                $row->enc_id = Crypt::encryptString($row->id);
            }

            $data = $arrRes;

        } catch (\Throwable $t) {

            Log::error("Exception in MasterLogController@logDataTableView", [
                'error_message' => $t->getMessage(),
                'trace'         => $t->getTraceAsString()
            ]);

            $recordsTotal    = 0;
            $recordsFiltered = 0;
            $data            = [];

            $total = $creates = $updates = $deletes = 0;
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,

            // 🔢 EXTRA (FOR TOP CARDS)
            'counts' => [
                'total'   => $total,
                'creates' => $creates,
                'updates' => $updates,
                'deletes' => $deletes
            ]
        ]);
    }

}
