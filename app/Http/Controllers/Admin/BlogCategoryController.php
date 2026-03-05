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
            $user_role = (request('user_role') !== null && request('user_role') !== '') ? (int)request('user_role') : '';

            $dataQuery = DB::table('users as u')
                ->select(
                    'u.id as users_id',
                    'u.unique_id',
                    'u.name as user_name',
                    'u.name as created_by_name',
                    'u.name as updated_by_name',
                    'u.organization_name',
                    'u.primary_email',
                    'u.primary_contact',
                    'u.location',
                    'u.created_at',
                    'u.created_by',
                    'u.updated_at',
                    'u.updated_by',
                    'u.active_status',
                    DB::raw('(SELECT name FROM mst_roles WHERE id = u.user_role LIMIT 1) as user_role')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('u.name', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($user_role) && $user_role != '') {
                $dataQuery->where('u.user_role', $user_role);
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('u.active_status', $selStatus);
            }

            $count = $dataQuery->count('u.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'u.name', 3 => 'u.organization_name', 4 => 'u.created_at', 5 => 'u.created_by', 6 => 'u.active_status'];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'u.name';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'u.name';
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
                    $val->enc_users_id = Crypt::encryptString($val->users_id);
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in UsersController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'UsersController',
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
        $data['edit_param'] = '';

        try {

            $redirectPage = "admin/blog-category";

            $config = config('blog.category_banner');

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;


            if ($id > 0) {

                $redirectPage = "admin/blog-category/edit/" . $encId;
                $data['strPage']    = $method = 'Edit';
                $data['strSubmit']  = 'Update';
                $data['strReset']   = 'Cancel';

                $dataResQry = BlogCategory::select('id', 'category_name', 'slug', 'description', 'icon','alt_text','banner_image');

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
                                     //   'image',
                                        'max:' . $config['max_size'], // in KB
                                      //  'dimensions:width=' . $config['width'] . ',height=' . $config['height'],
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
                    $categoryAlias = htmlEncode(Purifier::clean(request('categoryAlias')));
                    $altText = (request('categoryAlias') !== null)?htmlEncode(Purifier::clean(request('categoryAlias'))):null;
                    $icon = (request('icon') !== null)?htmlEncode(Purifier::clean(request('icon'))):null;
                    $description = (request('description') !== null)?htmlEncode(Purifier::clean(request('description'))):null;
                   

                    $duplicate = BlogCategory::select('id')
                                ->where(['category_name' => $categoryName]);

                    if ($duplicate->exists()) {
                        return back()->with([
                            'level' => 'danger',
                            'message' => 'Category Name already exist'
                        ])->withInput();
                    } else {
                     
                        $path = $config['path'];

                        if (!Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->makeDirectory($path);
                        }

                        if (request()->hasFile('banner_image')) {
                            $file = request()->file('banner_image');
                            $filename = Str::slug(request()->categoryName) . '-' . time() . '.' . $file->getClientOriginalExtension();
                           $file->storeAs($path, $filename, 'public');
                        }

                       

                        $obj = new BlogCategory();
                        $obj->category_name = $categoryName;
                        $obj->slug = $categoryAlias;
                        $obj->icon = $icon;
                        $obj->description = $description;
                        $obj->alt_text = $altText;
                        $obj->banner_image = $filename;
                        $obj->created_by = 1;
                        $obj->active_status = 1;

                        $obj->save();
                    
                        session()->flash('level', 'success');
                        session()->flash('message', 'Blog Category ' . (($id != 0) ?
                            'updated' : 'created') . ' successfully.');
                        
                    }

                    DB::commit();
                    return redirect($redirectPage);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'UsersController',
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

    public function edit($edit_param, $encId)
    {
        return $this->update($edit_param, $encId);
    }
}
