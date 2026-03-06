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
        $recordsTotal    = 0;
        $recordsFiltered = 0;
        $data            = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $categoryId = (request('faqCategory') !== null && request('faqCategory') !== '') ? (int)request('faqCategory') : '';

            $query = DB::table('faq as f')
                ->leftJoin('faq_category as c', 'c.id', '=', 'f.faq_category_id')
                ->select(
                    'f.id as faq_id',
                    'f.title',
                    'f.content',
                    'f.sequence_no',
                    'f.active_status',
                    'c.category_name',
                    'f.created_at',
                    'f.updated_at',
                    DB::raw('(SELECT name FROM users WHERE id = f.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = f.updated_by LIMIT 1) as updated_by_name')
                );


            if (!empty($txtSearch)) {
                $query->where(function ($q) use ($txtSearch) {
                    $q->where('f.title', 'like', "%{$txtSearch}%")
                        ->orWhere('c.category_name', 'like', "%{$txtSearch}%");
                });
            }


            if ($categoryId !== null && $categoryId !== '') {
                $query->where('f.faq_category_id', (int) $categoryId);
            }

            if ($selStatus !== null && $selStatus !== '') {
                $query->where('f.active_status', (int) $selStatus);
            }

            $countQuery = clone $query;

            $recordsFiltered = $countQuery->count('f.id');
            $recordsTotal = DB::table('faq')->count();

            $start  = (int) request('start', 0);
            $length = (int) request('length', 10);

            $orderColumn = 'f.sequence_no';
            $orderDir    = 'asc';

            if (!empty(request('order'))) {
                $columns = [
                    2 => 'f.title',
                    3 => 'c.category_name',
                    4 => 'f.content',
                    5 => 'f.sequence_no',
                    6 => '',
                    7 => 'f.active_status',
                ];

                $orderIndex  = request('order')[0]['column'];
                $orderDir    = request('order')[0]['dir'];
                $orderColumn = $columns[$orderIndex] ?? 'f.sequence_no';
            }

            $query->orderBy($orderColumn, $orderDir);

            if ($length != -1) {
                $query->offset($start)->limit($length);
            }

            $rows = $query->get();


            foreach ($rows as $row) {

                $row->content = htmlDecode($row->content);

                $row->created_date = $row->created_at
                    ? date('d-M-Y H:i:s', strtotime($row->created_at))
                    : '--';

                $row->updated_date = $row->updated_at
                    ? date('d-M-Y H:i:s', strtotime($row->updated_at))
                    : '--';

                $row->is_active   = $row->active_status == 1 ? 'Active' : 'Inactive';
                $row->enc_faq_id  = Crypt::encryptString($row->faq_id);
            }
            $data = $rows;
        } catch (\Throwable $e) {

            Log::error('FAQ dataTableView error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            $recordsTotal = 0;
            $recordsFiltered = 0;
            $data = [];
        }

        return response()->json([
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data
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
