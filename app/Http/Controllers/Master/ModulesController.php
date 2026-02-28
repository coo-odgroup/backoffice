<?php

namespace App\Http\Controllers\Master;

use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Modules;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ModulesController extends Controller
{

    public function modules()
    {
        return view('master.modules');
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

                $redirectPage = "admin/modules/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = Modules::find($id);
                if (!$row) {
                    return redirect('admin/modules');
                }

                $data['row'] = $row;
            } else {
                $redirectPage = "admin/modules";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'moduleCode.*' => [
                        'required',
                        'max:100',
                        'regex:/^[A-Z]+(_[A-Z]+)*$/'
                    ],
                    'moduleName.*' => 'required|max:100',
                    'sequence_no.*' => 'nullable|integer',
                    'selParent'    => 'nullable|integer'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $parentId = (int) Purifier::clean(request('selParent', 0));

                $codes = array_map(function ($val) {
                    return strtoupper(trim(Purifier::clean($val)));
                }, request('moduleCode', []));

                $names = array_map(function ($val) {
                    return trim(Purifier::clean($val));
                }, request('moduleName', []));

                $seqs = array_map(function ($val) {
                    return (int) Purifier::clean($val);
                }, request('sequence_no', []));

                if ($id > 0) {

                    // Duplicate check
                    if (
                        Modules::where('code', $codes[0])
                        ->where('id', '!=', $id)
                        ->exists()
                    ) {
                        DB::rollBack();
                        return back()->with([
                            'level'   => 'danger',
                            'message' => 'Module Code already exists.'
                        ])->withInput();
                    }

                    // Auto sequence if parent selected
                    $sequence = ($parentId > 0)
                        ? (Modules::where('parent_id', $parentId)->max('sequence_no') ?? 0) + 1
                        : ($seqs[0] ?? 1);

                    $row->update([
                        'parent_id'   => $parentId,
                        'code'        => htmlEncode($codes[0]),
                        'name'        => htmlEncode($names[0]),
                        'sequence_no' => $sequence,
                        'updated_by'  => 1
                    ]);
                } else {

                    foreach ($codes as $index => $code) {

                        if (Modules::where('code', $code)->exists()) {
                            DB::rollBack();
                            return back()->with([
                                'level'   => 'danger',
                                'message' => "Module Code {$code} already exists."
                            ])->withInput();
                        }

                        $sequence = ($parentId > 0)
                            ? (Modules::where('parent_id', $parentId)->max('sequence_no') ?? 0) + 1
                            : ($seqs[$index] ?? 1);

                        Modules::create([
                            'parent_id'     => $parentId,
                            'code'          => htmlEncode($code),
                            'name'          => htmlEncode($names[$index]),
                            'sequence_no'   => $sequence,
                            'active_status' => 1,
                            'created_by'    => 1
                        ]);
                    }
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Module ' . ($id > 0 ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error in ModulesController@add", [
                'method' => $method,
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addModules', compact('data'));
    }

    public function dataTableView()
    {
        $recordsTotal    = 0;
        $recordsFiltered = 0;
        $data            = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int) request('selStatus') : '';
            $selParent = (request('selParent') !== null && request('selParent') !== '') ? (int) request('selParent') : 0;

            $dataQuery = DB::table('mst_modules as m')
                ->leftJoin('mst_modules as p', 'p.id', '=', 'm.parent_id')
                ->select(
                    'm.id as module_id',
                    'm.name',
                    'm.code',
                    'm.parent_id',
                    'm.sequence_no',
                    'm.active_status',
                    'm.created_at',
                    'm.updated_at',
                    'm.created_by',
                    'm.updated_by',
                    DB::raw('(SELECT code FROM mst_modules WHERE id = m.parent_id LIMIT 1) as parent_module_name'),
                    DB::raw('(SELECT name FROM users WHERE id = m.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = m.updated_by LIMIT 1) as updated_by_name')
                );

            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('m.name', 'like', "%{$txtSearch}%")
                        ->orWhere('m.code', 'like', "%{$txtSearch}%")
                        ->orWhere('p.code', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== '' && $selStatus !== null) {
                $dataQuery->where('m.active_status', (int) $selStatus);
            }

            if ($selParent > 0) {
                $dataQuery->where('m.parent_id', $selParent);
            }

            $recordsTotal    = $dataQuery->count('m.id');
            $recordsFiltered = $recordsTotal;

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            if (!empty(request('order'))) {

                $columns = [
                    2 => 'm.name',
                    3 => 'm.code',
                    4 => 'parent_module_name',
                    5 => 'm.sequence_no',
                    6 => 'm.created_at',
                    7 => 'm.active_status'
                ];

                $order    = request('order');
                $orderCol = $columns[$order[0]['column']] ?? 'm.name';
                $orderDir = $order[0]['dir'] ?? 'asc';
            } else {
                $orderCol = 'm.name';
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

                $row->enc_module_id = Crypt::encryptString($row->module_id);
            }

            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error("Exception in ModulesController@dataTableView", [
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

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
