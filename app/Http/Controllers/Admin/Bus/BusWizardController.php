<?php

namespace App\Http\Controllers\Admin\Bus;

use App\Http\Controllers\Controller;
use App\Models\Bus\Bus;
use App\Models\Bus\BusAmenity;
use App\Models\Master\Amenity;
use App\Models\Master\AmenityCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BusWizardController extends Controller
{
    public function step1()
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        $data['categories'] = AmenityCategory::with(['amenities' => function ($q) {
            $q->where('active_status', 1);
        }])
            ->whereHas('amenities', function ($q) {
                $q->where('active_status', 1);
            })
            ->get();
        return view('admin.bus.wizard.step1', compact('data'));
    }

    public function postStep1(Request $request)
    {
        // return $request;
        $request->validate([
            'name' => 'required',
            'bus_number' => 'required',
            'bus_operator_id' => 'required',
            'slab' => 'required',
        ]);

        $bus_operator_id = (int)request('bus_operator_id');
        $cancellationslabs_id = (int)request('slab');
        $name = htmlEncode(request('name'));
        $bus_number = htmlEncode(request('bus_number'));
        $via = htmlEncode(request('via'));
        $max_seat_book = htmlEncode(request('max_seat_book'));

        $gen_bus_type = request('gen_bus_type');
        $gen_bus_type = preg_replace('/\s+/', ' ', $gen_bus_type);
        $gen_bus_type = trim($gen_bus_type);

        $is_irctc_model = (int)request('is_irctc_model');
        $brand_id = (int)request('brand_id');
        $axle_type_id = (int)request('axle_type_id');
        $model_id = (int)request('model_id');
        $service_id = (int)request('service_id');
        $ac_type_id = (int)request('ac_type_id');
        $seat_type_id = (int)request('seat_type_id');
        $seat_layout_type_id = (int)request('seat_layout_type_id');

        $obj = new Bus();
        $obj->bus_operator_id = $bus_operator_id;
        $obj->cancellationslabs_id = $cancellationslabs_id;
        $obj->name = $name;
        $obj->bus_number = $bus_number;
        $obj->via = $via;
        $obj->max_seat_book = $max_seat_book;
        $obj->gen_bus_type = $gen_bus_type;
        $obj->is_irctc_model = $is_irctc_model;
        $obj->brand_id = $brand_id;
        $obj->axle_type_id = $axle_type_id;
        $obj->model_id = $model_id;
        $obj->service_id = $service_id;
        $obj->ac_type_id = $ac_type_id;
        $obj->seat_type_id = $seat_type_id;
        $obj->seat_layout_type_id = $seat_layout_type_id;
        $obj->active_status = 1;

        $obj->save();

        $bus_id = $obj->id;

        $amenities_ids = request('amenities_id', []);

        $category_ids = Amenity::whereIn('id', $amenities_ids)
            ->pluck('category_id')
            ->unique();

        $amenitiesData = [];

        foreach ($amenities_ids as $i => $amenities_id) {

            if (!isset($category_ids[$i])) {
                continue;
            }

            $amenitiesData[] = [
                'bus_id' => $bus_id,
                'category_id' => $category_ids[$i],
                'amenities_id' => $amenities_id,
                'active_status' => 1,
                'created_at' => now(),
                'created_by' => 1
            ];
        }

        if (!empty($amenitiesData)) {
            BusAmenity::insert($amenitiesData);
        }

        session()->flash('level', 'success');
        session()->flash('message', 'Bus Info Created successfully.');

        $enc_bus_id = (!empty($bus_id)) ? Crypt::encryptString($bus_id) : 0;
        return redirect()->route('bus.step2', ['encId' => $enc_bus_id]);
    }

    public function step2()
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        return view('admin.bus.wizard.step2', compact('data'));
    }

    public function postStep2(Request $request)
    {
        session(['bus.step2' => $request->cities]);
        return redirect()->route('bus.step3');
    }

    public function step3()
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        return view('admin.bus.wizard.step3', compact('data'));
    }

    public function postStep3(Request $request)
    {
        session(['bus.step3' => $request->all()]);
        return redirect()->route('bus.step4');
    }

    public function step4()
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        return view('admin.bus.wizard.step4', compact('data'));
    }

    public function postStep4(Request $request)
    {
        session(['bus.step4' => $request->stations]);
        return redirect()->route('bus.step5');
    }

    public function step5()
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        return view('admin.bus.wizard.step5', compact('data'));
    }

    public function postStep5(Request $request)
    {
        session(['bus.step5' => $request->schedule]);
        return redirect()->route('bus.step6');
    }

    public function step6()
    {
        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        return view('admin.bus.wizard.step6', compact('data'));
    }

    public function businfo()
    {
        return view('master.amenities');
    }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $amenityCategory = (request('amenityCategory') !== null && request('amenityCategory') !== '') ? (int)request('amenityCategory') : '';

            $dataQuery = DB::table('mst_amenities as a')
                ->select(
                    'a.id as amenity_id',
                    'a.category_id',
                    'a.amenity_name',
                    'a.description',
                    'a.icon',
                    'a.is_paid',
                    'a.is_seat_specific',
                    'a.sequence_no',
                    'a.created_at',
                    'a.created_by',
                    'a.updated_at',
                    'a.updated_by',
                    'a.active_status',
                    DB::raw('(SELECT category_name FROM mst_amenity_categories WHERE id = a.category_id LIMIT 1) as category_name'),
                    DB::raw('(SELECT name FROM users WHERE id = a.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = a.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('a.amenity_name', 'like', "%{$txtSearch}%")
                        ->orWhere('a.description', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($amenityCategory) && $amenityCategory != '') {
                $dataQuery->where('a.category_id', $amenityCategory);
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('a.active_status', $selStatus);
            }

            $count = $dataQuery->count('a.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'a.amenity_name', 3 => 'ac.category_name', 4 => 'a.description', 5 => 'a.icon', 6 => 'a.is_paid', 7 => 'a.is_seat_specific', 8 => 'a.created_by', 9 => 'a.active_status'];

                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 'a.amenity_name';
                $orderType     = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'a.amenity_name';
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
                    $val->enc_amenity_id   = Crypt::encryptString($val->amenity_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in AmenitiesController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'AmenitiesController',
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

                $redirectPage = "admin/amenities/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = Amenity::select('id', 'category_id', 'amenity_name', 'description', 'icon', 'is_paid', 'is_seat_specific');

                $dataResQry = $dataResQry->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("amenities");
                }
                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/amenities";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'amenityCategory' => 'bail|required',
                    'amenity_name' => 'bail|required',
                    'icon' => 'bail|required'
                ], [
                    'amenityCategory.required' => 'Amenity Category cannot be left blank.',
                    'amenity_name.required' => 'Amenity Name cannot be left blank.',
                    'icon.required' => 'Amenity Icon cannot be left blank.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();

                    $category_id = (int)request('amenityCategory');
                    $amenity_name = htmlEncode(request('amenity_name'));
                    $icon = htmlEncode(request('icon'));
                    $is_paid = (int)request('is_paid');
                    $is_seat_specific = (int)request('is_seat_specific');
                    $description = htmlEncode(request('description'));

                    $duplicate = Amenity::select('id')->where(['amenity_name' => $amenity_name]);

                    if ($id != 0) {
                        $duplicate->where('id', '!=', $id);
                    }

                    if ($duplicate->exists()) {
                        return back()->with([
                            'level'     => 'danger',
                            'message'   => 'Amenity already exist'
                        ])->withInput();
                    } else {
                        $obj = ($id != 0) ? Amenity::find($id) : new Amenity();
                        $obj->category_id = $category_id;
                        $obj->amenity_name = $amenity_name;
                        $obj->icon = $icon;
                        $obj->is_paid = $is_paid;
                        $obj->is_seat_specific = $is_seat_specific;
                        $obj->description = $description;
                        $obj->created_by = 1;
                        $obj->active_status = 1;

                        if ($id != 0) {
                            $obj->updated_by = 1;
                        }

                        $obj->save();

                        session()->flash('level', 'success');
                        session()->flash('message', 'Amenity ' . (($id != 0) ? 'updated' : 'created') . ' successfully.');
                    }

                    DB::commit();
                    return redirect($redirectPage);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'AmenitiesController',
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
        return view('Master.addBusinfo', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }


    public function getcity(Request $request)
    {
        $city = $request->city;

        $cities = DB::table('mst_cities')
            ->when($city, function ($query, $city) {
                return $query->where('city_name', 'LIKE', '%' . $city . '%');
            })
            ->get();

        return response()->json([
            'status' => true,
            'data' => $cities
        ]);
    }
}
