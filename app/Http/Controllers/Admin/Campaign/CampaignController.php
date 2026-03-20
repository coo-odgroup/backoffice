<?php

namespace App\Http\Controllers\Admin\Campaign;

use App\Http\Controllers\Controller;
use App\Models\Campaign\Campaign;
use App\Models\Campaign\CampaignActiveDays;
use App\Models\Campaign\CampaignExcludedDates;
use App\Models\Campaign\CampaignRoutes;
use App\Models\Campaign\CampaignServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    public function index()
    {
        return view('Admin.Campaign.campaign');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';

            $dataQuery = DB::table('campaign as c')
                ->select(
                    'c.id as campaign_id',
                    'c.operator_id',
                    'c.campaign_master_id',
                    'c.offer_type',
                    'c.offer_value',
                    'c.min_ticket_value',
                    'c.services',
                    'c.auto_renewal',
                    'c.validity_type',
                    'c.start_date',
                    'c.end_date',
                    'c.duration_value',
                    'c.duration_unit',
                    'c.created_at',
                    'c.created_by',
                    'c.updated_at',
                    'c.updated_by',
                    'c.active_status',
                    DB::raw('(SELECT campaign_name FROM campaign_master WHERE id = c.campaign_master_id LIMIT 1) as campaign_name'),
                    DB::raw('(SELECT name FROM users WHERE id = c.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = c.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('c.min_ticket_value', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('c.active_status', $selStatus);
            }

            $count = $dataQuery->count('c.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'c.min_ticket_value', 3 => 'c.active_status'];

                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 'c.min_ticket_value';
                $orderType     = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'c.min_ticket_value';
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
                    $val->updated_date  = ($val->updated_at != null) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : null;
                    $val->is_active     = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_campaign_id   = Crypt::encryptString($val->campaign_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in CampaignController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'CampaignController',
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

                $redirectPage = "admin/campaign/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = Campaign::select('id', 'operator_id', 'campaign_master_id', 'offer_type', 'offer_value', 'min_ticket_value', 'services', 'auto_renewal', 'validity_type', 'start_date', 'end_date', 'duration_value', 'duration_unit');

                $dataResQry = $dataResQry->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("campaign");
                }
                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/campaign";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [

                    'campaign_master_id' => 'required',
                    'offer_type'         => 'required',
                    'offer_value'        => 'required|numeric',
                    'min_ticket_value'   => 'nullable|numeric',

                    'validity_type'      => 'required|in:DATE_RANGE,DURATION',

                    // Conditional validation
                    'start_date' => 'required_if:validity_type,DATE_RANGE|nullable|date',
                    'end_date'   => 'required_if:validity_type,DATE_RANGE|nullable|date|after_or_equal:start_date',

                    'duration_value' => 'required_if:validity_type,DURATION|nullable|numeric|min:1',
                    'duration_unit'  => 'required_if:validity_type,DURATION|nullable|in:DAY,WEEK',

                ], [
                    'start_date.required_if' => 'Start Date is required for Date Range.',
                    'end_date.required_if'   => 'End Date is required for Date Range.',
                    'duration_value.required_if' => 'Duration Value is required.',
                    'duration_unit.required_if'  => 'Duration Unit is required.',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'errors' => $validator->errors()
                    ]);
                }

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();

                    $campaign_master_id = request('campaign_master_id');
                    $offer_type         = request('offer_type');
                    $offer_value        = request('offer_value');
                    $min_ticket_value   = htmlEncode(request('min_ticket_value'));
                    $services           = request('services');
                    $auto_renewal       = request('auto_renewal');
                    $validity_type      = htmlEncode(request('validity_type'));

                    $start_date     = null;
                    $end_date       = null;
                    $duration_value = null;
                    $duration_unit  = null;

                    // Apply condition based on validity_type
                    if ($validity_type === 'DATE_RANGE') {

                        $start_date = request('start_date');
                        $end_date   = request('end_date');
                    } elseif ($validity_type === 'DURATION') {

                        $duration_value = htmlEncode(request('duration_value'));
                        $duration_unit  = request('duration_unit');
                    }

                    // $duplicate = Campaign::select('id')->where(['campaign_master_id' => $campaign_master_id]);

                    // if ($id != 0) {
                    //     $duplicate->where('id', '!=', $id);
                    // }

                    // if ($duplicate->exists()) {
                    //     return back()->with([
                    //         'level'     => 'danger',
                    //         'message'   => 'Campaign Master already exist'
                    //     ])->withInput();
                    // } else {
                    $obj = ($id != 0) ? Campaign::find($id) : new Campaign();
                    $obj->operator_id = 1;
                    $obj->campaign_master_id = $campaign_master_id;
                    $obj->offer_type = $offer_type;
                    $obj->offer_value = $offer_value;
                    $obj->min_ticket_value = $min_ticket_value;
                    $obj->services = $services;
                    $obj->auto_renewal = $auto_renewal;
                    $obj->validity_type = $validity_type;
                    $obj->start_date = $start_date;
                    $obj->end_date = $end_date;
                    $obj->duration_value = $duration_value;
                    $obj->duration_unit = $duration_unit;
                    $obj->created_by = 1;
                    $obj->active_status = 1;

                    if ($id != 0) {
                        $obj->updated_by = 1;
                    }

                    $obj->save();

                    $campaign_id = $obj->id;

                    $src_id  = request('src_id');
                    $dest_id = request('dest_id');

                    if (!empty($src_id) && !empty($dest_id)) {

                        $routes = ($id != 0) ? CampaignRoutes::find($id) : new CampaignRoutes();

                        $routes->campaign_id = $campaign_id;
                        $routes->src_id = $src_id;
                        $routes->dest_id = $dest_id;
                        $routes->active_status = 1;

                        $routes->save();

                        $campaign_routes_id = $routes->id;
                    }

                    $bus_id = request('bus_id');

                    if (!empty($bus_id) && !empty($campaign_routes_id)) {

                        $services = ($id != 0) ? CampaignServices::find($id) : new CampaignServices();

                        $services->campaign_id = $campaign_id;
                        $services->campaign_routes_id = $campaign_routes_id;
                        $services->bus_id = $bus_id;
                        $services->active_status = 1;

                        $services->save();
                    }

                    $excluded_date = request('excluded_date');

                    if (!empty($excluded_date)) {

                        $excluded_dates = ($id != 0) ? CampaignExcludedDates::find($id) : new CampaignExcludedDates();

                        $excluded_dates->campaign_id = $campaign_id;
                        $excluded_dates->excluded_date = $excluded_date;

                        $excluded_dates->save();
                    }

                    $day_of_week = request('day_of_week');

                    if (!empty($day_of_week)) {

                        $active_days = ($id != 0) ? CampaignActiveDays::find($id) : new CampaignActiveDays();

                        $active_days->campaign_id = $campaign_id;
                        $active_days->day_of_week = $day_of_week;

                        $active_days->save();
                    }

                    session()->flash('level', 'success');
                    session()->flash('message', 'Campaign ' . (($id != 0) ? 'updated' : 'created') . ' successfully.');
                    // }

                    DB::commit();
                    return redirect($redirectPage);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'CampaignController',
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
        return view('Admin.Campaign.addCampaign', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
