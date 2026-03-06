<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\blogs\BlogCategory;
use App\Models\blogs\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
            $apiApp = (request('apiApp') !== null && request('apiApp') !== '') ? (int)request('apiApp') : '';
            $selCity = (request('selCity') !== null && request('selCity') !== '') ? (int)request('selCity') : '';

            $dataQuery = DB::table('city_api_ids as cp')
                ->select(
                    'cp.id as city_api_ids_id',
                    'cp.city_id',
                    'cp.api_app_id',
                    'cp.api_city_ids',
                    'cp.created_at',
                    'cp.created_by',
                    'cp.updated_at',
                    'cp.updated_by',
                    'cp.active_status',
                    DB::raw('(SELECT city_name FROM mst_cities WHERE id = cp.city_id LIMIT 1) as city_name'),
                    DB::raw('(SELECT app_name FROM api_apps WHERE id = cp.api_app_id LIMIT 1) as app_name'),
                    DB::raw('(SELECT name FROM users WHERE id = cp.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = cp.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('cp.api_city_ids', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($apiApp) && $apiApp != '') {
                $dataQuery->where('cp.api_app_id', $apiApp);
            }

            if (isset($selCity) && $selCity != '') {
                $dataQuery->where('cp.city_id', $selCity);
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('cp.active_status', $selStatus);
            }

            $count = $dataQuery->count('cp.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'cp.api_app_id', 3 => 'cp.api_city_ids', 4 => 'cp.created_at', 5 => 'cp.created_by', 6 => 'cp.active_status'];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'cp.api_city_ids';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'cp.api_city_ids';
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
                    $val->enc_city_api_ids_id = Crypt::encryptString($val->city_api_ids_id);
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

                $dataResQry = Blog::select('id', 'category_id','title','slug','short_description','content','thumb_alt_text','thumb_image','feature_alt_text','featured_image','author_name','is_featured','active_status','published_at','meta_title','meta_description','meta_keywords','og_image','canonical_url','view_count');

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

                    $duplicate = BlogCategory::select('id')->where(['title' => $title]);

                    if ($id != 0) {
                        $duplicate->where('id', '!=', $id);
                    }

                    if ($duplicate->exists()) {
                        return back()->with([
                            'level' => 'danger',
                            'message' => 'Blog already exist'
                        ])->withInput();
                    } else {
                        $obj = ($id != 0) ? BlogCategory::find($id) : new BlogCategory();
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
                        $obj->created_by = 1;
                        $obj->active_status = 1;

                        if ($id != 0) {
                            $obj->updated_by = 1;
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
