<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\blogs\Blog;
use App\Models\blogs\BlogCategory;
use App\Models\blogs\BlogImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Storage;

class BlogImagesController extends Controller
{
    public function index()
    {
        return view('admin.blogs.blogImages');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));

            $dataQuery = Blog::with('images')->whereHas('images');

            // Search Filter
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('title', 'like', "%{$txtSearch}%");
                });
            }

            // Total Count
            $count = $dataQuery->count();

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [
                    2 => 'title',
                    3 => 'slug',
                    4 => 'created_at',
                    5 => 'created_by',
                    6 => 'active_status'
                ];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'title';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'title';
                $orderType = 'asc';
            }

            $dataQuery->orderBy($orderColumn, $orderType);

            // Pagination
            if ($length != -1) {
                $dataQuery->limit($length)->offset($start);
            }

            $arrRes = $dataQuery->get();

            // Format Data
            if ($arrRes->count() > 0) {
                foreach ($arrRes as $val) {

                    $val->created_date = date('d-M-Y H:i:s', strtotime($val->created_at));
                    $val->updated_date = ($val->updated_at != null)
                        ? date('d-M-Y H:i:s', strtotime($val->updated_at))
                        : null;

                    $val->is_active = ($val->active_status == 1) ? 'Active' : 'Inactive';

                    $val->enc_blog_id = Crypt::encryptString($val->id);
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in BlogImagesController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'BlogImagesController',
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

            $config = config('blog.blog');

            $id = (!empty($encId)) ? Crypt::decryptString($encId) : 0;

            if ($id > 0) {

                $redirectPage = "admin/blog-images/edit/" . $encId;
                $data['strPage']    = $method = 'Edit';
                $data['strSubmit']  = 'Update';
                $data['strReset']   = 'Cancel';

                $dataResQry = Blog::with('images');

                $dataResQry = $dataResQry->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("admin/blog-images");
                }
                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/blog-images";
            }

            if (request()->isMethod('post')) {

                request()->replace(request()->all());

                $rules = [
                    'blog_id' => 'required|integer',
                ];

                if ($id==0) {
                    $rules['image_name'] = [
                        'required',
                        'max:' . $config['max_size']
                    ];
                } else {
                    $rules['image_name'] = [
                        'nullable',
                        'max:' . $config['max_size']
                    ];
                }

                $validator = Validator::make(request()->all(), $rules, [
                    'blog_id.required' => 'Blog selection is required.',

                    'image_name.required' => 'Image cannot be left blank.',
                    'image_name.max' => 'Image size exceeds the allowed limit.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                } else {

                    DB::beginTransaction();

                    $blog_id = request('blog_id');
                    $alt_text = request('alt_text');
                    $sort_order = request('sort_order');
                    $path = $config['path'];

                    if (!Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->makeDirectory($path);
                    }

                    $image_id = request('image_id');

                    if (!empty($image_id)) {

                        foreach ($image_id as $k => $id) {

                            if (!empty($id)) {

                                BlogImages::where('id', $id)->update([
                                    'alt_text'   => $alt_text[$k] ?? null,
                                    'sort_order' => $sort_order[$k] ?? 0,
                                    'updated_at' => now(),
                                    'updated_by' => 1
                                ]);
                            }
                        }
                    }

                    $insertData = [];

                    if (request()->hasFile('image_name')) {

                        foreach (request()->file('image_name') as $k => $file) {

                            $filename = 'bloggallery-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

                            $file->storeAs($path, $filename, 'public');

                            $insertData[] = [
                                'blog_id'       => $blog_id,
                                'image_name'    => $filename,
                                'image_path'    => $path,
                                'alt_text'      => $alt_text[$k] ?? null,
                                'sort_order'    => $sort_order[$k] ?? 0,
                                'created_by'    => 1,
                                'created_at'    => now()
                            ];
                        }

                        if (!empty($insertData)) {
                            BlogImages::insert($insertData);
                        }
                    }

                    session()->flash('level', 'success');
                    session()->flash('message', 'Blog Images ' . (($id != 0) ? 'updated' : 'created') . ' successfully.');

                    DB::commit();
                    return redirect($redirectPage);
                }
            }
        } catch (\Throwable $t) {
            Log::error("Error", [
                'Controller' => 'BlogImagesController',
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
        return view('admin.blogs.addBlogImages', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function removeBlogImage(Request $request)
    {
        $table = $request->table;
        $id = $request->id;
        $path = $request->path;

        $data = DB::table($table)->where('id', $id)->first();

        if ($data) {

            if (!empty($data->image_name)) {
                $filePath = $path . '/' . $data->image_name;

                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            // Delete the row
            DB::table($table)->where('id', $id)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Image removed successfully'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Image not found'
        ]);
    }
}
