<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\blogs\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    public function index()
    {
        return view('admin.blogs.blogCategory');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';

            $dataQuery = DB::table('odbusdev.blog_categories as bc')
                ->select(
                    'bc.id as blog_cat_id',
                    'bc.category_name',
                    'bc.slug',
                    'bc.description',
                    'bc.icon',
                    'bc.alt_text',
                    'bc.banner_image',
                    'bc.sort_order',
                    'bc.created_at',
                    'bc.created_by',
                    'bc.updated_at',
                    'bc.updated_by',
                    'bc.active_status',
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = bc.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = bc.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('bc.category_name', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('bc.active_status', $selStatus);
            }

            $count = $dataQuery->count('bc.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'bc.category_name', 3 => 'bc.slug', 4 => 'bc.created_at', 5 => 'bc.created_by', 6 => 'bc.active_status'];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'bc.category_name';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'bc.category_name';
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
                    $val->enc_blog_cat_id = Crypt::encryptString($val->blog_cat_id);
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in BlogCategoryController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'BlogCategoryController',
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

            $config = config('blog.category_banner');

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;


            if ($id > 0) {

                $redirectPage = "admin/blog-category/edit/" . $encId;
                $data['strPage']    = $method = 'Edit';
                $data['strSubmit']  = 'Update';
                $data['strReset']   = 'Cancel';

                $dataResQry = BlogCategory::select('id', 'category_name', 'slug', 'description', 'icon', 'alt_text', 'banner_image', 'meta_title', 'meta_description', 'meta_keywords', 'og_image', 'canonical_url');

                $dataResQry = $dataResQry->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("admin/blog-category");
                }
                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/blog-category";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $validator = Validator::make(request()->all(), [
                    'categoryName' => 'required|max:50',
                    'categoryAlias' => 'required|max:50',
                    'banner_image' => [
                        'nullable',
                        'max:' . $config['max_size'], // in KB
                    ]

                ], [
                    'categoryName.required' => 'Category name cannot be left blank.',
                    'categoryName.exists' => 'Category Name already exist.',
                    'categoryName.max' => 'Category Name exceed max characters.',

                    'categoryAlias.required' => 'Category alias cannot be left blank.',
                    'categoryAlias.exists' => 'Category alias already exist.',
                    'categoryAlias.max' => 'Category alias exceed max characters.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {

                    DB::beginTransaction();

                    $categoryName = htmlEncode(Purifier::clean(request('categoryName')));
                    $categoryAlias = htmlEncode(Purifier::clean(request('categoryAlias')));
                    $altText = (request('categoryAlias') !== null) ? htmlEncode(Purifier::clean(request('categoryAlias'))) : null;
                    $icon = (request('icon') !== null) ? htmlEncode(Purifier::clean(request('icon'))) : null;
                    $description = (request('description') !== null) ? htmlEncode(Purifier::clean(request('description'))) : null;

                    $meta_title = htmlEncode(request('meta_title'));
                    $canonical_url = htmlEncode(request('canonical_url'));
                    $meta_description = htmlEncode(request('meta_description'));
                    $meta_keywords = htmlEncode(request('meta_keywords'));

                    $duplicate = BlogCategory::select('id')->where(['category_name' => $categoryName]);

                    if ($id != 0) {
                        $duplicate->where('id', '!=', $id);
                    }

                    if ($duplicate->exists()) {
                        return back()->with([
                            'level' => 'danger',
                            'message' => 'Category already exist'
                        ])->withInput();
                    } else {
                        $obj = ($id != 0) ? BlogCategory::find($id) : new BlogCategory();
                        $obj->category_name = $categoryName;
                        $obj->slug = $categoryAlias;
                        $obj->icon = $icon;
                        $obj->description = $description;
                        $obj->alt_text = $altText;
                        $obj->meta_title = $meta_title;
                        $obj->canonical_url = $canonical_url;
                        $obj->meta_description = $meta_description;
                        $obj->meta_keywords = $meta_keywords;
                        $obj->created_by = 1;
                        $obj->active_status = 1;

                        $path = $config['path'];

                        if (!Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->makeDirectory($path);
                        }

                        if (request()->hasFile('banner_image')) {

                            // delete old image
                            if (!empty($data['row']->banner_image) && Storage::disk('public')->exists($path . '/' . $data['row']->banner_image)) {
                                Storage::disk('public')->delete($path . '/' . $data['row']->banner_image);
                            }

                            // upload new image
                            $file = request()->file('banner_image');
                            $filename = 'banner-' . time() . rand() . '.' . $file->getClientOriginalExtension();

                            $file->storeAs($path, $filename, 'public');

                            $obj->banner_image = $filename;
                        }

                        if (request()->hasFile('og_image')) {

                            // delete old image
                            if (!empty($data['row']->og_image) && Storage::disk('public')->exists($path . '/' . $data['row']->og_image)) {
                                Storage::disk('public')->delete($path . '/' . $data['row']->og_image);
                            }

                            // upload new image
                            $file3 = request()->file('og_image');
                            $og_image = 'og-' . time() . rand() . '.' . $file3->getClientOriginalExtension();

                            $file3->storeAs($path, $og_image, 'public');

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
                'Controller' => 'BlogCategoryController',
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
        return view('admin.blogs.addBlogCategory', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
