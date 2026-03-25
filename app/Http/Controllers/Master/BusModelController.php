<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Validator;

class BusModelController extends Controller
{
    public function busModel()
    {
        return view('master.busModel');
    }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $brandSearch = (request('brandSearch') !== null && request('brandSearch') !== '') ? (int)request('brandSearch') : '';


            $dataQuery = DB::table('mst_bus_models as b')
                ->leftJoin('mst_bus_brand as c', 'c.id', '=', 'b.brand_id')
                ->leftJoin('users as u1', 'u1.id', '=', 'b.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'b.updated_by')
                ->select(
                    'b.id as model_id',
                    'b.model_name',
                    'c.brand_name as brand_id',
                    'b.created_at',
                    'b.description',
                    'b.updated_at',
                    'b.active_status',
                    'u1.name as created_by_name',
                    'u2.name as updated_by_name'
                );
            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('b.model_name', 'like', "%{$txtSearch}%");
                });
            }


            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('b.active_status', $selStatus);
            }
            if (!empty($brandSearch)) {
                $dataQuery->where('b.brand_id', $brandSearch);
            }

            $count = $dataQuery->count('b.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [
                    2 => 'b.model_name',
                    3 => 'c.brand_name',
                    4 =>  'b.description',
                    5 => 'b.created_at',
                    65 => 'b.active_status'
                ];

                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 'b.model_name';
                $orderType     = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'b.model_name';
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
                    $val->enc_model_id  = Crypt::encryptString($val->model_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in BusModelController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'BusModelController',
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
        $data['strPage']   = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset']  = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = "admin/busModel/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = DB::table('mst_bus_models')
                    ->select('id', 'brand_id', 'model_name','description')
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect()->route('busModel.index');
                }

                $data['row'] = $dataResQry;

            } else {

                $id = 0;
                $redirectPage = route('busModel.index');
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'brand' => 'bail|required',
                    'model' => 'bail|required|max:100',
                    'description' => 'max:500'
                ], [
                    'brand.required' => 'Brand must be selected.',
                    'model.required' => 'Bus Model Name cannot be blank.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $brand = request('brand');
                $model = htmlEncode(request('model'));
                $description = htmlEncode(request('description'));

                $duplicate = DB::table('mst_bus_models')
                    ->where('model_name', $model)
                    ->where('brand_id', $brand);

                if ($id != 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Bus Model already exists'
                    ])->withInput();
                }

                if ($id != 0) {

                    $oldData = DB::table('mst_bus_models')
                        ->where('id', $id)
                        ->first();

                    $newData = [
                        'brand_id'   => $brand,
                        'model_name' => $model,
                        'description'=> $description
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
                            'mst_bus_models',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    DB::table('mst_bus_models')
                        ->where('id', $id)
                        ->update([
                            'brand_id'   => $brand,
                            'model_name' => $model,
                            'description'=> $description,
                            'updated_by' => auth()->id(),
                            'updated_at' => now()
                        ]);

                } else {

                    $row = [
                        'brand_id'      => $brand,
                        'model_name'    => $model,
                        'description'   => $description,
                        'created_by'    => auth()->id(),
                        'active_status' => 1,
                        'created_at'    => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_bus_models',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    DB::table('mst_bus_models')->insert($row);
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Bus Model ' . (($id != 0) ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'BusModelController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addBusModel', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
