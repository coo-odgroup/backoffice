<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AnnextureTypeController extends Controller
{
    public function annextureType()
    {
        return view('master.annextureType');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '')
                ? (int)request('selStatus') : '';

            $dataQuery = DB::table('mst_annexture_type as m')
                ->select(
                    'm.id as annextureType_id',
                    'm.anexture_type',
                    'm.created_at',
                    'm.updated_at',
                    'm.active_status',
                    DB::raw('(SELECT name FROM users WHERE id = m.created_by) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = m.updated_by) as updated_by_name')
                );
            if (!empty($txtSearch)) {
                $dataQuery->where('m.anexture_type', 'like', "%{$txtSearch}%");
            }

            // Status filter
            if ($selStatus !== '') {
                $dataQuery->where('m.active_status', $selStatus);
            }
            $count = $dataQuery->count('m.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            // Ordering
            if (!empty(request('order'))) {

                $columns = [
                    2 => 'm.anexture_type',
                    3 => 'm.created_at',
                    4 => 'm.active_status'
                ];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'm.anexture_type';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'm.anexture_type';
                $orderType = 'asc';
            }

            $dataQuery->orderBy($orderColumn, $orderType);

            if ($length == -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery->limit($length)
                    ->offset($start)
                    ->get();
            }

            foreach ($arrRes as $val) {
                $val->created_date = date('d-M-Y H:i:s', strtotime($val->created_at));
                $val->updated_date = $val->updated_at
                    ? date('d-M-Y H:i:s', strtotime($val->updated_at))
                    : null;

                $val->is_active = ($val->active_status == 1) ? 'Active' : 'Inactive';

                $val->enc_annextureType_id = Crypt::encryptString($val->annextureType_id);
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error("AnnextureTypeController@dataTableView Error", [
                'message' => $t->getMessage()
            ]);
        }

        return response()->json([
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
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

                $redirectPage = route('annextureType.edit', $encId);

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $dataResQry = DB::table('mst_annexture_type')
                    ->select('id', 'anexture_type')
                    ->where('id', $id)
                    ->first();

                if (!$dataResQry) {
                    return redirect()->route('annextureType.index');
                }

                $data['row'] = $dataResQry;
            } else {

                $id = 0;
                $redirectPage = route('annextureType.index');
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'annextureType' => 'required|max:100'
                ], [
                    'annextureType.required' => 'Annexture Type Name cannot be blank.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $annextureType  = htmlEncode(request('annextureType'));

                $duplicate = DB::table('mst_annexture_type')
                    ->where('anexture_type', $annextureType);

                if ($id != 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {

                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Annexture Type already exists'
                    ])->withInput();
                }

                if ($id != 0) {

                    DB::table('mst_annexture_type')
                        ->where('id', $id)
                        ->update([
                            'anexture_type' => $annextureType,
                            'updated_by'       => auth()->id(),
                            'updated_at'       => now()
                        ]);
                } else {

                    DB::table('mst_annexture_type')->insert([
                        'anexture_type' => $annextureType,
                        'created_by'       => auth()->id(),
                        'active_status'    => 1,
                        'created_at'       => now()
                    ]);
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash('message', 'Annexture Type ' . ($id ? 'updated' : 'created') . ' successfully.');

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'AnnextureTypeController',
                'Method'     => $method,
                'Error'      => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addAnnextureType', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
