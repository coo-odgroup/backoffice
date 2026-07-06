<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Models\blogs\BlogTagMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Validator;


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
                ->join('odbusdev.blogs as b', 'b.id', '=', 'btm.blog_id')
                ->join('odbusdev.blog_tags as bt', 'bt.id', '=', 'btm.tag_id')
                ->select(
                    DB::raw('MIN(btm.id) as tag_map_id'),
                    'btm.blog_id',
                    'b.title',
                    DB::raw("GROUP_CONCAT(bt.tag_name ORDER BY bt.tag_name SEPARATOR '||') as tag_names"),
                    DB::raw('MAX(btm.created_at) as created_at'),
                    DB::raw('MAX(btm.created_by) as created_by'),
                    DB::raw('MAX(btm.updated_at) as updated_at'),
                    DB::raw('MAX(btm.updated_by) as updated_by'),
                    DB::raw('(SELECT u.name FROM odbusmaster.users u WHERE u.id = MAX(btm.created_by)) as created_by_name'),
                    DB::raw('(SELECT u.name FROM odbusmaster.users u WHERE u.id = MAX(btm.updated_by)) as updated_by_name')
                )
                ->groupBy('btm.blog_id', 'b.title');

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('b.title', 'like', "%{$txtSearch}%")
                        ->orWhere('bt.tag_name', 'like', "%{$txtSearch}%");
                });
            }

            // count after grouping
            $countQuery = clone $dataQuery;
            $count = $countQuery->get()->count();

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [
                    2 => 'b.title',
                    3 => DB::raw("GROUP_CONCAT(bt.tag_name ORDER BY bt.tag_name SEPARATOR '||')"),
                    4 => DB::raw('MAX(btm.updated_at)'),
                ];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'b.title';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'b.title';
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
                    $val->created_date = !empty($val->created_at)
                        ? date('d-M-Y H:i:s', strtotime($val->created_at))
                        : null;

                    $val->updated_date = !empty($val->updated_at)
                        ? date('d-M-Y H:i:s', strtotime($val->updated_at))
                        : null;

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

                $redirectPage = route('blog-tag-map.edit', $encId);
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = BlogTagMap::select('id', 'blog_id')
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect()->route('blog-tag-map.index');
                }

                $data['row'] = $dataResQry;

                // selected blog
                $data['selectedBlog'] = $dataResQry->blog_id;

                // fetch ALL mapped tags for this blog
                $data['selectedTags'] = BlogTagMap::where('blog_id', $dataResQry->blog_id)
                    ->pluck('tag_id')
                    ->toArray();
            } else {
                $id = 0;
                $redirectPage = route('blog-tag-map.index');
                $data['selectedBlog'] = 0;
                $data['selectedTags'] = [];
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'blog_id'   => 'required|integer',
                    'tag_id'    => 'required|array|min:1',
                    'tag_id.*'  => 'required|integer',
                ], [
                    'blog_id.required' => 'Please select a blog.',
                    'tag_id.required'  => 'Please select at least one blog tag.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $blogId = request('blog_id');
                $tagIds = request('tag_id', []);

                if ($id != 0) {

                    $editRow = BlogTagMap::find($id);

                    if (!$editRow) {
                        throw new \Exception('Blog tag map record not found.');
                    }

                    $oldBlogId = $editRow->blog_id;
                    $oldTagIds = BlogTagMap::where('blog_id', $oldBlogId)
                        ->pluck('tag_id')
                        ->toArray();

                    BlogTagMap::where('blog_id', $oldBlogId)->delete();
                    foreach ($tagIds as $tagId) {
                        BlogTagMap::create([
                            'blog_id'    => $blogId,
                            'tag_id'     => $tagId,
                            'created_by' => 1,
                            'created_at' => now(),
                            'updated_by' => 1,
                            'updated_at' => now(),
                        ]);
                    }

                    app(CommonController::class)->auditLog(
                        'mst_blog_tag_map',
                        $id,
                        'UPDATE',
                        [
                            'blog_id' => $oldBlogId,
                            'tag_ids' => implode(',', $oldTagIds)
                        ],
                        [
                            'blog_id' => $blogId,
                            'tag_ids' => implode(',', $tagIds)
                        ]
                    );
                } else {

                    // ADD MODE = one blog + multiple tags
                    foreach ($tagIds as $tagId) {

                        $exists = BlogTagMap::where('blog_id', $blogId)
                            ->where('tag_id', $tagId)
                            ->exists();

                        if (!$exists) {
                            $row = [
                                'blog_id'    => $blogId,
                                'tag_id'     => $tagId,
                                'created_by' => 1,
                                'created_at' => now(),
                            ];

                            app(CommonController::class)->auditLog(
                                'mst_blog_tag_map',
                                null,
                                'INSERT',
                                [],
                                $row
                            );

                            BlogTagMap::create($row);
                        }
                    }
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash('message', 'Blog Tag Map ' . ($id ? 'updated' : 'created') . ' successfully.');

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'BlogTagMapController',
                'Method' => $method,
                'Error' => $t->getMessage()
            ]);

            return back()->with([
                'level' => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('admin.blogs.addBlogTagMap', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
