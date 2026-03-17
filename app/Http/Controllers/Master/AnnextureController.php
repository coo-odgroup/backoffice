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
use Illuminate\Support\Facades\DB;

class AnnextureController extends Controller
{

    public function annexture()
    {
        return view('master.annexture');
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
                $redirectPage = "admin/annexture/edit/" . $encId;
                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = DB::table('mst_annexture')
                    ->select('id', 'annexture_type_id', 'annexture_name', 'annexture_value')
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect("annexture");
                }

                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/annexture";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [

                    'selAnnexureType'   => 'required|integer|exists:mst_annexture_type,id',

                    'annexture_name'    => 'required|array',
                    'annexture_name.*'  => 'required|string|max:100',

                    'annexture_value'   => 'required|array',
                    'annexture_value.*' => 'required|integer',

                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $annexureTypeId = (int) Purifier::clean(request('selAnnexureType'));

                $names  = array_map(fn($val) => Purifier::clean($val), request('annexture_name', []));
                $values = array_map(fn($val) => (int) Purifier::clean($val), request('annexture_value', []));

                foreach ($names as $i => $name) {

                    $query = DB::table('mst_annexture')
                        ->where('annexture_type_id', $annexureTypeId)
                        ->whereRaw('LOWER(annexture_name) = ?', [strtolower(trim($name))])
                        ->where('active_status', 1);

                    if ($id > 0) {
                        $query->where('id', '!=', $id);
                    }

                    if ($query->exists()) {
                        DB::rollBack();
                        return back()->with([
                            'level'   => 'danger',
                            'message' => 'Duplicate Annexture found for same type'
                        ])->withInput();
                    }
                }

                $insertData = [];

                foreach ($names as $i => $name) {

                    $insertData[] = [
                        'annexture_type_id' => $annexureTypeId,
                        'annexture_name'    => htmlEncode(trim($name ?? '')),
                        'annexture_value'   => $values[$i] ?? null,
                        'active_status'     => 1,
                        'created_at'        => now(),
                        'created_by'        => 1,
                    ];
                }

                if ($id > 0) {
                    DB::table('mst_annexture')
                        ->where('id', $id)
                        ->update($insertData[0]);
                } else {
                    DB::table('mst_annexture')->insert($insertData);
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash('message', 'Annexture ' . (($id != 0) ? 'updated' : 'created') . ' successfully.');

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error('Annexture add error', [
                'Method' => $method,
                'Error'  => $t->getMessage(),
                'Trace'  => $t->getTraceAsString()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addAnnexture', compact('data'));
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
            $selStatus = (request('selStatus') !== null && request('selstatus') !== '') ? (int)request('selstatus') : '';
            $selAnnexureType = (int) request('selAnnexureType');

            $dataQuery = DB::table('mst_annexture as a')
                ->leftJoin('mst_annexture_type as t', 't.id', '=', 'a.annexture_type_id')
                ->leftJoin('users as u', 'u.id', '=', 'a.created_by')
                ->select(
                    'a.id as ann_id',
                    't.annexture_type as annexture_type',
                    'a.annexture_name',
                    'a.annexture_value',
                    'a.active_status',
                    'a.created_at',
                    'u.name as created_by_name'
                );

            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('a.annexture_name', 'like', "%{$txtSearch}%")
                        ->orWhere('t.annexture_type', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== '' && $selStatus !== null) {
                $dataQuery->where('a.active_status', (int)$selStatus);
            }

            if ($selAnnexureType > 0) {
                $dataQuery->where('a.annexture_type_id', $selAnnexureType);
            }

            $countQuery = clone $dataQuery;
            $count = $countQuery->count('a.id');

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            if (!empty(request('order'))) {

                $columns = [
                    2 => 't.annexture_type',
                    3 => 'a.annexture_name',
                    4 => 'a.annexture_value',
                    5 => 'a.created_at',
                    6 => 'a.active_status'
                ];

                $orderBy       = request('order');
                $orderColumn   = $columns[$orderBy[0]['column']] ?? 'a.created_at';
                $orderType     = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'a.created_at';
                $orderType   = 'desc';
            }

            $dataQuery = $dataQuery->orderBy($orderColumn, $orderType);

            if ($length == -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery->limit($length)
                    ->offset($start)
                    ->get();
            }

            if (count($arrRes) > 0) {

                foreach ($arrRes as $val) {
                    $val->created_date  = date('d-M-Y H:i:s', strtotime($val->created_at));
                    $val->is_active     = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_ann_id    = Crypt::encryptString($val->ann_id);
                }
            }

            $recordsTotal     = $count;
            $recordsFiltered  = $count;
            $data             = $arrRes;
        } catch (\Throwable $t) {

            Log::error("Exception in AnnextureController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
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
        $exists = DB::table('mst_annexture')
            ->where('annexture_type_id', $request->annexture_type_id)
            ->where('LOWER(annexture_name) = ?', $request->annexture_name)
            ->whereRaw('LOWER(annexture_value) = ?', [strtolower(trim($request->annexture_value))])
            ->where('active_status', 1)
            ->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }
}
