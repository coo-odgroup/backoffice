<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\ReviewCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;
use Mews\Purifier\Facades\Purifier;

use function PHPUnit\Framework\returnValue;

class ReviewCategoryController extends Controller
{
    public function reviewCategory(){
        return view('Master.reviewCategory');
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

            $dataQuery = DB::table('mst_review_categories as rc')
                ->select(
                    'rc.id as review_cat_id',
                    'rc.name',
                    'rc.sequence_no',
                    'rc.active_status',
                    'rc.created_at',
                    'rc.updated_at',
                    'rc.created_by',
                    'rc.updated_by',
                    DB::raw('(SELECT name FROM users WHERE id = rc.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = rc.updated_by LIMIT 1) as updated_by_name')
                );

            if (!empty($txtSearch)) {
                $dataQuery->where('rc.name', 'like', "%{$txtSearch}%");
            }

            if ($selStatus !== '') {
                $dataQuery->where('rc.active_status', $selStatus);
            }

            $recordsTotal    = $dataQuery->count('rc.id');
            $recordsFiltered = $recordsTotal;

            $start  = (int) request()->input('start', 0);
            $length = (int) request()->input('length', 10);

            if (!empty(request('order'))) {

                $columns = [
                    2 => 'rc.name',
                    3 => 'rc.sequence_no',
                    4 => 'rc.created_at',
                    5 => 'rc.active_status'
                ];

                $order    = request('order');
                $orderCol = $columns[$order[0]['column']] ?? 'rc.sequence_no';
                $orderDir = $order[0]['dir'] ?? 'asc';
            } else {
                $orderCol = 'rc.sequence_no';
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
                $row->enc_review_cat_id = Crypt::encryptString($row->review_cat_id);
            }

            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::error("Exception in ReviewCategoryController@dataTableView", [
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

                $redirectPage = "admin/reviewcategory/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = ReviewCategory::select(
                    'id',
                    'name',
                    'description',
                    'sequence_no',
                    'active_status'
                )->where('id', $id)->first();

                if (!$row) {
                    return redirect('admin/reviewcategory');
                }

                $data['row'] = $row;

            } else {
                $id = 0;
                $redirectPage = "admin/reviewcategory";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'name'        => 'bail|required|max:100',
                    'sequence_no' => 'nullable|integer',
                    'description' => 'bail|required'
                ], [
                    'name.required'        => 'Review Category Name cannot be left blank.',
                    'description.required' => 'Review description cannot be left blank.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $categoryName = htmlEncode(trim(Purifier::clean(request('name'))));
                $description  = htmlEncode(request('description'));

                $duplicate = ReviewCategory::where('name', $categoryName);

                if ($id > 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    DB::rollBack();
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'Review Category already exists.'
                    ])->withInput();
                }

                if ($id == 0 && empty(request('sequence_no'))) {
                    $sequence = (ReviewCategory::max('sequence_no') ?? 0) + 1;
                } else {
                    $sequence = request('sequence_no') ?? 1;
                }

                if ($id > 0) {

                    $oldData = ReviewCategory::find($id);

                    $newData = [
                        'name'          => $categoryName,
                        'description'   => $description,
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
                            'mst_review_category',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    $oldData->name          = $categoryName;
                    $oldData->description   = $description;
                    $oldData->sequence_no   = $sequence;
                    $oldData->active_status = 1;
                    $oldData->updated_by    = 1;
                    $oldData->save();

                } else {

                    $row = [
                        'name'          => $categoryName,
                        'description'   => $description,
                        'sequence_no'   => $sequence,
                        'active_status' => 1,
                        'created_by'    => 1,
                        'created_at'    => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_review_category',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    $obj = new ReviewCategory();
                    $obj->fill($row);
                    $obj->save();
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'Review Category ' . ($id > 0 ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error in ReviewCategoryController@add", [
                'method' => $method,
                'error'  => $t->getMessage()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addReviewCategory', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
