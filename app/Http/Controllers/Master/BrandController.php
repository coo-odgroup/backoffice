<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function brand()
    {
        return view('master.brand');
    }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $countrySearch = (request('countrySearch') !== null && request('countrySearch') !== '') ? (int)request('countrySearch') : '';

            $dataQuery = DB::table('mst_bus_brand as b')
                ->leftJoin('mst_countries as c', 'c.id', '=', 'b.country')
                ->leftJoin('users as u1', 'u1.id', '=', 'b.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'b.updated_by')
                ->select(
                    'b.id as brand_id',
                    'b.brand_name',
                    'c.name as country',
                    'b.created_at',
                    'b.updated_at',
                    'b.active_status',
                    'u1.name as created_by_name',
                    'u2.name as updated_by_name'
                );
            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('b.brand_name', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($countrySearch) && $countrySearch != '') {
                $dataQuery->where('b.country', $countrySearch);
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('b.active_status', $selStatus);
            }

            $count = $dataQuery->count('b.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [
                    2 => 'b.brand_name',
                    3 => 'c.name',
                    4 => 'b.created_at',
                    5 => 'b.active_status'
                ];

                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 'b.brand_name';
                $orderType     = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'b.brand_name';
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
                    $val->enc_brand_id  = Crypt::encryptString($val->brand_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in BrandController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'BrandController',
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

                $redirectPage = "admin/brand/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = DB::table('mst_bus_brand')
                    ->select('id','country','brand_name')
                    ->where('id',$id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect()->route('brand.index');
                }

                $data['row'] = $dataResQry;

            } else {

                $id = 0;
                $redirectPage = "admin/brand";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'country' => 'required',
                    'brand'   => 'required|max:100'
                ], [
                    'country.required' => 'Country must be selected.',
                    'brand.required'   => 'Bus Brand Name cannot be blank.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $country = request('country');
                $brand   = htmlEncode(request('brand'));

                $duplicate = DB::table('mst_bus_brand')
                    ->where('brand_name',$brand);

                if ($id != 0) {
                    $duplicate->where('id','!=',$id);
                }

                if ($duplicate->exists()) {

                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Bus Brand already exists'
                    ])->withInput();

                }

                if ($id != 0) {

                    DB::table('mst_bus_brand')
                        ->where('id',$id)
                        ->update([
                            'country'     => $country,
                            'brand_name'  => $brand,
                            'updated_by'  => auth()->id(),
                            'updated_at'  => now()
                        ]);

                } else {

                    DB::table('mst_bus_brand')->insert([
                        'country'       => $country,
                        'brand_name'    => $brand,
                        'created_by'    => auth()->id(),
                        'active_status' => 1,
                        'created_at'    => now()
                    ]);

                }

                DB::commit();

                session()->flash('level','success');
                session()->flash('message','Bus Brand '.($id ? 'updated' : 'created').' successfully.');

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error",[
                'Controller' => 'BrandController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addBrand',compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
