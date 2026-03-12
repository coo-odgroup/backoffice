<?php

namespace App\Http\Controllers\Admin\Ad;

use App\Http\Controllers\Controller;
use App\Models\Ad\AdCampaign;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdCampaignController extends Controller
{
    public function index()
    {
        return view('admin.Ad.AdCampaign');
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

                $redirectPage = route('AdCampaign.edit', $encId);

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = AdCampaign::select(
                    'id',
                    'vendor_id',
                    'placement_id',
                    'pricing_plan_id',
                    'title',
                    'start_date',
                    'end_date',
                    'total_budget'
                )->where('id', $id)->first();

                if (!$row) {
                    return redirect()->route('AdCampaign.index');
                }

                $data['row'] = $row;
            } else {

                $redirectPage = route('AdCampaign.index');
            }


            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [

                    'vendor'       => 'required',
                    'placement'    => 'required',
                    'pricingPlan'  => 'required',
                    'title'        => 'required|max:100',
                    'startDate'    => 'required|date',
                    'endDate'      => 'required|date|after_or_equal:startDate',
                    'budget'       => 'required|numeric|max:99999999',

                ], [

                    'vendor.required'       => 'Vendor must be selected.',
                    'placement.required'    => 'Placement must be selected.',
                    'pricingPlan.required'  => 'Pricing Plan must be selected.',

                    'title.required'        => 'Title cannot be left blank.',
                    'title.max'             => 'Title cannot be more than 100 characters.',

                    'startDate.required'    => 'Start Date cannot be blank.',
                    'endDate.required'      => 'End Date cannot be blank.',
                    'endDate.after_or_equal' => 'End Date cannot be earlier than Start Date.',

                    'budget.required'       => 'Budget cannot be left blank.',
                    'budget.numeric'        => 'Budget must contain numbers only.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $vendor       = trim(Purifier::clean(request('vendor')));
                $placement    = trim(Purifier::clean(request('placement')));
                $pricingPlan  = trim(Purifier::clean(request('pricingPlan')));
                $title        = trim(Purifier::clean(request('title')));
                $startDate    = trim(Purifier::clean(request('startDate')));
                $endDate      = trim(Purifier::clean(request('endDate')));
                $budget       = trim(Purifier::clean(request('budget')));

                $obj = ($id > 0) ? AdCampaign::find($id) : new AdCampaign();

                $obj->vendor_id       = $vendor;
                $obj->placement_id    = $placement;
                $obj->pricing_plan_id = $pricingPlan;
                $obj->title           = htmlEncode($title);
                $obj->start_date      = $startDate;
                $obj->end_date        = $endDate;
                $obj->total_budget    = $budget;

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
                    'Campaign ' . ($id > 0 ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error in AdCampaignController@add", [
                'method' => $method,
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('admin.Ad.addAdCampaign', compact('data'));
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

            $txtSearch = trim(htmlEncode(request('txtSearch')));
            $selStatus = request('selStatus');

            $dataQuery = DB::connection('mysql_dev')
                ->table('ad_campaigns as c')
                ->select(
                    'c.id as campaign_id',
                    'c.vendor_id',
                    'c.placement_id',
                    'c.pricing_plan_id',
                    'c.title',
                    'c.start_date',
                    'c.end_date',
                    'c.total_budget',
                    'c.active_status',
                    'c.created_at',
                    'c.updated_at',
                    'c.created_by',
                    'c.updated_by',
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = c.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = c.updated_by LIMIT 1) as updated_by_name')
                );


            if (!empty($txtSearch)) {

                $vendorIds = DB::connection('mysql_dev')
                    ->table('vendors')
                    ->where('company_name', 'like', "%{$txtSearch}%")
                    ->pluck('id')
                    ->toArray();

                $placementIds = DB::connection('mysql_dev')
                    ->table('ad_placements')
                    ->where('name', 'like', "%{$txtSearch}%")
                    ->pluck('id')
                    ->toArray();

                $planIds = DB::connection('mysql_dev')
                    ->table('ad_pricing_plans')
                    ->where('plan_name', 'like', "%{$txtSearch}%")
                    ->pluck('id')
                    ->toArray();

                $dataQuery->where(function ($q) use ($txtSearch, $vendorIds, $placementIds, $planIds) {

                    $q->where('c.title', 'like', "%{$txtSearch}%");

                    if (!empty($vendorIds)) {
                        $q->orWhereIn('c.vendor_id', $vendorIds);
                    }

                    if (!empty($placementIds)) {
                        $q->orWhereIn('c.placement_id', $placementIds);
                    }

                    if (!empty($planIds)) {
                        $q->orWhereIn('c.pricing_plan_id', $planIds);
                    }
                });
            }
            if ($selStatus !== '' && $selStatus !== null) {
                $dataQuery->where('c.active_status', $selStatus);
            }


            $recordsTotal = DB::connection('mysql_dev')
                ->table('ad_campaigns')
                ->count();

            $recordsFiltered = (clone $dataQuery)->count();


            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);


            if (!empty(request('order'))) {

                $columns = [
                    2 => 'c.vendor_id',
                    3 => 'c.placement_id',
                    4 => 'c.pricing_plan_id',
                    5 => 'c.title',
                    6 => 'c.start_date',
                    7 => 'c.end_date',
                    8 => 'c.total_budget',
                    9 => 'c.updated_at',
                    10 => 'c.active_status'
                ];

                $order      = request('order');
                $orderCol   = $columns[$order[0]['column']] ?? 'c.created_at';
                $orderDir   = $order[0]['dir'] ?? 'desc';
            } else {

                $orderCol = 'c.created_at';
                $orderDir = 'desc';
            }

            $dataQuery->orderBy($orderCol, $orderDir);

            if ($length == -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery->offset($start)->limit($length)->get();
            }

            /* Lookup names */

            $vendors = DB::connection('mysql_dev')
                ->table('vendors')
                ->pluck('company_name', 'id');

            $placements = DB::connection('mysql_dev')
                ->table('ad_placements')
                ->pluck('name', 'id');

            $plans = DB::connection('mysql_dev')
                ->table('ad_pricing_plans')
                ->pluck('plan_name', 'id');

            foreach ($arrRes as $row) {

                $row->vendorId    = $vendors[$row->vendor_id] ?? '--';
                $row->placementId = $placements[$row->placement_id] ?? '--';
                $row->pricingPlan = $plans[$row->pricing_plan_id] ?? '--';

                $row->startDate = date('d-M-Y', strtotime($row->start_date));
                $row->endDate   = date('d-M-Y', strtotime($row->end_date));

                $row->totalBudget = number_format($row->total_budget);

                $row->created_date = date('d-M-Y H:i:s', strtotime($row->created_at));

                $row->updated_date = $row->updated_at
                    ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                    : null;

                $row->is_active = ($row->active_status == 1) ? 'Active' : 'Inactive';

                $row->enc_campaign_id = Crypt::encryptString($row->campaign_id);
            }

            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error("Exception in AdCampaignController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace'         => $t->getTraceAsString()
            ]);
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
}
