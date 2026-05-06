<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\blogs\Blog;
use App\Models\blogs\BlogCategory;
use App\Models\blogs\BlogTags;
use App\Models\Master\AmenityCategory;
use Illuminate\Http\Request;
use App\Models\Master\Districts;
use App\Models\Master\States;
use App\Models\Master\ApiApps;
use App\Models\Master\AuditLog;
use App\Models\Master\CancellationslabInfo;
use App\Models\Master\Modules;
use App\Models\Master\FaqCategory;
use App\Models\Master\Roles;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CommonController extends Controller
{

    public function auditLog($table, $recordId, $action, $oldData = [], $newData = [], $deviceType = null)
    {
        try {

            //  ENUM validation
            $allowedActions = ['INSERT', 'UPDATE', 'SOFT_DELETE', 'STATUS_CHANGE'];

            if (!in_array($action, $allowedActions)) {
                throw new \Exception("Invalid audit action: " . $action);
            }

            $userId = auth()->id() ?? 1;

            //  IP + User Agent
            $userIp = $_SERVER['HTTP_X_FORWARDED_FOR']
                ?? $_SERVER['REMOTE_ADDR']
                ?? 'Unknown';

            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

            //  Auto detect device
            if (!$deviceType) {
                $deviceType = 'Desktop';
                if (preg_match('/mobile/i', $userAgent)) {
                    $deviceType = 'Mobile';
                } elseif (preg_match('/tablet|ipad/i', $userAgent)) {
                    $deviceType = 'Tablet';
                }
            }

            //  Ensure array
            $oldData = is_object($oldData) ? (array)$oldData : $oldData;
            $newData = is_object($newData) ? (array)$newData : $newData;

            DB::connection('mysql_log')->table('audit_logs_master')->insert([
                'table_name'  => $table,
                'record_id'   => $recordId,
                'action'      => $action,
                'old_data'    => !empty($oldData) ? json_encode($oldData) : null,
                'new_data'    => !empty($newData) ? json_encode($newData) : null,
                'created_by'  => $userId,
                'created_at'  => now(),
                'user_ip'     => $userIp,
                'user_agent'  => $userAgent,
                'device_type' => $deviceType,
            ]);
        } catch (\Exception $e) {
            Log::error('Audit Log Failed', [
                'error' => $e->getMessage()
            ]);
        }
    }




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

    public function getDistrictList(Request $request)
    {

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
            'BusType' => \App\Models\Master\BusType::class,
            'AmenityCategory' => \App\Models\Master\AmenityCategory::class,
            'Amenity' => \App\Models\Master\Amenity::class,
            'Roles' => \App\Models\Master\Roles::class,
            'Modules' => \App\Models\Master\Modules::class,
            'FaqCategory' => \App\Models\Master\FaqCategory::class,
            'Faq' => \App\Models\Master\Faq::class,
            'SeatType' => \App\Models\Master\SeatType::class,
            'ApiApps' => \App\Models\Master\ApiApps::class,
            'ApiKeys' => \App\Models\Master\ApiKeys::class,
            'CityApis' => \App\Models\Master\CityApis::class,
            'BlogCategory' => \App\Models\blogs\BlogCategory::class,
            'Blog' => \App\Models\blogs\Blog::class,
            'Vendor' => \App\Models\Ad\Vendor::class,
            'AdPlacement' => \App\Models\Ad\AdPlacement::class,
            'PricingPlan' => \App\Models\Ad\PricingPlan::class,
            'AdCampaign' => \App\Models\Ad\AdCampaign::class,
            'Ads' => \App\Models\Ad\Ads::class,
            'Reason' => \App\Models\Master\Reason::class,
            'FestiveDays' => \App\Models\Master\FestiveDays::class,
            'Brand' => \App\Models\Master\Brand::class,
            'BusModel' => \App\Models\Master\BusModel::class,
            'AxleType' => \App\Models\Master\AxleType::class,
            'MstSeatLayout' => \App\Models\Master\MstSeatLayout::class,
            'BusService' => \App\Models\Master\BusService::class,
            'ReviewCategory' => \App\Models\Master\ReviewCategory::class,
            'BlogTags' => \App\Models\blogs\BlogTags::class,
            'BlogTagMap' => \App\Models\blogs\BlogTagMap::class,
            'Cancellationslab' => \App\Models\Master\Cancellationslab::class,
            'TicketFareSlab' => \App\Models\Master\TicketFareSlab::class,
            'TicketFareSlabInfo' => \App\Models\Master\TicketFareSlabInfo::class,
            'AnnextureType' => \App\Models\Master\AnnextureType::class,
            'CancellationslabInfo' => \App\Models\Master\CancellationslabInfo::class,
            'Annexture' => \App\Models\Master\Annexture::class,
            'BusSchedule' => \App\Models\Bus\BusSchedule::class, 
            'BusCancel' => \App\Models\Bus\BusCancel::class,
            'NotificationTemplate' => \App\Models\Master\NotificationTemplate::class,
            'CampaignMaster' => \App\Models\Campaign\CampaignMaster::class,
        ];

        if (!isset($allowedModels[$modelName])) {
            return response()->json(['message' => 'Invalid model'], 400);
        }

        $model = $allowedModels[$modelName];
        $common = new \App\Http\Controllers\CommonController();
        $userId = auth()->id() ?? 1;

        try {

            DB::beginTransaction();

            $table = (new $model)->getTable();

            if ($action == 'A') {

                foreach ($ids as $id) {

                    $common->auditLog(
                        $table,
                        $id,
                        'STATUS_CHANGE',
                        ['active_status' => 0],
                        ['active_status' => 1]
                    );
                }

                $model::whereIn('id', $ids)->update([
                    'active_status' => 1,
                    'updated_at'    => now(),
                    'updated_by'    => $userId
                ]);
            }

            // ================= DEACTIVATE =================
            elseif ($action == 'UN') {

                foreach ($ids as $id) {

                    $common->auditLog(
                        $table,
                        $id,
                        'STATUS_CHANGE',
                        ['active_status' => 1], // assumed old
                        ['active_status' => 0]  // new
                    );
                }

                $model::whereIn('id', $ids)->update([
                    'active_status' => 0,
                    'updated_at'    => now(),
                    'updated_by'    => $userId
                ]);
            }

            // ================= DELETE =================
            elseif ($action == 'D') {

                foreach ($ids as $id) {

                    $common->auditLog(
                        $table,
                        $id,
                        'SOFT_DELETE',
                        ['deleted_at' => null], // assumed old
                        ['deleted_at' => now()]
                    );
                }

                $model::whereIn('id', $ids)->update([
                    'deleted_at' => now(),
                    'deleted_by' => $userId
                ]);
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Invalid action'], 400);
            }

            DB::commit();

            return response()->json([
                'message' => 'Action completed successfully'
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Bulk Action Error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Something went wrong'
            ], 500);
        }
    }


    public function getCityList(Request $request)
    {
        try {

            $cities = DB::table('mst_cities')
                ->select('id', 'city_name', 'alias')
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

    public function updateSequence(Request $request)
    {
        $request->validate([
            'table'  => 'required|string',
            'column' => 'required|string',
            'value'  => 'required',
            'id' => 'required|string'
        ]);

        $id = Crypt::decryptString($request->id);

        DB::table($request->table)
            ->where('id', $id)
            ->update([
                $request->column => $request->value
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Updated successfully'
        ]);
    }

    public function getAmenityCategoryList(Request $request)
    {
        $data = AmenityCategory::where('active_status', 1)
            ->orderBy('category_name')
            ->get(['id', 'category_name']);

        return response()->json([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function getApiAppsList(Request $request)
    {
        $data = ApiApps::where('active_status', 1)
            ->orderBy('app_name')
            ->get(['id', 'app_name']);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }


    public function getParentModuleList(Request $request)
    {
        $modules = Modules::where('parent_id', 0)
            ->where('active_status', 1)
            ->orderBy('sequence_no')
            ->get(['id', 'code']);

        return response()->json([
            'status' => true,
            'data'   => $modules
        ]);
    }


    public function getFaqCategoryList()
    {
        $data = FaqCategory::select('id', 'category_name')
            ->where('active_status', 1)
            ->orderBy('sequence_no')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function getRoleList(Request $request)
    {
        $data = Roles::where('active_status', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function getBlogCategoryList(Request $request)
    {
        $data = BlogCategory::where('active_status', 1)
            ->orderBy('category_name')
            ->get(['id', 'category_name']);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function removeImage(Request $request)
    {
        $table = $request->table;
        $id = $request->id;
        $column = $request->column;
        $path = $request->path;

        $data = DB::table($table)->where('id', $id)->first();

        if ($data && $data->$column) {

            $filePath = $path . '/' . $data->$column;

            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            DB::table($table)
                ->where('id', $id)
                ->update([$column => null]);

            return response()->json([
                'status' => true,
                'message' => 'Image removed successfully'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Image not found'
        ]);
    }

    public function getBlogList(Request $request)
    {
        $data = Blog::where('active_status', 1)
            ->orderBy('title')
            ->get(['id', 'title']);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function getPlacementList(Request $request)
    {
        try {

            $placements = DB::connection('mysql_dev')
                ->table('ad_placements')
                ->select('id', 'name')
                ->where('active_status', 1)
                ->orderBy('name', 'ASC')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $placements
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'data'   => []
            ], 500);
        }
    }

    public function getVendorList(Request $request)
    {
        try {

            $vendors = DB::connection('mysql_dev')
                ->table('vendors')
                ->select('id', 'company_name')
                ->where('active_status', 1)
                ->orderBy('company_name', 'ASC')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $vendors
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'data'   => []
            ], 500);
        }
    }

    public function getPricingPlanList(Request $request)
    {
        try {

            $plans = DB::connection('mysql_dev')
                ->table('ad_pricing_plans')
                ->select('id', 'plan_name')
                ->where('active_status', 1)
                ->orderBy('plan_name', 'ASC')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $plans
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'data'   => []
            ], 500);
        }
    }

    public function getCampaignList(Request $request)
    {
        try {

            $campaigns = DB::table('odbusdev.ad_campaigns')
                ->select('id', 'title')
                ->where('active_status', 1)
                ->orderBy('title', 'ASC')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $campaigns
            ]);
        } catch (\Throwable $t) {

            Log::error("Error in getCampaignList", [
                'error' => $t->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'data' => []
            ]);
        }
    }

    public function getBlogTagsList(Request $request)
    {
        $data = BlogTags::orderBy('tag_name')->get(['id', 'tag_name']);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function getCountryList()
    {
        try {

            $countries = DB::table('mst_countries')
                ->select('id', 'name')
                ->where('active_status', 1)
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $countries
            ]);
        } catch (\Throwable $t) {

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }
    }


    public function getBrandList()
    {
        try {

            $brands = DB::table('mst_bus_brand')
                ->select('id', 'brand_name')
                ->where('active_status', 1)
                ->orderBy('brand_name', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $brands
            ]);
        } catch (\Throwable $t) {

            Log::error("Error in CommonController@getBrandList", [
                'error' => $t->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }
    }

    public function getAnnexureTypeList(Request $request)
    {
        try {

            $types = DB::table('mst_annexture_type')
                ->select('id', 'annexture_type')
                ->where('active_status', 1)
                ->orderBy('annexture_type', 'ASC')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $types
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'error'  => $e->getMessage(),
                'data'   => []
            ], 500);
        }
    }


    public function getCancellationslabList()
    {
        try {

            $data = DB::table('mst_cancellationslab')
                ->select('id', 'slab_name')
                ->where('active_status', 1)
                ->orderBy('slab_name', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Throwable $t) {

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }
    }

    public function getBusModelsList(Request $request)
    {
        try {

            $query = DB::table('mst_bus_models')
                ->select('id', 'model_name')
                ->where('active_status', 1);

            // ✅ Filter by brand_id (MAIN CHANGE)
            if (!empty($request->brand_id)) {
                $query->where('brand_id', $request->brand_id);
            }

            $data = $query->orderBy('model_name', 'asc')->get();

            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Throwable $t) {

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }
    }

    public function getAxleTypeList()
    {
        try {

            $data = DB::table('mst_axle_type')
                ->select('id', 'axle_type')
                ->where('active_status', 1)
                ->orderBy('axle_type', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Throwable $t) {

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }
    }

    public function getBusServicesList()
    {
        try {

            $data = DB::table('mst_bus_service')
                ->select('id', 'bus_service_name')
                ->where('active_status', 1)
                ->orderBy('bus_service_name', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Throwable $t) {

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }
    }

    public function getSeatTypeList()
    {
        try {

            $data = DB::table('mst_seat_type')
                ->select('id', 'seat_type')
                ->where('active_status', 1)
                ->orderBy('seat_type', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Throwable $t) {

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }
    }

    public function getSeatLayoutList()
    {
        try {

            $data = DB::table('mst_seat_layout')
                ->select('id', 'seat_layout')
                ->where('active_status', 1)
                ->orderBy('seat_layout', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Throwable $t) {

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }
    }

    // public function getAnnextureList(Request $request)
    // {
    //     try {

    //         $annexture_type = $request->annexture_type;

    //         // Step 1: Get annexture_type_id
    //         $type = DB::table('mst_annexture_type')
    //             ->select('id')
    //             ->where('annexture_type', $annexture_type)
    //             ->where('active_status', 1)
    //             ->first();

    //         if (!$type) {
    //             return response()->json([
    //                 'status' => false,
    //                 'data'   => []
    //             ]);
    //         }

    //         // Step 2: Get annexture list using type_id
    //         $data = DB::table('mst_annexture')
    //             ->select('id', 'annexture_name', 'annexture_value')
    //             ->where('annexture_type_id', $type->id)
    //             ->where('active_status', 1)
    //             ->orderBy('annexture_value', 'asc')
    //             ->get();

    //         return response()->json([
    //             'status' => true,
    //             'data'   => $data
    //         ]);
    //     } catch (\Throwable $t) {

    //         return response()->json([
    //             'status' => false,
    //             'data'   => []
    //         ]);
    //     }
    // }

    public function getAnnextureList(Request $request)
{
    try {

        $annextureTypes = $request->annexture_types ?? [];

        if (empty($annextureTypes)) {

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }

        // GET TYPE IDS
        $types = DB::table('mst_annexture_type')
            ->select('id', 'annexture_type')
            ->whereIn('annexture_type', $annextureTypes)
            ->where('active_status', 1)
            ->get();

        if ($types->isEmpty()) {

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }

        $typeMap = [];

        foreach ($types as $type) {
            $typeMap[$type->id] = $type->annexture_type;
        }

        // GET ALL ANNEXTURES
        $annextures = DB::table('mst_annexture')
            ->select(
                'id',
                'annexture_type_id',
                'annexture_name',
                'annexture_value'
            )
            ->whereIn('annexture_type_id', array_keys($typeMap))
            ->where('active_status', 1)
            ->orderBy('annexture_value', 'asc')
            ->get();

        // GROUP DATA
        $groupedData = [];

        foreach ($annextures as $item) {

            $typeKey = $typeMap[$item->annexture_type_id];

            $groupedData[$typeKey][] = [
                'id' => $item->id,
                'annexture_name' => $item->annexture_name,
                'annexture_value' => $item->annexture_value
            ];
        }

        return response()->json([
            'status' => true,
            'data'   => $groupedData
        ]);

    } catch (\Throwable $t) {

        return response()->json([
            'status' => false,
            'data'   => []
        ]);
    }
}

    public function getCampaignMasterList()
    {
        try {

            $data = DB::table('campaign_master')
                ->select('id', 'campaign_name')
                ->where('active_status', 1)
                ->orderBy('campaign_name', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Throwable $t) {

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }
    }

    public function getAmenities()
    {
        try {

            $categories = DB::table('mst_amenity_categories as c')
                ->leftJoin('mst_amenities as a', 'a.category_id', '=', 'c.id')
                ->select(
                    'c.id as category_id',
                    'c.category_name as category_name',
                    'a.id as amenity_id',
                    'a.amenity_name as amenity_name'
                )
                ->get()
                ->groupBy('category_id');

            $data = [];

            foreach ($categories as $group) {

                $first = $group->first();

                $data[] = [
                    'category_id'   => $first->category_id,
                    'category_name' => $first->category_name,
                    'amenities'     => $group->filter(fn($item) => $item->amenity_id)
                        ->map(function ($item) {
                            return [
                                'id'   => $item->amenity_id,
                                'name' => $item->amenity_name
                            ];
                        })->values()
                ];
            }

            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Exception $e) {

            Log::error('Amenity error', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to load amenities'
            ], 500);
        }
    }

    public function getBusOperatorList()
    {
        try {

            $data = DB::table('users')
                ->select('id', 'name', 'unique_id', 'organization_name')
                ->where('active_status', 1)
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Throwable $t) {

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }
    }

    public function searchAmenities(Request $request)
    {
        $search = $request->search;

        $query = AmenityCategory::query()
            ->where('active_status', 1);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('category_name', 'LIKE', "%$search%")
                    ->orWhereHas('amenities', function ($q2) use ($search) {
                        $q2->where('active_status', 1)
                            ->where('amenity_name', 'LIKE', "%$search%");
                    });
            });
        }

        $categories = $query->get();

        $categories->map(function ($category) use ($search) {

            if (!$search) {
                $category->amenities = $category->amenities()
                    ->where('active_status', 1)
                    ->get();
            } elseif (stripos($category->category_name, $search) !== false) {
                $category->amenities = $category->amenities()
                    ->where('active_status', 1)
                    ->get();
            } else {
                $category->amenities = $category->amenities()
                    ->where('active_status', 1)
                    ->where('amenity_name', 'LIKE', "%$search%")
                    ->get();
            }

            return $category;
        });

        return response()->json($categories);
    }

    public function getSlabDetails(Request $request)
    {
        $slabId = $request->slab_id;

        if (!$slabId) {
            return response()->json([]);
        }

        $data = CancellationslabInfo::where('slab_id', $slabId)
            ->where('active_status', 1)
            ->orderBy('id', 'asc')
            ->get([
                'id',
                'duration as hours',
                'deduction as charge'
            ]);

        return response()->json($data);
    }

    public function uploadImage(Request $request)
    {
        return $request;
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $path = $file->store('uploads', 'public');

            return response()->json([
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['error' => 'Upload failed'], 400);
    }


    public function getTicketFareSlabList()
    {
        try {

            $data = DB::table('mst_ticket_fare_slab')
                ->where('active_status', 1)
                ->select('id', 'slab_name')
                ->orderBy('slab_name', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'data' => []
            ]);
        }
    }


    public function getSlabList()
    {
        try {
            $data = DB::table('mst_ticket_fare_slab')
                ->select('id', 'slab_name')
                ->where('is_active', 1)
                ->orderBy('slab_name', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getBusOperatorListRoleWise()
    {
        try {

            $data = DB::table('users')
                ->select('id', 'name', 'unique_id', 'organization_name')
                ->where('active_status', 1)
                ->where('user_role', 9)
                ->orderBy('organization_name', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Throwable $t) {

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }
    }

    public function getBusesByOperator(Request $request)
    {
        $operator_id = $request->operator_id;

        $buses = DB::table('odbusdev.bus')
            ->where('bus_operator_id', $operator_id)
            ->where('active_status', 1)
            ->select('id', 'name', 'bus_number')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $buses
        ]);
    }

    public function getUsersList(Request $request)
    {
        try {

            $user_code = $request->user_code;

            $role = DB::table('mst_roles')
                ->select('id')
                ->where('code', $user_code)
                ->where('active_status', 1)
                ->first();

            if (!$role) {
                return response()->json([
                    'status' => false,
                    'data'   => []
                ]);
            }

            $data = DB::table('users')
                ->select('id', 'name', 'organization_name', 'unique_id')
                ->where('user_role', $role->id)
                ->where('active_status', 1)
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Throwable $t) {

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }
    }

    public function getBusCancelReasons()
    {
        try {

            $data = DB::table('mst_annexture')
                ->select('id', 'annexture_name')
                ->where('annexture_type_id', 16)
                ->where('active_status', 1)
                ->orderBy('annexture_value', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }
    }
}
