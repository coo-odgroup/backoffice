<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Models\blogs\BlogAuthor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CommonController;

class BlogAuthorController extends Controller
{
    public function index()
    {
        return view('admin.blogs.blogAuthor');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));
            $selStatus = (request('selStatus') !== null && request('selStatus') !== '') ? (int)request('selStatus') : '';
            $author = htmlEncode(request('author'));
            $authorAlias = htmlEncode(request('authorAlias'));

            $dataQuery = DB::table('odbusdev.blog_authors as ba')
                ->select(
                    'ba.id as blog_author_id',
                    'ba.author_name',
                    'ba.author_slug',
                    'ba.canonical_url',
                    'ba.about_author',
                    'ba.created_at',
                    'ba.created_by',
                    'ba.updated_at',
                    'ba.updated_by',
                    'ba.active_status',
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = ba.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = ba.updated_by LIMIT 1) as updated_by_name')
                );

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('ba.author_name', 'like', "%{$txtSearch}%")
                        ->orWhere('ba.author_slug', 'like', "%{$txtSearch}%");
                });
            }

            if (isset($selStatus) && $selStatus != '') {
                $dataQuery->where('ba.active_status', $selStatus);
            }
            
            // Author Filter
            if (!empty($author)) {
                $dataQuery->where('ba.author_name', 'like', "%{$author}%");
            }

            // Alias Filter
            if (!empty($authorAlias)) {
                $dataQuery->where('ba.author_slug', 'like', "%{$authorAlias}%");
            }

            $count = $dataQuery->count('ba.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [
                    2 => 'ba.author_name',
                    3 => 'ba.author_slug',
                    4 => 'ba.canonical_url',
                    5 => 'ba.about_author',
                    6 => 'ba.updated_at',
                    7 => 'ba.active_status',
                ];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'ba.author_name';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'ba.author_name';
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
                    $val->enc_blog_author_id  = Crypt::encryptString($val->blog_author_id);
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in BlogAuthorController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'BlogAuthorController',
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

                $redirectPage = "admin/blog-author/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = BlogAuthor::select(
                    'id',
                    'author_name',
                    'author_slug',
                    'about_author',
                    'meta_title',
                    'meta_description',
                    'meta_keywords',
                    'canonical_url',
                    'person_schema',
                    'breadcrumb_schema'
                )->where('id', $id)->first();

                if (empty($dataResQry)) {
                    return redirect("admin/blog-author");
                }

                $data['row'] = $dataResQry;
            } else {
                $id = 0;
                $redirectPage = "admin/blog-author";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'authorName'  => 'required|max:100',
                    'authorAlias' => 'required|max:110',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $authorName = htmlEncode(Purifier::clean(request('authorName')));
                $authorAlias = htmlEncode(Purifier::clean(request('authorAlias')));
                $about_author = request('about_author');
                $person_schema = request('person_schema');
                $breadcrumb_schema = request('breadcrumb_schema');
                $meta_title = htmlEncode(request('meta_title'));
                $canonical_url = htmlEncode(request('canonical_url'));
                $meta_description = htmlEncode(request('meta_description'));
                $meta_keywords = htmlEncode(request('meta_keywords'));

                $duplicate = BlogAuthor::where('author_name', $authorName);


                if ($id != 0) {
                    $duplicate->where('id', '!=', $id);
                }

                if ($duplicate->exists()) {
                    return back()->with([
                        'level' => 'danger',
                        'message' => 'Author already exist'
                    ])->withInput();
                }





                if ($id > 0) {

                    $oldData = BlogAuthor::find($id);


                    $newData = [

                        'author_name'       => $authorName,
                        'author_slug'       => $authorAlias,
                        'about_author'      => $about_author,
                        'meta_title'        => $meta_title,
                        'meta_description'  => $meta_description,
                        'meta_keywords'     => $meta_keywords,
                        'canonical_url'     => $canonical_url,
                        'person_schema'     => $person_schema,
                        'breadcrumb_schema' => $breadcrumb_schema,

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
                            'blog_author',
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

                        'author_name'       => $authorName,
                        'author_slug'       => $authorAlias,
                        'about_author'      => $about_author,
                        'meta_title'        => $meta_title,
                        'meta_description'  => $meta_description,
                        'meta_keywords'     => $meta_keywords,
                        'canonical_url'     => $canonical_url,
                        'person_schema'     => $person_schema,
                        'breadcrumb_schema' => $breadcrumb_schema,
                        'created_by'        => 1,
                        'active_status'     => 1,
                        'created_at'        => now(),
                    ];

                    app(CommonController::class)->auditLog(
                        'blog_author',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    BlogAuthor::create($row);
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash('message', 'Blog Author ' . ($id ? 'updated' : 'created') . ' successfully.');

                return redirect($redirectPage);
            }
        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'BlogAuthorController',
                'Method' => $method,
                'Error' => $t->getMessage()
            ]);

            return back()->with([
                'level' => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('admin.blogs.addBlogAuthor', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function getAuthorDetails(Request $request)
    {
        try {

            $row = BlogAuthor::find($request->id);

            if (!$row) {
                return response()->json([
                    'status' => false
                ]);
            }

            return response()->json([
                'status' => true,
                'data' => $row
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
