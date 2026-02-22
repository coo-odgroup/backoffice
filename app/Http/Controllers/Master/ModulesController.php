<?php

namespace App\Http\Controllers\Master;

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

            /* =======================
            * EDIT MODE
            * ======================= */
            if ($id > 0) {

                $redirectPage = "admin/modules/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = Modules::select(
                    'id',
                    'name',
                    'code',
                    'sequence_no',
                    'active_status'
                )->where('id', $id)->first();

                if (!$row) {
                    return redirect('admin/modules');
                }

                $data['row'] = $row;

            } else {
                $redirectPage = "admin/modules";
            }

            /* =======================
            * POST (ADD / UPDATE)
            * ======================= */
            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'moduleName' => 'required|max:100',
                    'moduleCode' => [
                        'required',
                        'max:100',
                        'regex:/^[A-Z]+(_[A-Z]+)*$/'
                    ],
                    'sequence_no'=> 'nullable|integer'
                ], [
                    'moduleName.required' => 'Module Name cannot be blank.',
                    'moduleCode.required' => 'Module Code cannot be blank.',
                    'moduleCode.regex'    => 'Module Code must be CAPITAL letters with underscore (_).'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                /* DUPLICATE CHECK */
                $duplicate = Modules::where('code', request('moduleCode'));

                if ($id > 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Module Code already exists.'
                    ])->withInput();
                }

                /* SAVE DATA */
                $obj = ($id > 0) ? Modules::find($id) : new Modules();

                $obj->name        = htmlEncode(request('moduleName'));
                $obj->code        = htmlEncode(request('moduleCode'));
                $obj->sequence_no = request('sequence_no') ?? 0;
                $obj->active_status = 1;

                if ($id > 0) {
                    $obj->updated_by = 1; // auth()->id()
                } else {
                    $obj->created_by = 1; // auth()->id()
                }

                $obj->save();

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

            $txtSearch = htmlEncode(request('txtSearch'));$selStatus = (request('selStatus') !== null && request('selStatus') !== '')? (int) request('selStatus'): '';

            $dataQuery = DB::table('mst_modules as m')
                ->select(
                    'm.id as module_id',
                    'm.name',
                    'm.code',
                    'm.sequence_no',
                    'm.active_status',
                    'm.created_at',
                    'm.updated_at',
                    'm.created_by',
                    'm.updated_by',
                    DB::raw('(SELECT name FROM users WHERE id = m.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = m.updated_by LIMIT 1) as updated_by_name')
                );

            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('m.name', 'like', "%{$txtSearch}%")
                    ->orWhere('m.code', 'like', "%{$txtSearch}%");
                });
            }

            if ($selStatus !== '' && $selStatus !== null) {
                $dataQuery->where('m.active_status', (int) $selStatus);
            }

            $recordsTotal    = $dataQuery->count('m.id');
            $recordsFiltered = $recordsTotal;

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            if (!empty(request('order'))) {

                $columns = [
                    2 => 'm.name',
                    3 => 'm.code',
                    4 => 'm.sequence_no',
                    5 => 'm.created_at',
                    6 => 'm.active_status'
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
