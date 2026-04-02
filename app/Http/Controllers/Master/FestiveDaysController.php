<?php

namespace App\Http\Controllers\Master;

use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use App\Models\Master\FestiveDays;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\DB;

class FestiveDaysController extends Controller
{

    public function festiveDays()
    {
        return view('master.festiveDays');
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

                $redirectPage = "admin/festiveDays/edit/" . $encId;
                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = FestiveDays::select('id', 'festive_date', 'short_desc', 'year')
                    ->where('id', $id)
                    ->first();

                if (!$row) {
                    return redirect('festiveDays');
                }

                $data['row'] = $row;
            } else {
                $id = 0;
                $redirectPage = "admin/festiveDays";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'festive_name' => 'required|max:100',
                    'festive_date' => 'required|date',
                    'year'         => 'required|numeric'
                ], [
                    'festive_name.required' => 'Festive Name cannot be blank.',
                    'festive_date.required' => 'Festive Date is required.',
                    'year.required'         => 'Year is required.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $festiveName = htmlEncode(trim(Purifier::clean(request('festive_name'))));
                $festiveDate = request('festive_date');
                $year        = request('year');
                $description = htmlEncode(trim(Purifier::clean(request('short_desc'))));

                if ($id > 0) {

                    $oldData = FestiveDays::find($id);

                    $newData = [
                        'festive_date' => $festiveDate,
                        'short_desc'   => $description,
                        'year'         => $year,
                        'festive_name' => $festiveName,
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
                            'mst_festive_days',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    $oldData->festive_date  = $festiveDate;
                    $oldData->short_desc    = $description;
                    $oldData->year          = $year;
                    $oldData->festive_name = $festiveName;
                    $oldData->active_status = 1;
                    $oldData->updated_by    = 1;
                    $oldData->updated_at    = now();
                    $oldData->save();
                } else {

                    $row = [
                        'festive_name'  => $festiveName,
                        'festive_date'  => $festiveDate,
                        'short_desc'    => $description,
                        'year'          => $year,
                        'active_status' => 1,
                        'created_by'    => 1,
                        'created_at'    => now()
                    ];

                    $obj = new FestiveDays();
                    $obj->fill($row);
                    $obj->save();

                    $insertId = $obj->id;

                    app(CommonController::class)->auditLog(
                        'mst_festive_days',
                        $insertId,
                        'INSERT',
                        [],
                        $row
                    );
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Festive Day ' . ($id > 0 ? 'updated' : 'created') . ' successfully.'
                );

                return redirect()->route('festiveDays.index');
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error in FestiveDaysController@add", [
                'method' => $method,
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addFestiveDays', compact('data'));
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

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $selYear = request('selYear');

            $dataQuery = DB::table('mst_festive_days as r')
                ->select(
                    'r.id as festive_day_id',
                    'r.festive_name', // ✅ IMPORTANT
                    'r.festive_date',
                    'r.short_desc',
                    'r.year',
                    'r.active_status',
                    'r.created_at',
                    'r.updated_at',
                    'r.created_by',
                    'r.updated_by',
                    DB::raw('(SELECT name FROM users WHERE id = r.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = r.updated_by LIMIT 1) as updated_by_name')
                );

            // 🔍 SEARCH
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('r.festive_name', 'like', "%{$txtSearch}%")
                        ->orWhere('r.short_desc', 'like', "%{$txtSearch}%")
                        ->orWhere('r.year', 'like', "%{$txtSearch}%");
                });
            }

            // 🔘 STATUS FILTER
            if ($selStatus !== '' && $selStatus !== null) {
                $dataQuery->where('r.active_status', $selStatus);
            }
            $selYear = request('selYear');

            if (!empty($selYear)) {
                $dataQuery->where('r.year', $selYear);
            }

            $recordsTotal = $dataQuery->count('r.id');
            $recordsFiltered = $recordsTotal;

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            // 🔽 ORDER
            if (!empty(request('order'))) {

                $columns = [
                    2 => 'r.festive_name',
                    3 => 'r.festive_date',
                    4 => 'r.year',
                    5 => 'r.short_desc',
                    6 => 'r.created_at',
                    7 => 'r.active_status'
                ];

                $order      = request('order');
                $orderCol   = $columns[$order[0]['column']] ?? 'r.id';
                $orderDir   = $order[0]['dir'] ?? 'desc';
            } else {
                $orderCol = 'r.id';
                $orderDir = 'desc';
            }

            $dataQuery->orderBy($orderCol, $orderDir);

            // 📄 PAGINATION
            if ($length == -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery->offset($start)->limit($length)->get();
            }

            // 🔄 FORMAT DATA
            foreach ($arrRes as $row) {

                // ✅ Match blade
                $row->festival_name = $row->festive_name;

                // ✅ Formatted date
                $row->festiveDays = date('d-M-Y', strtotime($row->festive_date));

                $row->created_date = date('d-M-Y H:i:s', strtotime($row->created_at));
                $row->updated_date = $row->updated_at
                    ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                    : null;

                $row->is_active = ($row->active_status == 1) ? 'Active' : 'Inactive';

                $row->enc_festiveDays_id = Crypt::encryptString($row->festive_day_id);
            }

            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error("Exception in FestiveDaysController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $recordsTotal = 0;
            $recordsFiltered = 0;
            $data = [];
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
}
