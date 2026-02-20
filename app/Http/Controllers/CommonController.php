<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Districts;
use App\Models\Master\States;
use App\Models\Master\AuditLog;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class CommonController extends Controller
{
    public function getStateList(Request $request)
    {
        $states = States::where('active_status', 1)
                    ->orderBy('state_name')
                    ->get(['id', 'state_name']);


        return response()->json([
            'status' => true,
            'data'   => $states
        ]);
    }

    public function getDistrictList(Request $request) {

        $stateId = $request->state_id;
       
        $districts = Districts::where('state_id', $stateId)
                              ->where('active_status', 1)
                              ->orderBy('district_name')
                              ->get(['id', 'district_name']);

        return response()->json([
            'status' => true,
            'data'   => $districts
        ]);
    }

    public function bulkAction(Request $request)
    {
        $ids = explode(',', $request->ids);
        $action = $request->action;
        $modelName = $request->model;

        $allowedModels = [
            'Cities' => \App\Models\Master\Cities::class,
            'States' => \App\Models\Master\States::class,
            'Districts' => \App\Models\Master\Districts::class,
            'BoardingDropping' => \App\Models\Master\BoardingDropping::class,
        ];

        if (!isset($allowedModels[$modelName])) {
            return response()->json([
                'message' => 'Invalid model'
            ], 400);
        }

        $model = $allowedModels[$modelName];

        switch ($action) {

            case 'D':
                  $model::whereIn('id', $ids)->update([
                                  'deleted_at' => now(),
                                  'deleted_by' => 1,   // Need to udpate with auth user id
                ]);
                break;

            case 'A':
                $model::whereIn('id', $ids)->update([
                            'active_status' => 1,
                            'updated_at' => now(),
                            'updated_by' => 1
                    ]);  // Need to udpate with auth user id]);
                break;

            case 'UN':
                $model::whereIn('id', $ids)->update([
                                'active_status' => 0,
                                'updated_at' => now(),
                                'updated_by' => 1,   // Need to udpate with auth user id]);
                ]);
                break;

            default:
                return response()->json([
                    'message' => 'Invalid action'
                ], 400);
        }

        return response()->json([
            'message' => 'Action completed successfully'
        ]);
    }

    public function getCityList(Request $request)
    {
        try {

            $cities = DB::table('mst_cities')
                ->select('id', 'city_name')
                ->where('active_status', 1)
                ->orderBy('city_name', 'ASC')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $cities
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'data'   => []
            ], 500);
        }
    }

    public function getLogs(Request $request)
    {
        $table = $request->table;
        $id = $request->id;

        $id = Crypt::decryptString($id);

       $logs = AuditLog::on('mysql_log')
                        ->select(
                            'audit_logs_master.*',
                            'u.name as user_name'
                        )
                        ->leftJoin('odbusmaster.users as u', 'u.id', '=', 'audit_logs_master.created_by')
                        ->where('audit_logs_master.table_name', $table)
                        ->where('audit_logs_master.record_id', $id)
                        ->orderByDesc('audit_logs_master.created_at')
                        ->limit(5)
                        ->get();


        $formattedLogs = $logs->map(function ($log) {

            $old = json_decode($log->old_data, true) ?? [];
            $new = json_decode($log->new_data, true) ?? [];

            $changes = [];

            foreach ($new as $key => $value) {
                $oldValue = $old[$key] ?? null;

                if ($oldValue != $value) {
                    $changes[] = [
                        'field' => $key,
                        'old' => $oldValue,
                        'new' => $value
                    ];
                }
            }

            return [
                'id' => $log->id,
                'action' => $log->action,
                'created_by' => $log->user_name,
                'created_at' => $log->created_at,
                'changes' => $changes
            ];
        });

        return response()->json($formattedLogs);
    }

}
