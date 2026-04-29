<?php

namespace App\Http\Controllers\Admin\Bus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Bus\Bus;
use App\Models\Bus\BusAmenity;
use App\Models\Master\Amenity;
use App\Models\Master\AmenityCategory;

class BusStep1Controller extends Controller
{
    protected $createBusUrl;

    public function __construct()
    {
        $this->createBusUrl = '/admin/bus/create/';

        view()->share('createBusUrl', $this->createBusUrl);
    }

    public function step1($bus_id = null, $param = null, $param2 = null)
    {
        $data = [];
        $data['strPage'] = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';
        $data['categories'] = AmenityCategory::with(['amenities' => function ($q) {
            $q->where('active_status', 1);
        }])
            ->whereHas('amenities', function ($q) {
                $q->where('active_status', 1);
            })
            ->get();

        $busId = (!empty($bus_id)) ? Crypt::decryptString($bus_id) : 0;
        $data['bus_id'] = $busId;
        $data['enc_bus_id'] = $bus_id;
        $data['param'] = $param;
        $data['param2'] = $param2;

        // Edit or Back or Continue
        $step1Res = [];
        $step1AmenityRes = [];
        $selectedAmenities = [];
        if ($busId != 0) {
            $step1Res = Bus::where('id', $busId)->first();
            $step1AmenityRes = BusAmenity::with('amenity')
                ->where('bus_id', $busId)
                ->get()
                ->map(function ($item) {
                    return [
                        (string) $item->amenity->id,
                        $item->amenity->amenity_name
                    ];
                })
                ->values();

            $selectedAmenities = BusAmenity::with('amenity')
                ->where('bus_id', $busId)
                ->pluck('amenities_id')
                ->toArray();
        }

        return view('admin.bus.wizard.step1', compact('data', 'step1Res', 'step1AmenityRes', 'selectedAmenities'));
    }

    public function postStep1(Request $request)
    {
        try {

            DB::beginTransaction();

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

            $busId = (int)request('bus_id');

            // Bus Save
            $obj = ($busId != 0) ? Bus::find($busId) : new Bus();
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

            if ($busId != 0) {
                $obj->updated_by = 1;
            }

            $obj->save();

            $bus_id = $obj->id;

            if ($busId != 0) {
                $bus_id = $busId;
            }

            // Amenities Section
            $amenities_ids = request('amenities_id', []);

            $category_map = Amenity::whereIn('id', $amenities_ids)
                ->pluck('category_id', 'id')
                ->toArray();

            $newAmenities = collect($amenities_ids)
                ->filter(fn($id) => isset($category_map[$id]))
                ->values()
                ->toArray();

            BusAmenity::withTrashed()
                ->where('bus_id', $bus_id)
                ->whereIn('amenities_id', $newAmenities)
                ->restore();

            BusAmenity::where('bus_id', $bus_id)
                ->whereIn('amenities_id', $newAmenities)
                ->update([
                    'deleted_by'    => null,
                    'active_status' => 1,
                    'updated_at'    => now(),
                    'updated_by'    => 1
                ]);

            $existingAll = BusAmenity::where('bus_id', $bus_id)
                ->whereNull('deleted_at')
                ->pluck('amenities_id')
                ->toArray();

            $toInsert = array_diff($newAmenities, $existingAll);

            $insertData = [];

            foreach ($toInsert as $amenities_id) {
                $insertData[] = [
                    'bus_id'        => $bus_id,
                    'category_id'   => $category_map[$amenities_id],
                    'amenities_id'  => $amenities_id,
                    'active_status' => 1,
                    'created_by'    => 1,
                    'created_at'    => now()
                ];
            }

            if (!empty($insertData)) {
                BusAmenity::insert($insertData);
            }

            $existingActive = BusAmenity::where('bus_id', $bus_id)
                ->whereNull('deleted_at')
                ->pluck('amenities_id')
                ->toArray();

            $toDelete = array_diff($existingActive, $newAmenities);

            if (!empty($toDelete)) {
                BusAmenity::where('bus_id', $bus_id)
                    ->whereIn('amenities_id', $toDelete)
                    ->whereNull('deleted_at')
                    ->update([
                        'deleted_at'    => now(),
                        'deleted_by'    => 1,
                        'active_status' => 0,
                        'updated_at'    => now(),
                        'updated_by'    => 1
                    ]);
            }
            // Amenities Section End

            DB::commit();

            session()->flash('level', 'success');
            session()->flash('message', 'Bus info created Successfully.');

            $enc_bus_id = (!empty($bus_id)) ? Crypt::encryptString($bus_id) : 0;
            $param2 = $request->param2;
            if ($param2 == 'edit') {
                return redirect()->route('bus.index');
            } else {
                return redirect()->route('bus.step2', [
                    'encId' => $enc_bus_id,
                    'param' => 'save'
                ]);
            }
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Bus creation failed: ' . $e->getMessage());

            session()->flash('level', 'error');
            session()->flash('message', 'Something went wrong! Please try again.');

            return redirect()->back()->withInput();
        }
    }
}
