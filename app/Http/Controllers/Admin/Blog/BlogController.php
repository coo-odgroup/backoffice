<?php

namespace App\Http\Controllers\Admin\Blog;

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

            $title      = trim(request('title'));
            $slug       = trim(request('slug'));
            $authorId   = request('author_id');
            $categoryId = request('category_id');
            $selStatus  = (request('selstatus') !== null && request('selstatus') !== '') ? (int) request('selstatus') : '';

            $dataQuery = DB::table('odbusdev.blogs as b')
                ->select(
                    'b.id as blog_id',
                    'b.category_id',
                    'b.title',
                    'b.slug',
                    'b.short_description',
                    'b.content',
                    'b.thumb_alt_text',
                    'b.thumb_image',
                    'b.feature_alt_text',
                    'b.featured_image',
                    'b.author_id',
                    'b.breadcrumb_schema',
                    'b.faq_schema',
                    'b.service_schema',
                    'b.is_featured',
                    'b.active_status',
                    'b.published_at',
                    'b.meta_title',
                    'b.meta_description',
                    'b.meta_keywords',
                    'b.canonical_url',
                    'b.view_count',
                    'b.created_at',
                    'b.created_by',
                    'b.updated_at',
                    'b.updated_by',
                    DB::raw('(SELECT category_name FROM odbusdev.blog_categories WHERE id = b.category_id LIMIT 1) as category_name'),
                    DB::raw('(SELECT author_name FROM odbusdev.blog_authors WHERE id = b.author_id LIMIT 1) as author_name'),
                    DB::raw('(SELECT name FROM users WHERE id = b.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM users WHERE id = b.updated_by LIMIT 1) as updated_by_name')
                );

            // =========================
            // FILTERS
            // =========================

            if (!empty($title)) {
                $dataQuery->where(function ($q) use ($title) {
                    $q->where('b.title', 'like', "%{$title}%")
                        ->orWhere('b.slug', 'like', "%{$title}%")
                        ->orWhere('b.short_description', 'like', "%{$title}%")
                        ->orWhere('b.content', 'like', "%{$title}%");
                });
            }

            if (!empty($slug)) {
                $dataQuery->where('b.slug', 'like', '%' . $slug . '%');
            }

            if (!empty($authorId)) {
                $dataQuery->where('b.author_id', $authorId);
            }

            if (!empty($categoryId)) {
                $dataQuery->where('b.category_id', $categoryId);
            }

            if ($selStatus !== '') {
                $dataQuery->where('b.active_status', $selStatus);
            }

            $recordsTotal = $dataQuery->count();

            $start  = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start  = is_numeric($start) ? (int) $start : 0;
            $length = is_numeric($length) ? (int) $length : 10;

            // =========================
            // ORDERING
            // =========================
            if (!empty(request('order'))) {

                $columns = [
                    2 => 'category_name',
                    3 => 'b.title',
                    4 => 'b.slug',
                    5 => 'author_name',
                    6 => 'b.published_at',
                    7 => 'b.updated_at',
                    8 => 'b.active_status',
                ];

                $orderBy = request('order');
                $columnIndex = $orderBy[0]['column'] ?? 3;
                $orderType   = $orderBy[0]['dir'] ?? 'asc';

                $orderColumn = $columns[$columnIndex] ?? 'b.title';

                if (in_array($orderColumn, ['category_name', 'author_name'])) {
                    $dataQuery->orderByRaw($orderColumn . ' ' . $orderType);
                } else {
                    $dataQuery->orderBy($orderColumn, $orderType);
                }
            } else {
                $dataQuery->orderBy('b.title', 'asc');
            }

            // =========================
            // PAGINATION
            // =========================
            if ($length == -1) {
                $arrRes = $dataQuery->get();
            } else {
                $arrRes = $dataQuery->offset($start)
                    ->limit($length)
                    ->get();
            }

            // =========================
            // FORMAT DATA
            // =========================
            if ($arrRes->count() > 0) {
                foreach ($arrRes as $val) {
                    $val->created_date = !empty($val->created_at) ? date('d-M-Y H:i:s', strtotime($val->created_at)) : '--';
                    $val->updated_date = !empty($val->updated_at) ? date('d-M-Y H:i:s', strtotime($val->updated_at)) : '--';
                    $val->is_active    = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_blog_id  = Crypt::encryptString($val->blog_id);
                }
            }

            $recordsFiltered = $recordsTotal;
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
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
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
                    'category_id' => 'bail|required',


                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $title = htmlEncode(request('title'));
                $slug = htmlEncode(request('slug'));
                $short_description = htmlEncode(request('short_description'));
                $content = request('content') ?? '';
                $category_id = request('category_id');
                $is_featured = request('is_featured');

                $thumb_alt_text = htmlEncode(request('thumb_alt_text'));
                $feature_alt_text = htmlEncode(request('feature_alt_text'));

                $meta_title = htmlEncode(request('meta_title'));
                $canonical_url = htmlEncode(request('canonical_url'));
                $meta_description = htmlEncode(request('meta_description'));
                $meta_keywords = htmlEncode(request('meta_keywords'));
                $faq_schema = request('faq_schema');
                $service_schema = request('service_schema');
                $breadcrumb_schema = request('breadcrumb_schema');
                $author_id = request('author_id');

                $duplicate = Blog::where('title', $title);

                $openGraphJson = [];
                $twitterJson   = [];

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




                $openGraphData = DB::table('mst_annexture as a')
                    ->join('mst_annexture_type as t', 't.id', '=', 'a.annexture_type_id')
                    ->where('t.annexture_type', 'OPEN_GRAPH')
                    ->orderBy('a.id')
                    ->select('a.*')
                    ->get();

                if ($openGraphData->count() > 0) {
                    foreach ($openGraphData as $rowOg) {
                        $attributeValue = '';
                        if (strtolower(trim($rowOg->annexture_name)) === 'image') {

                            if (request()->hasFile("open_graph_image.{$rowOg->id}")) {

                                $file = request()->file("open_graph_image.{$rowOg->id}");
                                $ogFileName = 'og-' . time() . rand() . '.' . $file->getClientOriginalExtension();
                                $file->storeAs($path, $ogFileName, 'public');

                                $attributeValue = $ogFileName;

                                $newOg = $ogFileName;
                            } else {
                                $attributeValue = request("old_open_graph_image.{$rowOg->id}", '');
                            }
                        } else {
                            $attributeValue = request("open_graph.{$rowOg->id}", '');
                        }

                        $openGraphJson[] = [
                            'attribute_id'    => $rowOg->id,
                            'attribute_name'  => $rowOg->annexture_name,
                            'attribute_value' => $attributeValue
                        ];
                    }
                }

                $twitterData = DB::table('mst_annexture as a')
                    ->join('mst_annexture_type as t', 't.id', '=', 'a.annexture_type_id')
                    ->where('t.annexture_type', 'TWITTER')
                    ->orderBy('a.id')
                    ->select('a.*')
                    ->get();

                if ($twitterData->count() > 0) {
                    foreach ($twitterData as $rowTw) {
                        $twitterJson[] = [
                            'attribute_id'    => $rowTw->id,
                            'attribute_name'  => $rowTw->annexture_name,
                            'attribute_value' => request("twitter.{$rowTw->id}", '')
                        ];
                    }
                }


                $blogId = 0;

                if ($id > 0) {

                    $oldData = Blog::find($id);

                    $newData = [
                        'title' => $title,
                        'slug' => $slug,
                        'short_description' => $short_description,
                        'content' => $content,
                        'category_id' => $category_id,
                        'author_id' => $author_id,
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
                        'faq_schema' => $faq_schema,
                        'service_schema' => $service_schema,
                        'breadcrumb_schema' => $breadcrumb_schema,
                        'open_graph' => !empty($openGraphJson) ? json_encode($openGraphJson, JSON_UNESCAPED_UNICODE) : null,
                        'twitter' => !empty($twitterJson) ? json_encode($twitterJson, JSON_UNESCAPED_UNICODE) : null,
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

                    $oldData->fill($newData);
                    $oldData->updated_by = 1;
                    $oldData->save();

                    $blogId = $oldData->id;

                    if (!empty($newChanged)) {
                        app(CommonController::class)->auditLog(
                            'blogs',
                            $blogId,
                            'UPDATE',
                            $oldChanged,
                            $newChanged
                        );
                    }
                } else {

                    $row = [
                        'title' => $title,
                        'slug' => $slug,
                        'short_description' => $short_description,
                        'content' => $content,
                        'category_id' => $category_id,
                        'author_id' => $author_id,
                        'is_featured' => $is_featured,
                        'thumb_alt_text' => $thumb_alt_text,
                        'feature_alt_text' => $feature_alt_text,
                        'meta_title' => $meta_title,
                        'canonical_url' => $canonical_url,
                        'meta_description' => $meta_description,
                        'meta_keywords' => $meta_keywords,
                        'faq_schema' => $faq_schema,
                        'service_schema' => $service_schema,
                        'breadcrumb_schema' => $breadcrumb_schema,
                        'thumb_image' => $newThumb,
                        'featured_image' => $newFeature,
                        'open_graph' => !empty($openGraphJson) ? json_encode($openGraphJson, JSON_UNESCAPED_UNICODE) : null,
                        'twitter' => !empty($twitterJson) ? json_encode($twitterJson, JSON_UNESCAPED_UNICODE) : null,
                        'og_image' => $newOg,
                        'created_by' => 1,
                        'active_status' => 0,
                        'published_at' => null,
                        'created_at' => now(),
                    ];

                    $blog = Blog::create($row);
                    $blogId = $blog->id;

                    app(CommonController::class)->auditLog(
                        'blogs',
                        $blogId,
                        'INSERT',
                        [],
                        $row
                    );
                }




                if (request()->has('schema')) {
                    foreach (request('schema') as $attrId => $value) {

                        if (!empty($value)) {
                            $cleanValue = $value;

                            // OPTIONAL: remove script wrapper
                            $cleanValue = str_replace(
                                ['<script type="application/ld+json">', '</script>'],
                                '',
                                $cleanValue
                            );

                            DB::connection('mysql_dev')->table('blog_attributes')->insert([
                                'blog_id' => $blogId,
                                'attribute_type' => 3,
                                'attribute_id' => $attrId,
                                'attribute_value' => $cleanValue, //  RAW VALUE
                                'active_status' => 1,
                                'created_by' => 1,
                                'created_at' => now()
                            ]);
                        }
                    }
                }


                // ================== ARTICLE → BLOG TABLE ==================
                if (request()->has('article')) {
                    foreach (request('article') as $attrId => $value) {

                        if (!empty($value)) {
                            DB::connection('mysql_dev')->table('blogs')
                                ->where('id', $blogId)
                                ->update([
                                    'published_at' => $value,
                                    'updated_at' => now()
                                ]);
                        }
                    }
                }

                // ================== BLOG FAQ ==================
                $faqQuestions = request('faq_question', []);
                $faqAnswers   = request('faq_answer', []);

                // If edit, remove old FAQ rows first
                DB::connection('mysql_dev')
                    ->table('blog_faq')
                    ->where('blog_id', $blogId)
                    ->delete();

                if (!empty($faqQuestions)) {
                    foreach ($faqQuestions as $index => $question) {

                        $question = trim($question ?? '');
                        $answer   = trim($faqAnswers[$index] ?? '');

                        // skip empty row
                        if ($question === '' && $answer === '') {
                            continue;
                        }

                        DB::connection('mysql_dev')->table('blog_faq')->insert([
                            'blog_id'       => $blogId,
                            'faq_question'  => $question,
                            'faq_answer'    => $answer,
                            'active_status' => 1,
                            'created_by'    => 1,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                            'updated_by'    => 1,
                        ]);
                    }
                }
                DB::commit();


                session()->flash('level', 'success');
                session()->flash('message', 'Blog ' . ($id ? 'updated' : 'created') . ' successfully.');

                return redirect()->route('blogs.preview', Crypt::encryptString($blogId));
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

        $openGraphData = [];

        $openGraphData = DB::table('mst_annexture as a')
            ->join('mst_annexture_type as t', 't.id', '=', 'a.annexture_type_id')
            ->where('t.annexture_type', 'OPEN_GRAPH')
            ->orderBy('a.id')
            ->select('a.*')
            ->get();

        $twitterDat = [];

        $twitterData = DB::table('mst_annexture as a')
            ->join('mst_annexture_type as t', 't.id', '=', 'a.annexture_type_id')
            ->where('t.annexture_type', 'TWITTER')
            ->orderBy('a.id')
            ->select('a.*')
            ->get();

        $articleData = [];

        $articleData = DB::table('mst_annexture as a')
            ->join('mst_annexture_type as t', 't.id', '=', 'a.annexture_type_id')
            ->where('t.annexture_type', 'ARTICLE')
            ->orderBy('a.id')
            ->select('a.*')
            ->get();


        $schemaData = [];

        $schemaData = DB::table('mst_annexture as a')
            ->join('mst_annexture_type as t', 't.id', '=', 'a.annexture_type_id')
            ->where('t.annexture_type', 'SCHEMA')
            ->orderBy('a.id')
            ->select('a.*')
            ->get();

        $blogAttributes = [];
        $blogFaqs = [];
        $openGraphValues = [];
        $twitterValues = [];

        if ($id > 0) {
            $blogFaqs = DB::connection('mysql_dev')
                ->table('blog_faq')
                ->where('blog_id', $id)
                ->where('active_status', 1)
                ->orderBy('id')
                ->get();
        }

        if ($id > 0) {
            $blogAttributes = DB::connection('mysql_dev')
                ->table('blog_attributes')
                ->where('blog_id', $id)
                ->get()
                ->groupBy('attribute_type');

            $openGraphValues = !empty($data['row']->open_graph) ? json_decode($data['row']->open_graph, true) : [];
            $twitterValues   = !empty($data['row']->twitter) ? json_decode($data['row']->twitter, true) : [];
        }



        return view('admin.blogs.addBlogs', compact('data', 'openGraphData', 'twitterData', 'articleData', 'schemaData',  'blogAttributes',  'blogFaqs', 'openGraphValues', 'twitterValues'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }

    public function uploadEditorImage(Request $request)
    {
        try {
            $request->validate([
                'upload' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:3072'
            ]);

            if (!$request->hasFile('upload')) {
                return response()->json([
                    'error' => [
                        'message' => 'No file uploaded'
                    ]
                ], 400);
            }

            $file = $request->file('upload');
            $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/editor'), $name);

            return response()->json([
                'url' => asset('uploads/editor/' . $name)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => [
                    'message' => $th->getMessage()
                ]
            ], 422);
        }
    }

    public function getBlogDetails(Request $request)
    {
        try {
            $row = Blog::from('odbusdev.blogs as b')
                ->leftJoin('odbusdev.blog_categories as c', 'c.id', '=', 'b.category_id')
                ->leftJoin('odbusdev.blog_authors as a', 'a.id', '=', 'b.author_id')
                ->select(
                    'b.*',
                    'c.category_name',
                    'a.author_name'
                )
                ->where('b.id', $request->id)
                ->first();

            if (!$row) {
                return response()->json([
                    'status' => false
                ]);
            }

            return response()->json([
                'status' => true,
                'data'   => $row
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
