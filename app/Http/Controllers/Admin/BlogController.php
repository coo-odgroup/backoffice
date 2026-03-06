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
use Illuminate\Support\Str;

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

                $dataResQry = Blog::select('id', 'category_id', 'title', 'slug', 'short_description', 'content', 'thumb_alt_text', 'thumb_image', 'feature_alt_text', 'featured_image', 'author_name', 'is_featured', 'active_status', 'published_at', 'meta_title', 'meta_description', 'meta_keywords', 'og_image', 'canonical_url', 'view_count');

                $dataResQry = $dataResQry->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("blogs");
                }
                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/blogs";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'title' => 'bail|required',
                    'slug' => 'bail|required',
                    'short_description' => 'bail|required',
                    'category_id' => 'bail|required'
                ], [
                    'title.required' => 'Title cannot be left blank.',
                    'slug.required' => 'Slug cannot be left blank.',
                    'short_description.required' => 'Short Description cannot be left blank.',
                    'category_id.required' => 'Category cannot be left blank.'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {
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

                    $duplicate = Blog::select('id')->where(['title' => $title]);

                    if ($id != 0) {
                        $duplicate->where('id', '!=', $id);
                    }

                    if ($duplicate->exists()) {
                        return back()->with([
                            'level' => 'danger',
                            'message' => 'Blog already exist'
                        ])->withInput();
                    } else {
                        $obj = ($id != 0) ? Blog::find($id) : new Blog();
                        $obj->title = $title;
                        $obj->slug = $slug;
                        $obj->short_description = $short_description;
                        $obj->content = $content;
                        $obj->category_id = $category_id;
                        $obj->is_featured = $is_featured;
                        $obj->thumb_alt_text = $thumb_alt_text;
                        $obj->feature_alt_text = $feature_alt_text;
                        $obj->meta_title = $meta_title;
                        $obj->canonical_url = $canonical_url;
                        $obj->meta_description = $meta_description;
                        $obj->meta_keywords = $meta_keywords;
                        $obj->published_at = now();
                        $obj->created_by = 1;
                        $obj->active_status = 1;

                        if ($id != 0) {
                            $obj->updated_by = 1;
                        }

                        $path = $config['path'];

                        if (!Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->makeDirectory($path);
                        }

                        if (request()->hasFile('thumb_image')) {

                            // delete old image
                            if (!empty($data['row']->thumb_image) && Storage::disk('public')->exists($path . '/' . $data['row']->thumb_image)) {
                                Storage::disk('public')->delete($path . '/' . $data['row']->thumb_image);
                            }

                            // upload new image
                            $file = request()->file('thumb_image');
                            $thumb_image = 'thumb-' . time() . rand() . '.' . $file->getClientOriginalExtension();

                            $file->storeAs($path, $thumb_image, 'public');

                            $obj->thumb_image = $thumb_image;
                        }

                        if (request()->hasFile('featured_image')) {

                            // delete old image
                            if (!empty($data['row']->featured_image) && Storage::disk('public')->exists($path . '/' . $data['row']->featured_image)) {
                                Storage::disk('public')->delete($path . '/' . $data['row']->featured_image);
                            }

                            // upload new image
                            $file2 = request()->file('featured_image');
                            $featured_image = 'featured-' . time() . rand() . '.' . $file2->getClientOriginalExtension();

                            $file2->storeAs($path, $featured_image, 'public');

                            $obj->featured_image = $featured_image;
                        }

                        if (request()->hasFile('og_image')) {

                            // delete old image
                            if (!empty($data['row']->og_image) && Storage::disk('public')->exists($path . '/' . $data['row']->og_image)) {
                                Storage::disk('public')->delete($path . '/' . $data['row']->og_image);
                            }

                            // upload new image
                            $file2 = request()->file('og_image');
                            $og_image = 'featured-' . time() . rand() . '.' . $file2->getClientOriginalExtension();

                            $file2->storeAs($path, $og_image, 'public');

                            $obj->og_image = $og_image;
                        }

                        $obj->save();

                        session()->flash('level', 'success');
                        session()->flash('message', 'Blog Category ' . (($id != 0) ? 'updated' : 'created') . ' successfully.');
                    }

                    DB::commit();
                    return redirect($redirectPage);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'BlogController',
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
        return view('admin.blogs.addBlogs', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
