<?php

namespace App\Http\Controllers\Master;

use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use App\Models\Master\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Validator;

class FaqCategoryController extends Controller
{
    public function faqCategory()
    {
        return view('master.faqcategory');
    }

    public function dataTableView()
    {
        $recordsTotal    = 0;
        $recordsFiltered = 0;
        $data            = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '')
                ? (int) request('selStatus')
                : '';

            $dataQuery = DB::table('faq_category as fc')
                ->select(
                    'fc.id as faq_cat_id',
                    'fc.category_name',
                    'fc.sequence_no',
                    'fc.active_status',
                    'fc.created_at',
                    'fc.updated_at',
                    'fc.created_by',
                    'fc.updated_by',
                    DB::raw('(SELECT name FROM users WHERE id = fc.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = fc.updated_by LIMIT 1) as updated_by_name')
                );

            if (!empty($txtSearch)) {
                $dataQuery->where('fc.category_name', 'like', "%{$txtSearch}%");
            }

            if ($selStatus !== '') {
                $dataQuery->where('fc.active_status', $selStatus);
            }

            $recordsTotal    = $dataQuery->count('fc.id');
            $recordsFiltered = $recordsTotal;

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            if (!empty(request('order'))) {

                $columns = [
                    2 => 'fc.category_name',
                    3 => 'fc.sequence_no',
                    4 => 'fc.created_at',
                    5 => 'fc.active_status'
                ];

                $order    = request('order');
                $orderCol = $columns[$order[0]['column']] ?? 'fc.sequence_no';
                $orderDir = $order[0]['dir'] ?? 'asc';
            } else {
                $orderCol = 'fc.sequence_no';
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
                $row->enc_faq_cat_id = Crypt::encryptString($row->faq_cat_id);
            }

            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error("Exception in FaqCategoryController@dataTableView", [
                'error' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $recordsTotal    = 0;
            $recordsFiltered = 0;
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
        $data['strPage']   = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset']  = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = "admin/faqcategory/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = FaqCategory::select(
                    'id',
                    'category_name',
                    'sequence_no',
                    'active_status'
                )->where('id', $id)->first();

                if (!$row) {
                    return redirect('admin/faqcategory');
                }

                $data['row'] = $row;

            } else {
                $id = 0;
                $redirectPage = "admin/faqcategory";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'category_name' => 'bail|required|max:100',
                    'sequence_no'   => 'nullable|integer'
                ], [
                    'category_name.required' => 'FAQ Category Name cannot be left blank.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $categoryName = htmlEncode(
                    trim(Purifier::clean(request('category_name')))
                );

                $duplicate = FaqCategory::where('category_name', $categoryName);

                if ($id > 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    DB::rollBack();
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'FAQ Category already exists.'
                    ])->withInput();
                }

                if ($id == 0 && empty(request('sequence_no'))) {
                    $sequence = (FaqCategory::max('sequence_no') ?? 0) + 1;
                } else {
                    $sequence = request('sequence_no') ?? 1;
                }

                if ($id > 0) {

                    $oldData = FaqCategory::find($id);

                    $newData = [
                        'category_name' => $categoryName,
                        'sequence_no'   => $sequence,
                        'active_status' => 1
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
                            'mst_faq_category',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    $oldData->category_name = $categoryName;
                    $oldData->sequence_no   = $sequence;
                    $oldData->active_status = 1;
                    $oldData->updated_by    = 1;
                    $oldData->save();

                } else {

                    $row = [
                        'category_name' => $categoryName,
                        'sequence_no'   => $sequence,
                        'active_status' => 1,
                        'created_by'    => 1,
                        'created_at'    => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_faq_category',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    $obj = new FaqCategory();
                    $obj->fill($row);
                    $obj->save();
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'FAQ Category ' . ($id > 0 ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error in FaqCategoryController@add", [
                'method' => $method,
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addFaqCategory', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
