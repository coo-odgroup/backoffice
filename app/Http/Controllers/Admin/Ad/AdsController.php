<?php

namespace App\Http\Controllers\Admin\Ad;

use App\Http\Controllers\Controller;
use App\Models\blogs\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdsController extends Controller
{
    public function index()
    {
        return view('admin.Ad.ads');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '')
                ? (int)request('selStatus') : '';

            $dataQuery = DB::connection('mysql_dev')
                ->table('ads as a')
                ->select(
                    'a.id as ads_id',
                    'a.campaign_id',
                    'a.redirect_url',
                    'a.alt_text',
                    'a.image',
                    'a.impressions',
                    'a.clicks',
                    'a.created_at',
                    'a.updated_at',
                    'a.created_by',
                    'a.updated_by',
                    'a.active_status',

                    DB::raw('(SELECT title FROM ad_campaigns WHERE id = a.campaign_id LIMIT 1) as campaign'),
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = a.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = a.updated_by LIMIT 1) as updated_by_name')
                );
            if (!empty($txtSearch)) {

                $campaignIds = DB::connection('mysql_dev')
                    ->table('ad_campaigns')
                    ->where('title', 'like', "%{$txtSearch}%")
                    ->pluck('id')
                    ->toArray();

                $dataQuery->where(function ($q) use ($txtSearch, $campaignIds) {

                    $q->where('a.redirect_url', 'like', "%{$txtSearch}%");

                    if (!empty($campaignIds)) {
                        $q->orWhereIn('a.campaign_id', $campaignIds);
                    }
                });
            }


            if ($selStatus !== '') {
                $dataQuery->where('a.active_status', $selStatus);
            }

            $count = $dataQuery->count('a.id');


            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            if (!empty(request('order'))) {

                $columns = [
                    2 => 'a.campaign_id',
                    4 => 'a.redirect_url',
                    5 => 'a.impressions',
                    6 => 'a.clicks',
                    7 => 'a.updated_at',
                    8 => 'a.active_status'
                ];

                $orderBy = request('order');

                $orderColumn = $columns[$orderBy[0]['column']] ?? 'a.created_at';
                $orderType = $orderBy[0]['dir'];
            } else {

                $orderColumn = 'a.created_at';
                $orderType = 'desc';
            }

            $dataQuery = $dataQuery->orderBy($orderColumn, $orderType);


            if ($length == -1) {

                $arrRes = $dataQuery->get();
            } else {

                $arrRes = $dataQuery
                    ->limit($length)
                    ->offset($start)
                    ->get();
            }

            if ($arrRes->count() > 0) {

                foreach ($arrRes as $val) {

                    $val->redirectURL = $val->redirect_url;

                    $val->created_date = date('d-M-Y H:i:s', strtotime($val->created_at));

                    $val->updated_date = ($val->updated_at)
                        ? date('d-M-Y H:i:s', strtotime($val->updated_at))
                        : null;

                    $val->is_active = ($val->active_status == 1)
                        ? 'Active'
                        : 'Inactive';

                    $val->enc_ads_id = Crypt::encryptString($val->ads_id);
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error("Exception in AdsController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $recordsTotal = 0;
            $recordsFiltered = 0;
            $data = [];
        }

        return response()->json([
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function add($encId = null)
    {
        $config = config('constants.ads');

        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = "admin/ads/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = DB::connection('mysql_dev')
                    ->table('ads')
                    ->select('id', 'campaign_id', 'redirect_url', 'alt_text', 'image')
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect("admin/ads");
                }

                $data['row'] = $dataResQry;
            } else {

                $id = 0;
                $redirectPage = "admin/ads";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'campaign' => 'bail|required',
                    'redirectUrl' => 'bail|required',
                    'alt_text' => 'bail|required'
                ], [
                    'campaign.required' => 'Campaign must be selected.',
                    'redirectUrl.required' => 'Redirect URL cannot be left blank.',
                    'alt_text.required' => 'Alt Text cannot be left blank.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $campaign_id = request('campaign');
                $redirect_url = htmlEncode(request('redirectUrl'));
                $alt_text = htmlEncode(request('alt_text'));

                $obj = ($id != 0)
                    ? DB::connection('mysql_dev')->table('ads')->where('id', $id)->first()
                    : null;

                if ($id != 0) {
                    $ads = DB::connection('mysql_dev')->table('ads')->where('id', $id);
                }

                $dataArr = [
                    'campaign_id' => $campaign_id,
                    'redirect_url' => $redirect_url,
                    'alt_text' => $alt_text,
                    'active_status' => 1
                ];

                if ($id == 0) {
                    $dataArr['created_by'] = 1;
                    $dataArr['created_at'] = now();
                } else {
                    $dataArr['updated_by'] = 1;
                    $dataArr['updated_at'] = now();
                }

                $path = "uploads/Ad/Ads";

                if (!Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->makeDirectory($path);
                }

                if (request()->hasFile('ad_image')) {

                    if ($id != 0 && !empty($data['row']->image)) {

                        if (Storage::disk('public')->exists($path . '/' . $data['row']->image)) {
                            Storage::disk('public')->delete($path . '/' . $data['row']->image);
                        }
                    }

                    $file = request()->file('ad_image');

                    $imageName = 'ad-' . time() . rand() . '.' . $file->getClientOriginalExtension();

                    $file->storeAs($path, $imageName, 'public');

                    $dataArr['image'] = $imageName;
                }

                /*
                |--------------------------------
                | Insert / Update
                |--------------------------------
                */

                if ($id == 0) {

                    DB::connection('mysql_dev')->table('ads')->insert($dataArr);
                } else {

                    DB::connection('mysql_dev')
                        ->table('ads')
                        ->where('id', $id)
                        ->update($dataArr);
                }

                session()->flash('level', 'success');
                session()->flash('message', 'Ads ' . (($id != 0) ? 'updated' : 'created') . ' successfully.');

                DB::commit();

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            Log::error("Error", [
                'Controller' => 'AdsController',
                'Method' => $method,
                'Error' => $t->getMessage()
            ]);

            DB::rollBack();

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            return back()->with([
                'level' => 'danger',
                'message' => $errorMsg
            ])->withInput();
        }

        return view('admin.Ad.addAds', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
