<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\blogs\BlogTagMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogTagMapController extends Controller
{
    public function index()
    {
        return view('admin.blogs.blogTagMap');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));

            $dataQuery = DB::table('odbusdev.blog_tag_map as btm')
                ->select(
                    'btm.id as tag_map_id',
                    'btm.blog_id',
                    'btm.tag_id',
                    'btm.created_at',
                    'btm.created_by',
                    'btm.updated_at',
                    'btm.updated_by',
                    DB::raw('(SELECT b.title FROM odbusdev.blogs b WHERE b.id = btm.blog_id) as title'),
                    DB::raw('(SELECT tag_name FROM odbusdev.blog_tags bt WHERE bt.id = btm.tag_id) as tag_name'),
                    DB::raw('(SELECT u.name FROM odbusmaster.users u WHERE u.id = btm.created_by) as created_by_name'),
                    DB::raw('(SELECT u.name FROM odbusmaster.users u WHERE u.id = btm.updated_by) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->whereRaw("(SELECT b.title FROM odbusdev.blogs b WHERE b.id = btm.blog_id) LIKE ?", ["%{$txtSearch}%"])
                        ->orWhereRaw("(SELECT bt.tag_name FROM odbusdev.blog_tags bt WHERE bt.id = btm.tag_id) LIKE ?", ["%{$txtSearch}%"]);
                });
            }

            $count = $dataQuery->count('btm.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [
                    2 => DB::raw('(SELECT b.title FROM odbusdev.blogs b WHERE b.id = btm.blog_id)'),
                    3 => DB::raw('(SELECT bt.tag_name FROM odbusdev.blog_tags bt WHERE bt.id = btm.tag_id)'),
                    4 => 'btm.created_at',
                    5 => 'btm.created_by'
                ];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'title';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'title';
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
                    $val->created_date = date('d-M-Y H:i:s', strtotime($val->created_at));
                    $val->updated_date = ($val->updated_at != null) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : null;
                    $val->enc_tag_map_id = Crypt::encryptString($val->tag_map_id);
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in BlogTagMapController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'BlogTagMapController',
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
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = "admin/blog-tag-map/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = BlogTagMap::select('id', 'blog_id', 'tag_id');

                $dataResQry = $dataResQry->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("blog-tag-map");
                }
                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/blog-tag-map";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'blog_id' => 'bail|required',
                    'tag_id' => 'bail|required'
                ], [
                    'blog_id.required' => 'Blog is required.',
                    'tag_id.required' => 'Tag is required.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();

                    $blog_id = request('blog_id');
                    $tag_id = request('tag_id');

                    $obj = ($id != 0) ? BlogTagMap::find($id) : new BlogTagMap();
                    $obj->blog_id = $blog_id;
                    $obj->tag_id = $tag_id;
                    $obj->created_at = now();
                    $obj->created_by = 1;

                    if ($id != 0) {
                        $obj->updated_by = 1;
                    }

                    $obj->save();

                    session()->flash('level', 'success');
                    session()->flash('message', 'Blog Tag Map ' . (($id != 0) ? 'updated' : 'created') . ' successfully.');

                    DB::commit();
                    return redirect($redirectPage);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'BlogTagMapController',
                'Method' => $method,
                'Error' => $t->getMessage()
            ]);

            DB::rollBack();

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            return back()->with([
                'level' => 'danger',
                'message' => $errorMsg
            ])->withInput();
        }
        return view('admin.blogs.addBlogTagMap', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
