<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Districts;
use App\Models\Master\States;
use App\Models\Master\AuditLog;
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

    public function getLogs($table, $id)
    {
        $logs = AuditLog::where('table_name', $table)
                        ->where('record_id', $id)
                        ->orderByDesc('created_at','DESC')
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
                'created_by' => $log->performed_by,
                'created_at' => $log->created_at,
                'changes' => $changes
            ];
        });

        return response()->json($formattedLogs);
    }

}
