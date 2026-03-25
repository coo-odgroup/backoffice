<?php

namespace App\Http\Controllers\Admin\Ad;

use App\Http\Controllers\Controller;
use App\Models\Ad\PricingPlan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\CommonController;

class PricingPlanController extends Controller
{
    public function index()
    {
        return view('admin.Ad.pricingPlan');
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

                $redirectPage = route('pricingPlan.edit', $encId);

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = PricingPlan::select(
                    'id',
                    'plan_name',
                    'placement_id',
                    'model',
                    'price',
                    'duration_days'
                )->where('id', $id)->first();

                if (!$row) {
                    return redirect()->route('pricingPlan.index');
                }

                $data['row'] = $row;

            } else {
                $id = 0;
                $redirectPage = route('pricingPlan.index');
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [

                    'placement'     => 'bail|required',
                    'defaultModel'  => 'bail|required',
                    'planName'      => 'bail|required|max:100',
                    'Price'         => 'bail|required|numeric',
                    'duration'      => 'bail|required|integer|min:1|max:90',

                ], [

                    'placement.required'     => 'Placement must be selected.',
                    'defaultModel.required'  => 'Default Model must be selected.',

                    'planName.required'      => 'Plan Name cannot be left blank.',
                    'planName.max'           => 'Plan Name cannot be more than 100 characters.',

                    'Price.required'         => 'Price cannot be left blank.',
                    'Price.numeric'          => 'Price must contain numbers only.',

                    'duration.required'      => 'Time Duration cannot be blank.',
                    'duration.min'           => 'Time Duration must be between 1 and 90 days.',
                    'duration.max'           => 'Time Duration must be between 1 and 90 days.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $placementId = (int) Purifier::clean(request('placement'));
                $model       = trim(Purifier::clean(request('defaultModel')));
                $planName    = htmlEncode(trim(Purifier::clean(request('planName'))));
                $price       = (float) Purifier::clean(request('Price'));
                $duration    = (int) Purifier::clean(request('duration'));

                if ($id > 0) {

                    $oldData = PricingPlan::find($id);

                    $newData = [
                        'plan_name'     => $planName,
                        'placement_id'  => $placementId,
                        'model'         => $model,
                        'price'         => $price,
                        'duration_days' => $duration
                    ];

                    $oldChanged = [];
                    $newChanged = [];

                    foreach ($newData as $key => $value) {
                        $oldValue = $oldData->$key ?? null;

                        if (trim((string)$oldValue) !== trim((string)$value)) {
                            $oldChanged[$key] = $oldValue;
                            $newChanged[$key] = $value;
                        }
                    }

                    if (!empty($newChanged)) {
                        app(CommonController::class)->auditLog(
                            'mst_pricing_plan',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }


                    $oldData->plan_name     = $planName;
                    $oldData->placement_id  = $placementId;
                    $oldData->model         = $model;
                    $oldData->price         = $price;
                    $oldData->duration_days = $duration;
                    $oldData->updated_by    = 1;
                    $oldData->save();

                } else {

                    $row = [
                        'plan_name'     => $planName,
                        'placement_id'  => $placementId,
                        'model'         => $model,
                        'price'         => $price,
                        'duration_days' => $duration,
                        'created_by'    => 1,
                        'active_status' => 1,
                        'created_at'    => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_pricing_plan',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    $obj = new PricingPlan();
                    $obj->fill($row);
                    $obj->save();
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Pricing Plan ' . ($id > 0 ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error in PricingPlanController@add", [
                'method' => $method,
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('admin.Ad.addPricingPlan', compact('data'));
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
            $selModel  = trim(request('selModel'));

            $dataQuery = DB::connection('mysql_dev')->table('ad_pricing_plans as p')
                ->select(
                    'p.id as pricing_plan_id',
                    'p.placement_id',
                    'p.plan_name',
                    'p.model',
                    'p.price',
                    'p.duration_days',
                    'p.active_status',
                    'p.created_at',
                    'p.updated_at',
                    'p.created_by',
                    'p.updated_by',
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = p.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = p.updated_by LIMIT 1) as updated_by_name')
                );


            if (!empty($txtSearch)) {

                $placementIds = DB::connection('mysql_dev')
                    ->table('ad_placements')
                    ->where('name', 'like', "%{$txtSearch}%")
                    ->pluck('id')
                    ->toArray();

                $dataQuery->where(function ($q) use ($txtSearch, $placementIds) {

                    $q->where('p.plan_name', 'like', "%{$txtSearch}%");

                    if (!empty($placementIds)) {
                        $q->orWhereIn('p.placement_id', $placementIds);
                    }
                });
            }

            if ($selStatus !== '' && $selStatus !== null) {
                $dataQuery->where('p.active_status', $selStatus);
            }

            if (!empty($selModel)) {
                $dataQuery->where('p.model', $selModel);
            }

            $recordsTotal = DB::connection('mysql_dev')->table('ad_pricing_plans')->count();
            $recordsFiltered = (clone $dataQuery)->count();

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            if (!empty(request('order'))) {

                $columns = [
                    2 => 'p.placement_id',
                    3 => 'p.plan_name',
                    4 => 'p.model',
                    5 => 'p.price',
                    6 => 'p.duration_days',
                    7 => 'p.created_at',
                    8 => 'p.active_status'
                ];

                $order      = request('order');
                $orderCol   = $columns[$order[0]['column']] ?? 'p.created_at';
                $orderDir   = $order[0]['dir'] ?? 'desc';

            } else {

                $orderCol = 'p.created_at';
                $orderDir = 'desc';
            }

            $dataQuery->orderBy($orderCol, $orderDir);

            if ($length === -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery->offset($start)->limit($length)->get();
            }

            $placements = DB::connection('mysql_dev')
                ->table('ad_placements')
                ->pluck('name', 'id');

            foreach ($arrRes as $row) {

                $row->created_date = date('d-M-Y H:i:s', strtotime($row->created_at));

                $row->updated_date = $row->updated_at
                    ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                    : null;

                $row->is_active = ($row->active_status == 1) ? 'Active' : 'Inactive';

                $row->placementId = $placements[$row->placement_id] ?? '--';

                $row->planName = $row->plan_name;

                $row->timeDuration = $row->duration_days . ' Days';

                $row->enc_pricing_plan_id = Crypt::encryptString($row->pricing_plan_id);
            }

            $data = $arrRes;

        } catch (\Throwable $t) {

            Log::error("Exception in PricingPlanController@dataTableView", [
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
