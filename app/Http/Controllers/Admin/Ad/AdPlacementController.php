<?php

namespace App\Http\Controllers\Admin\Ad;

use App\Http\Controllers\Controller;
use App\Models\Ad\AdPlacement;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;
use Mews\Purifier\Facades\Purifier;

class AdPlacementController extends Controller
{
    public function index()
    {
        return view('admin.Ad.AdPlacement');
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

                $redirectPage = route('AdPlacement.edit', $encId);

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = AdPlacement::select(
                    'id',
                    'name',
                    'slug',
                    'description',
                    'default_model'
                )->where('id', $id)->first();

                if (!$row) {
                    return redirect()->route('AdPlacement.index');
                }

                $data['row'] = $row;

            } else {
                $id = 0;
                $redirectPage = route('AdPlacement.index');
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [

                    'placement'     => 'bail|required|max:100',
                    'slug'          => 'bail|required|max:100',
                    'description'   => 'nullable|max:500',
                    'defaultModel'  => 'bail|required',

                ], [

                    'placement.required'    => 'Placement cannot be left blank.',
                    'placement.max'         => 'Placement cannot be more than 100 characters.',

                    'slug.required'         => 'Slug cannot be left blank.',
                    'slug.max'              => 'Slug cannot be more than 100 characters.',

                    'description.max'       => 'Description cannot be more than 500 characters.',

                    'defaultModel.required' => 'Default Model must be selected.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $placement    = htmlEncode(trim(Purifier::clean(request('placement'))));
                $slug         = htmlEncode(trim(Purifier::clean(request('slug'))));
                $description  = htmlEncode(trim(Purifier::clean(request('description'))));
                $defaultModel = trim(Purifier::clean(request('defaultModel')));

                if ($id > 0) {

                    $oldData = AdPlacement::find($id);

                    $newData = [
                        'name'          => $placement,
                        'slug'          => $slug,
                        'description'   => $description,
                        'default_model' => $defaultModel
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
                            'mst_ad_placement',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    $oldData->name          = $placement;
                    $oldData->slug          = $slug;
                    $oldData->description   = $description;
                    $oldData->default_model = $defaultModel;
                    $oldData->updated_by    = 1;
                    $oldData->save();

                } else {

                    $row = [
                        'name'          => $placement,
                        'slug'          => $slug,
                        'description'   => $description,
                        'default_model' => $defaultModel,
                        'created_by'    => 1,
                        'active_status' => 1,
                        'created_at'    => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_ad_placement',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    $obj = new AdPlacement();
                    $obj->fill($row);
                    $obj->save();
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Ad Placement ' . ($id > 0 ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error in AdPlacementController@add", [
                'method' => $method,
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('admin.Ad.addAdPlacement', compact('data'));
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
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int) request('selStatus') : '';
            $selModel = (request('selModel') !== null && request('selModel') !== '') ? (int) request('selModel') : '';

            $dataQuery = DB::connection('mysql_dev')->table('ad_placements as a')
                ->select(
                    'a.id as ad_placement_id',
                    'a.name',
                    'a.slug',
                    'a.description',
                    'a.default_model',
                    'a.active_status',
                    'a.created_at',
                    'a.updated_at',
                    'a.created_by',
                    'a.updated_by',
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = a.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = a.updated_by LIMIT 1) as updated_by_name')
                );


            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('a.name', 'like', "%{$txtSearch}%")
                        ->orWhere('a.slug', 'like', "%{$txtSearch}%")
                        ->orWhere('a.description', 'like', "%{$txtSearch}%")
                        ->orWhere('a.default_model', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== '' && $selStatus !== null) {
                $dataQuery->where('a.active_status', (int) $selStatus);
            }

            if ($selModel !== '' && $selModel !== null) {
                $dataQuery->where('a.default_model', (int) $selModel);
            }


            $recordsTotal = DB::connection('mysql_dev')->table('ad_placements')->count();
            $recordsFiltered = (clone $dataQuery)->count();

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);


            if (!empty(request('order'))) {


                $columns = [
                    2 => 'a.name',
                    3 => 'a.slug',
                    4 => 'a.description',
                    5 => 'a.default_model',
                    6 => 'a.created_at',
                    7 => 'a.active_status'
                ];

                $order      = request('order');
                $orderCol   = $columns[$order[0]['column']] ?? 'a.name';
                $orderDir   = $order[0]['dir'] ?? 'asc';
            } else {
                $orderCol = 'a.name';
                $orderDir = 'asc';
            }

            $dataQuery->orderBy($orderCol, $orderDir);


            if ($length === -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery
                    ->offset($start)
                    ->limit($length)
                    ->get();
            }


            foreach ($arrRes as $row) {
                $row->created_date = date('d-M-Y H:i:s', strtotime($row->created_at));
                $row->updated_date = $row->updated_at
                    ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                    : null;

                $row->is_active = ($row->active_status == 1) ? 'Active' : 'Inactive';
                $row->enc_ad_placement_id = Crypt::encryptString($row->ad_placement_id);
            }

            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error("Exception in AdPlacementController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace'         => $t->getTraceAsString()
            ]);

            $recordsTotal    = 0;
            $recordsFiltered = 0;
            $data             = [];
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
}
