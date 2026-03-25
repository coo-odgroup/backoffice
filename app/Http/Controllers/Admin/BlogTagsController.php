<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\blogs\BlogRoutes;
use App\Models\blogs\BlogTags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;


class BlogTagsController extends Controller
{
    public function index()
    {
        return view('admin.blogs.blogTags');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));

            $dataQuery = DB::table('odbusdev.blog_tags as bt')
                ->select(
                    'bt.id as blog_tags_id',
                    'bt.tag_name',
                    'bt.slug',
                    'bt.created_at',
                    'bt.created_by',
                    'bt.updated_at',
                    'bt.updated_by',
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = bt.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = bt.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('bt.tag_name', 'like', "%{$txtSearch}%");
                });
            }

            $count = $dataQuery->count('bt.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'bt.tag_name', 3 => 'bt.created_at', 4 => 'bt.created_by'];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'bt.tag_name';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'bt.tag_name';
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
                    $val->enc_blog_tags_id = Crypt::encryptString($val->blog_tags_id);
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in BlogTagsController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'BlogTagsController',
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

                $redirectPage = "admin/blog-tags/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = BlogTags::select('id', 'tag_name', 'slug')
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect("blog-tags");
                }

                $data['row'] = $dataResQry;

            } else {
                $id = 0;
                $redirectPage = "admin/blog-tags";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'tag_name' => 'bail|required',
                    'slug'     => 'bail|required'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $tag_name = htmlEncode(request('tag_name'));
                $slug     = htmlEncode(request('slug'));

                if ($id != 0) {

                    $oldData = BlogTags::find($id);

                    $newData = [
                        'tag_name' => $tag_name,
                        'slug'     => $slug,
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
                            'mst_blog_tags',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    $oldData->fill($newData);
                    $oldData->updated_by = 1;
                    $oldData->updated_at = now();
                    $oldData->save();

                } else {

                    $row = [
                        'tag_name'   => $tag_name,
                        'slug'       => $slug,
                        'created_by' => 1,
                        'created_at' => now(),
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_blog_tags',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    BlogTags::create($row);
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash('message', 'Blog Tags ' . ($id ? 'updated' : 'created') . ' successfully.');

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'BlogTagsController',
                'Method' => $method,
                'Error' => $t->getMessage()
            ]);

            return back()->with([
                'level' => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('admin.blogs.addBlogTags', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
