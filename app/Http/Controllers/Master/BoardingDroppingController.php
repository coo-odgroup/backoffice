<?php

namespace App\Http\Controllers\Master;

use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Cities;
use App\Models\Master\BoardingDropping;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\DB;

class BoardingDroppingController extends Controller
{

    public function boardingDropping()
    {
        return view('master.boardingDropping');
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
                $redirectPage = "admin/boardingDropping/edit/" . $encId;
                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = BoardingDropping::select(
                    'id',
                    'cities_id',
                    'type',
                    'brd_drp_point',
                    'landmark',
                    'latitude',
                    'longitude',
                    'sequence_no'
                )->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("boardingDropping");
                }

                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/boardingDropping";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'selCity'         => 'required',
                    'type'            => 'required|array',
                    'brd_drp_point'   => 'required|array',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $cityId     = (int) Purifier::clean(request('selCity'));

                $types      = request('type', []);
                $points     = request('brd_drp_point', []);
                $landmarks  = request('landmark', []);
                $latitudes  = request('latitude', []);
                $longitudes = request('longitude', []);
                $sequences  = request('sequence_no', []);

                $insertData = [];

                foreach ($types as $i => $type) {

                    $insertData[] = [
                        'cities_id'     => $cityId,
                        'type'          => (int) $type,
                        'brd_drp_point' => htmlEncode(trim($points[$i] ?? '')),
                        'landmark'      => htmlEncode(trim($landmarks[$i] ?? '')),
                        'latitude'      => $latitudes[$i] ?? null,
                        'longitude'     => $longitudes[$i] ?? null,
                        'sequence_no'   => $sequences[$i] ?? null,
                        'created_by'    => 1,
                    ];
                }

                if ($id > 0) {

                    $oldData = DB::table('mst_boarding_droping')
                        ->where('id', $id)
                        ->first();

                    $newData = $insertData[0];

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
                            'mst_boarding_droping',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    DB::table('mst_boarding_droping')
                        ->where('id', $id)
                        ->update($newData);

                } else {

                    foreach ($insertData as $row) {
                        app(CommonController::class)->auditLog(
                            'mst_boarding_droping',
                            null,
                            'INSERT',
                            [],
                            $row
                        );
                    }

                    DB::table('mst_boarding_droping')->insert($insertData);
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash('message', 'Boarding / Dropping ' . (($id != 0) ? 'updated' : 'created') . ' successfully.');

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error('BoardingDropping add error', [
                'Method' => $method,
                'Error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addBoardingDropping', compact('data'));
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
            $selCity = (int) request('selCity');
            $selType = (int) request('type', 0);

            $dataQuery = DB::table('mst_boarding_droping as b')
                ->leftJoin('mst_cities as c', 'c.id', '=', 'b.cities_id')
                ->leftJoin('users as u', 'u.id', '=', 'b.created_by')
                ->select(
                    'b.id as bd_id',
                    'c.city_name',
                    'b.brd_drp_point',
                    'b.type',
                    'b.sequence_no',
                    'b.active_status',
                    'b.created_at',
                    'u.name as created_by_name'
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('b.brd_drp_point', 'like', "%{$txtSearch}%")
                        ->orWhere('c.city_name', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== '' && $selStatus !== null) {
                $dataQuery->where('b.active_status', (int)$selStatus);
            }

            if ($selCity > 0) {
                $dataQuery->where('b.cities_id', $selCity);
            }
            if ($selType > 0) {
                $dataQuery->where('b.type', $selType);
            }

            $count = $dataQuery->count('b.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'c.city_name', 3 => 'b.created_at', 4 => 'b.created_by', 5 => 'b.active_status'];

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
                    $val->created_date  = date('d-M-Y H:i:s', strtotime($val->created_at));
                    $val->is_active     = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_bd_id   = Crypt::encryptString($val->bd_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in BoardingDroppingController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'BoardingDroppingController',
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

    public function checkExists(Request $request)
    {
        $exists = DB::table('mst_boarding_droping')
            ->where('cities_id', $request->city_id)
            ->where('type', $request->type)
            ->whereRaw('LOWER(brd_drp_point) = ?', [strtolower(trim($request->point))])
            ->where('active_status', 1)
            ->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }
}
