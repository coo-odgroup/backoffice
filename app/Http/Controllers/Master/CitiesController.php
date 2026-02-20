<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Cities;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CitiesController extends Controller
{

    public function cities()
    {
        return view('master.cities');
    }

    public function add($encId = null)
    {

        $data = [];
        $data['strPage']    = $method = 'Add';
        $data['strSubmit']  = 'Submit';
        $data['strReset']  = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;


            if ($id > 0) {

                $redirectPage = "admin/cities/edit/" . $encId;
                $data['strPage']    = $method = 'Edit';
                $data['strSubmit']  = 'Update';
                $data['strReset']   = 'Cancel';

                $dataResQry = Cities::select('id', 'state_id', 'district_id', 'city_name', 'alias');

                $dataResQry = $dataResQry->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("cities");
                }
                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/cities";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'txtCity' => 'bail|required',
                    'txtCityAlias' => 'bail|required',
                    'selState' => 'required',
                ], [
                    'txtCity.required' => 'City Name cannot be left blank.',
                    'txtCityAlias.required' => 'City Alias cannot be left blank.',
                    'selState.required' => 'State cannot be left blank.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {

                    DB::beginTransaction();

                    $selState  = (int)request('selState');
                    $selDistrict  = (int)request('selDistrict');
                    $txtCity  = htmlEncode(ucwords(strtolower(request('txtCity'))));
                    $txtCityAlias = htmlEncode(request('txtCityAlias'));


                    $duplicate = Cities::select('id')
                        ->where([
                            'city_name' => $txtCity,
                            'alias'     => $txtCityAlias
                        ]);

                    if ($id != 0) {
                        $duplicate->where('id', '!=', $id);
                    }

                    if ($duplicate->exists()) {
                        return back()->with([
                            'level'     => 'danger',
                            'message'   => 'City already exist'
                        ])->withInput();
                    } else {
                        $obj = ($id != 0) ? Cities::find($id) : new Cities();

                        $obj->state_id          = $selState;
                        $obj->district_id       = $selDistrict ?? null;
                        $obj->city_name         = $txtCity;
                        $obj->alias             = $txtCityAlias;
                        $obj->created_by        = 1;
                        $obj->active_status     = 1;
                        if ($id != 0) {
                            $obj->updated_by    = 1;
                        }

                        $obj->save();
                        //Save City Synonym
                        $cityId = $obj->id;

                        $synonyms = request('txtSynonym', []);

                        if (!empty($synonyms)) {

                            if ($id != 0) {
                                DB::table('cities_synonyms')
                                    ->where('cities_id', $cityId)
                                    ->delete();
                            }

                            $insertData = [];

                            foreach ($synonyms as $synonym) {

                                $synonym = trim(htmlEncode($synonym));

                                if ($synonym !== '') {
                                    $insertData[] = [
                                        'cities_id'     => $cityId,
                                        'synonym'       => ucwords(strtolower($synonym)),
                                        'active_status' => 1,
                                        'created_at'    => now(),
                                        'created_by'    => 1
                                    ];
                                }
                            }

                            if (!empty($insertData)) {
                                DB::table('cities_synonyms')->insert($insertData);
                            }
                        }


                        session()->flash('level', 'success');
                        session()->flash('message', 'City ' . (($id != 0) ?
                            'updated' : 'created') . ' successfully.');
                    }

                    DB::commit();
                    return redirect($redirectPage);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'CitiesController',
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
        return view('Master.addCities', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $selState = (int) request('selState');
            $selDistrict = (int) request('selDistrict');

           $dataQuery = DB::table('mst_cities as c')
                            ->select(
                                'c.id as city_id',
                                'c.city_name',
                                'c.alias',
                                DB::raw('(SELECT state_name FROM mst_states as s WHERE s.id = c.state_id LIMIT 1) as state_name'),
                                'c.created_at',
                                'c.created_by',
                                'c.updated_at',
                                'c.updated_by',
                                DB::raw('(SELECT name FROM users as u WHERE u.id = c.created_by LIMIT 1) as created_by_name'),
                                DB::raw('(SELECT name FROM users as u WHERE u.id = c.updated_by LIMIT 1) as updated_by_name'),
                                'c.active_status',
                                DB::raw('(
                                    SELECT GROUP_CONCAT(synonym SEPARATOR "||")
                                    FROM cities_synonyms
                                    WHERE cities_synonyms.cities_id = c.id
                                    AND cities_synonyms.active_status = 1
                                ) as synonyms')
                            );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('c.city_name', 'like', "%{$txtSearch}%")
                        ->orWhere('c.alias', 'like', "%{$txtSearch}%")

                        // Synonym search using EXISTS (no join)
                       ->orWhereExists(function ($sub) use ($txtSearch) {
                            $sub->select(DB::raw(1))
                                ->from('cities_synonyms as cs')
                                ->whereRaw('cs.city_id = c.id')
                                ->where('cs.active_status', 1)
                                ->where('cs.synonym', 'like', "%{$txtSearch}%");
                        });
                });
            }


            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('c.active_status', $selStatus);
            }

            if ($selState > 0) {
                $dataQuery->where('c.state_id', $selState);
            }

            if ($selDistrict > 0) {
                $dataQuery->where('c.district_id', $selDistrict);
            }


            $count = $dataQuery->count('c.id');
          
            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [
                    2 => 'state_name',
                    3 => 'c.city_name',
                    4 => 'c.alias',
                    5 => 'synonyms',
                    6 => 'c.created_at',
                    7 => 'c.active_status'
                ];


                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 'c.city_name';
                $orderType     = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'c.city_name';
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

                    $val->city_alias    = $val->alias ?? '--';
                    $val->created_date  = date('d-M-Y H:i:s', strtotime($val->created_at));
                    $val->updated_date  = ($val->updated_at != null) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : null;
                    $val->is_active     = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_city_id   = Crypt::encryptString($val->city_id);

                    $val->synonym = !empty($val->synonyms)
                        ? implode('<br>', explode('||', $val->synonyms))
                        : '--';
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;
        } catch (\Throwable $t) {

            log::info("Exception occurred in CitiesController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'CityController',
                'Method'     => 'dataTableView',
                'Error'      =>  $errorMsg
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
}
