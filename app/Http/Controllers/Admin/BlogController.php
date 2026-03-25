<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\blogs\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CommonController;

class BlogController extends Controller
{
    public function index()
    {
        return view('admin.blogs.blogsList');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';

            $dataQuery = DB::table('odbusdev.blogs as b')
                ->select(
                    'b.id as blog_id',
                    'b.title',
                    'b.slug',
                    'b.short_description',
                    'b.content',
                    'b.thumb_alt_text',
                    'b.thumb_image',
                    'b.feature_alt_text',
                    'b.featured_image',
                    'b.author_name',
                    'b.is_featured',
                    'b.published_at',
                    'b.view_count',
                    'b.created_at',
                    'b.created_by',
                    'b.updated_at',
                    'b.updated_by',
                    'b.active_status',
                    DB::raw('(SELECT category_name FROM odbusdev.blog_categories WHERE id = b.category_id LIMIT 1) as category_name'),
                    DB::raw('(SELECT name FROM users WHERE id = b.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = b.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('b.title', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('b.active_status', $selStatus);
            }

            $count = $dataQuery->count('b.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'b.title', 3 => 'b.author_name', 4 => 'b.created_at', 5 => 'b.created_by', 6 => 'b.active_status'];

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
                    $val->created_date = date('d-M-Y H:i:s', strtotime($val->created_at));
                    $val->updated_date = ($val->updated_at != null) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : null;
                    $val->is_active = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_blog_id = Crypt::encryptString($val->blog_id);
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in BlogController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'BlogController',
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
        $config = config('blog.blog');

        $data = [];
        $data['strPage'] = $method = 'Add';
        $data['strSubmit'] = 'Submit';
        $data['strReset'] = 'Reset';

        try {

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = "admin/blogs/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = Blog::where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("blogs");
                }

                $data['row'] = $dataResQry;

            } else {
                $id = 0;
                $redirectPage = "admin/blogs";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'title' => 'bail|required',
                    'slug' => 'bail|required',
                    'short_description' => 'bail|required',
                    'category_id' => 'bail|required'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $title = htmlEncode(request('title'));
                $slug = htmlEncode(request('slug'));
                $short_description = htmlEncode(request('short_description'));
                $content = htmlEncode(request('content'));
                $category_id = request('category_id');
                $is_featured = request('is_featured');

                $thumb_alt_text = htmlEncode(request('thumb_alt_text'));
                $feature_alt_text = htmlEncode(request('feature_alt_text'));

                $meta_title = htmlEncode(request('meta_title'));
                $canonical_url = htmlEncode(request('canonical_url'));
                $meta_description = htmlEncode(request('meta_description'));
                $meta_keywords = htmlEncode(request('meta_keywords'));

                $duplicate = Blog::where('title', $title);
                if ($id != 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    return back()->with([
                        'level' => 'danger',
                        'message' => 'Blog already exist'
                    ])->withInput();
                }

                $path = $config['path'];

                if (!Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->makeDirectory($path);
                }

                $newThumb = null;
                $newFeature = null;
                $newOg = null;

                if (request()->hasFile('thumb_image')) {
                    if ($id && !empty($data['row']->thumb_image)) {
                        Storage::disk('public')->delete($path . '/' . $data['row']->thumb_image);
                    }

                    $file = request()->file('thumb_image');
                    $newThumb = 'thumb-' . time() . rand() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs($path, $newThumb, 'public');
                }

                if (request()->hasFile('featured_image')) {
                    if ($id && !empty($data['row']->featured_image)) {
                        Storage::disk('public')->delete($path . '/' . $data['row']->featured_image);
                    }

                    $file2 = request()->file('featured_image');
                    $newFeature = 'featured-' . time() . rand() . '.' . $file2->getClientOriginalExtension();
                    $file2->storeAs($path, $newFeature, 'public');
                }

                if (request()->hasFile('og_image')) {
                    if ($id && !empty($data['row']->og_image)) {
                        Storage::disk('public')->delete($path . '/' . $data['row']->og_image);
                    }

                    $file3 = request()->file('og_image');
                    $newOg = 'og-' . time() . rand() . '.' . $file3->getClientOriginalExtension();
                    $file3->storeAs($path, $newOg, 'public');
                }

                if ($id > 0) {

                    $oldData = Blog::find($id);

                    $newData = [
                        'title' => $title,
                        'slug' => $slug,
                        'short_description' => $short_description,
                        'content' => $content,
                        'category_id' => $category_id,
                        'is_featured' => $is_featured,
                        'thumb_alt_text' => $thumb_alt_text,
                        'feature_alt_text' => $feature_alt_text,
                        'meta_title' => $meta_title,
                        'canonical_url' => $canonical_url,
                        'meta_description' => $meta_description,
                        'meta_keywords' => $meta_keywords,
                        'thumb_image' => $newThumb ?: $oldData->thumb_image,
                        'featured_image' => $newFeature ?: $oldData->featured_image,
                        'og_image' => $newOg ?: $oldData->og_image,
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
                            'mst_blog',
                            $id,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }

                    $oldData->fill($newData);
                    $oldData->updated_by = 1;
                    $oldData->save();

                } else {

                    $row = [
                        'title' => $title,
                        'slug' => $slug,
                        'short_description' => $short_description,
                        'content' => $content,
                        'category_id' => $category_id,
                        'is_featured' => $is_featured,
                        'thumb_alt_text' => $thumb_alt_text,
                        'feature_alt_text' => $feature_alt_text,
                        'meta_title' => $meta_title,
                        'canonical_url' => $canonical_url,
                        'meta_description' => $meta_description,
                        'meta_keywords' => $meta_keywords,
                        'thumb_image' => $newThumb,
                        'featured_image' => $newFeature,
                        'og_image' => $newOg,
                        'created_by' => 1,
                        'active_status' => 1,
                        'published_at' => now(),
                        'created_at' => now()
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_blog',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    Blog::create($row);
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash('message', 'Blog ' . ($id ? 'updated' : 'created') . ' successfully.');

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'BlogController',
                'Method' => $method,
                'Error' => $t->getMessage()
            ]);

            return back()->with([
                'level' => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('admin.blogs.addBlogs', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
