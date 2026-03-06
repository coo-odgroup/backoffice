<?php

namespace App\Http\Controllers\Master;

use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use App\Models\Master\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    public function faq()
    {
        return view('master.faq');
    }

    public function dataTableView()
    {
        $recordsTotal     = 0;
        $recordsFiltered  = 0;
        $data             = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $faq_cat = (request('faq_cat') !== null && request('faq_cat') !== '') ? (int)request('faq_cat') : '';

            $dataQuery = DB::table('faq as f')
                ->select(
                    'f.id as faq_id',
                    'f.title',
                    'f.content',
                    'f.sequence_no',
                    'f.created_at',
                    'f.created_by',
                    'f.updated_at',
                    'f.updated_by',
                    'f.active_status',
                    DB::raw('(SELECT category_name FROM faq_category WHERE id = f.faq_category_id LIMIT 1) as category_name'),
                    DB::raw('(SELECT name FROM users WHERE id = f.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = f.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('f.title', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($faq_cat) && $faq_cat != 0) {
                $dataQuery->where('f.faq_category_id', $faq_cat);
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('f.active_status', $selStatus);
            }

            $count = $dataQuery->count('f.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'f.title', 3 => 'f.sequence_no', 4 => 'f.created_at', 5 => 'f.created_by', 6 => 'f.active_status'];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'f.title';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'f.title';
                $orderType = 'asc';
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

                    $val->content = htmlDecode($val->content);

                    $val->created_date = date('d-M-Y H:i:s', strtotime($val->created_at));
                    $val->updated_date = ($val->updated_at != null) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : null;
                    $val->is_active = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_faq_id = Crypt::encryptString($val->faq_id);
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in FaqController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'FaqController',
                'Method'     => 'dataTableView',
                'Error'      => $errorMsg
            ]);

            $recordsTotal = 0;
            $recordsFiltered = 0;
            $data = [];
        }

        return response()->json([
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
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

                $redirectPage = "admin/faq/edit/" . $encId;

                $data['strPage']   = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset']  = 'Cancel';

                $row = Faq::select(
                    'id',
                    'faq_category_id',
                    'title',
                    'content',
                    'active_status',
                    'sequence_no'
                )->where('id', $id)->first();

                if (!$row) {
                    return redirect()->route('faq.index');
                }

                $data['row'] = $row;
            } else {
                $redirectPage = "admin/faq";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'faqCategory' => 'required|integer|min:1',
                    'faq_name'    => 'required|string|max:150',
                    'content'     => 'nullable|string',
                ], [
                    'faqCategory.required' => 'FAQ Category cannot be left blank.',
                    'faq_name.required'    => 'FAQ Name cannot be left blank.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $categoryId = (int) Purifier::clean(request('faqCategory'));
                $title = htmlEncode(trim(Purifier::clean(request('faq_name'))));
                $content = htmlEncode(Purifier::clean(request('content')));
                $duplicate = Faq::where('title', $title)
                    ->where('faq_category_id', $categoryId);

                if ($id > 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    DB::rollBack();
                    return back()->with([
                        'level'   => 'danger',
                        'message' => 'FAQ already exists in this category.'
                    ])->withInput();
                }

                $faq = ($id > 0) ? Faq::find($id) : new Faq();

                $faq->faq_category_id = $categoryId;
                $faq->title           = $title;
                $faq->content         = $content;
                $faq->active_status   = 1;

                if ($id > 0) {
                    $faq->updated_by = 1;
                } else {
                    $faq->created_by = 1;
                }

                $faq->save();

                DB::commit();

                session()->flash('level', 'success');
                session()->flash(
                    'message',
                    'FAQ ' . ($id > 0 ? 'updated' : 'created') . ' successfully.'
                );

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error('Error in FaqController@add', [
                'method' => $method,
                'error'  => $t->getMessage(),
                'trace'  => $t->getTraceAsString()
            ]);

            return back()->with([
                'level'   => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('Master.addFaq', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
