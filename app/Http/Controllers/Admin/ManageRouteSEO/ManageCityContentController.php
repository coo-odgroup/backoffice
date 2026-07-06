<?php

namespace App\Http\Controllers\Admin\ManageRouteSEO;

use App\Http\Controllers\Controller;
use App\Models\ManageRouteSEO\ManageCityContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CommonController;

class ManageCityContentController extends Controller
{
    public function index()
    {
        return view('admin.ManageRouteSEO.manageCityContent');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $search = trim(request('search'));
            $selStatus = (request('selstatus') !== null && request('selstatus') !== '') ? (int) request('selstatus') : '';

            $dataQuery = DB::table('odbusmaster.mst_city_content as mc')
                ->leftJoin('odbusmaster.mst_cities as c', 'c.id', '=', 'mc.city_id')
                ->leftJoin('users as uc', 'uc.id', '=', 'mc.created_by')
                ->leftJoin('users as uu', 'uu.id', '=', 'mc.updated_by')
                ->select(
                    'mc.id',
                    'mc.city_id',
                    'mc.content',
                    'mc.active_status',
                    'mc.created_at',
                    'mc.created_by',
                    'mc.updated_at',
                    'mc.updated_by',
                    'c.city_name as city_name',
                    'uc.name as created_by_name',
                    'uu.name as updated_by_name'
                );

            if (!empty($search)) {
                $dataQuery->where(function ($q) use ($search) {
                    $q->where('c.city_name', 'like', "%{$search}%");
                       
                });
            }

            if ($selStatus !== '') {
                $dataQuery->where('mc.active_status', $selStatus);
            }

            $recordsTotal = (clone $dataQuery)->count();

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            if (!empty(request('order'))) {
                $columns = [
                    1 => 'c.city_name',
                    2 => 'mc.content',
                    3 => 'mc.updated_at',
                ];

                $orderBy = request('order');
                $columnIndex = $orderBy[0]['column'] ?? 1;
                $orderType   = $orderBy[0]['dir'] ?? 'asc';

                $orderColumn = $columns[$columnIndex] ?? 'c.city_name';
                $dataQuery->orderBy($orderColumn, $orderType);
            } else {
                $dataQuery->orderBy('mc.id', 'asc');
            }

            $arrRes = ($length == -1)
                ? $dataQuery->get()
                : $dataQuery->offset($start)->limit($length)->get();

            foreach ($arrRes as $val) {
                $val->created_date = !empty($val->created_at) ? date('d-M-Y H:i:s', strtotime($val->created_at)) : '--';
                $val->updated_date = !empty($val->updated_at) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : '--';
                $val->is_active    = ($val->active_status == 1) ? 'Active' : 'Inactive';
                $val->enc_id       = Crypt::encryptString($val->id);
                $val->city_name    = $val->city_name ?? '--';
                $val->content      = $val->content ?? '--';
            }

            $recordsFiltered = $recordsTotal;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in ManageCityContentController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            Log::error("Error", [
                'Controller' => 'ManageCityContentController',
                'Method'     => 'dataTableView',
                'Error'      => $t->getMessage()
            ]);

            $recordsTotal = 0;
            $recordsFiltered = 0;
            $data = [];
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function updateContent(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'enc_id'  => 'required',
                'content' => 'required|string'
            ], [
                'content.required' => 'Content is required.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $id = Crypt::decryptString($request->enc_id);

            $row = DB::table('odbusmaster.mst_city_content')->where('id', $id)->first();

            if (!$row) {
                return response()->json([
                    'status' => false,
                    'message' => 'City content not found.'
                ]);
            }

            DB::table('odbusmaster.mst_city_content')
                ->where('id', $id)
                ->update([
                    'content'    => trim($request->content),
                    'updated_at' => now(),
                    'updated_by' => session('admin_user_id') ?? auth()->id() ?? 1
                ]);

            return response()->json([
                'status' => true,
                'message' => 'Content updated successfully.'
            ]);
        } catch (\Throwable $t) {
            Log::error("ManageCityContentController@updateContent Error", [
                'message' => $t->getMessage(),
                'trace'   => $t->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while updating content.'
            ]);
        }
    }
}
