<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\AmenityCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;


class AmenityCategoryController extends Controller
{
    public function amenityCategory()
    {
        return view('master.amenityCategory');
    }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';

            $dataQuery = DB::table('mst_amenity_categories as ac')
                ->select(
                    'ac.id as amenity_cat_id',
                    'ac.category_name',
                    'ac.description',
                    'ac.display_order',
                    'ac.created_at',
                    'ac.created_by',
                    'ac.updated_at',
                    'ac.updated_by',
                    'ac.active_status',
                    DB::raw('(SELECT name FROM users WHERE id = ac.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = ac.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('ac.category_name', 'like', "%{$txtSearch}%")
                        ->orWhere('ac.description', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('ac.active_status', $selStatus);
            }

            $count = $dataQuery->count('ac.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'ac.category_name', 3 => 'ac.description', 4 => 'ac.display_order', 5 => 'ac.created_by', 6 => 'ac.active_status'];

                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 'ac.category_name';
                $orderType     = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'ac.category_name';
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
                    $val->enc_amenity_cat_id   = Crypt::encryptString($val->amenity_cat_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in AmenityCategoryController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'AmenityCategoryController',
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

                $redirectPage = "admin/amenitycategory/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = AmenityCategory::select('id', 'category_name', 'description', 'display_order')
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect("amenitycategory");
                }

                $data['row'] = $dataResQry;

            } else {
                $redirectPage = "admin/amenitycategory";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'category_name' => 'bail|required'
                ], [
                    'category_name.required' => 'Amenity Category Name cannot be left blank.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $category_name = htmlEncode(request('category_name'));
                $description   = htmlEncode(request('description'));

                $duplicate = AmenityCategory::where('category_name', $category_name);

                if ($id != 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Amenity Category already exist'
                    ])->withInput();
                }

                // ================= UPDATE =================
                if ($id != 0) {

                    $oldData = AmenityCategory::find($id);

                    $newData = [
                        'category_name' => $category_name,
                        'description'   => $description,
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

                    // ✅ LOG BEFORE UPDATE
                    if (!empty($newChanged)) {
                        app(CommonController::class)->auditLog(
                            'mst_amenity_category',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    // ✅ SAVE
                    $oldData->category_name = $category_name;
                    $oldData->description   = $description;
                    $oldData->updated_by    = 1;
                    $oldData->save();
                }

                // ================= INSERT =================
                else {

                    $row = [
                        'category_name' => $category_name,
                        'description'   => $description,
                        'created_by'    => 1,
                        'active_status' => 1,
                        'created_at'    => now()
                    ];

                    // ✅ LOG BEFORE INSERT
                    app(CommonController::class)->auditLog(
                        'mst_amenity_category',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    $obj = new AmenityCategory();
                    $obj->fill($row);
                    $obj->save();
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Amenity Category ' . ($id ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'AmenityCategoryController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addAmenityCategory', compact('data'));
    }


    public function edit($encId)
    {
        return $this->add($encId);
    }
}


