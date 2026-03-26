<?php

namespace App\Http\Controllers\Admin\Campaign;

use App\Http\Controllers\Controller;
use App\Models\Campaign\CampaignMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CampaignMasterController extends Controller
{
    public function index()
    {
        return view('Admin.Campaign.campaignMaster');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';

            $dataQuery = DB::table('campaign_master as cm')
                ->select(
                    'cm.id as campaign_master_id',
                    'cm.campaign_name',
                    'cm.short_desc',
                    'cm.full_desc',
                    'cm.start',
                    'cm.stop',
                    'cm.created_at',
                    'cm.created_by',
                    'cm.updated_at',
                    'cm.updated_by',
                    'cm.active_status',
                    DB::raw('(SELECT name FROM users WHERE id = cm.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = cm.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('cm.campaign_name', 'like', "%{$txtSearch}%")
                        ->orWhere('cm.short_desc', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('cm.active_status', $selStatus);
            }

            $count = $dataQuery->count('cm.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'cm.campaign_name', 3 => 'ac.short_desc', 4 => 'cm.full_desc', 5 => 'cm.start', 6 => 'cm.stop', 7 => 'cm.created_by', 8 => 'cm.active_status'];

                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 'cm.campaign_name';
                $orderType     = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'cm.campaign_name';
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
                    $val->enc_campaign_master_id   = Crypt::encryptString($val->campaign_master_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in CampaignMasterController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'CampaignMasterController',
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

                $redirectPage = "admin/campaign-master/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = CampaignMaster::select('id', 'campaign_name', 'short_desc', 'full_desc', 'start', 'stop');

                $dataResQry = $dataResQry->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("campaign-master");
                }
                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/campaign-master";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'campaign_name' => 'bail|required|max:100',

                    'short_desc'    => 'nullable|max:255',
                    'full_desc'     => 'nullable|max:255',

                    'start'         => 'bail|required',
                    'stop'          => 'bail|required',

                ], [
                    'campaign_name.required' => 'Campaign Name cannot be left blank.',
                    'campaign_name.max'      => 'Campaign Name cannot exceed 100 characters.',

                    'short_desc.max'         => 'Short Description cannot exceed 255 characters.',
                    'full_desc.max'          => 'Full Description cannot exceed 255 characters.',

                    'start.required'         => 'Start Date cannot be left blank.',

                    'stop.required'          => 'Stop Date cannot be left blank.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();

                    $campaign_name = htmlEncode(request('campaign_name'));
                    $start = request('start');
                    $stop = request('stop');
                    $short_desc = htmlEncode(request('short_desc'));
                    $full_desc = htmlEncode(request('full_desc'));

                    $duplicate = CampaignMaster::select('id')->where(['campaign_name' => $campaign_name]);

                    if ($id != 0) {
                        $duplicate->where('id', '!=', $id);
                    }

                    if ($duplicate->exists()) {
                        return back()->with([
                            'level'     => 'danger',
                            'message'   => 'Campaign already exist'
                        ])->withInput();
                    } else {
                        $obj = ($id != 0) ? CampaignMaster::find($id) : new CampaignMaster();
                        $obj->campaign_name = $campaign_name;
                        $obj->start = $start;
                        $obj->stop = $stop;
                        $obj->short_desc = $short_desc;
                        $obj->full_desc = $full_desc;
                        $obj->created_by = 1;
                        $obj->active_status = 1;

                        if ($id != 0) {
                            $obj->updated_by = 1;
                        }

                        $obj->save();

                        session()->flash('level', 'success');
                        session()->flash('message', 'Campaign Master ' . (($id != 0) ? 'updated' : 'created') . ' successfully.');
                    }

                    DB::commit();
                    return redirect($redirectPage);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'CampaignMasterController',
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
        return view('Admin.Campaign.addCampaignMaster', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
