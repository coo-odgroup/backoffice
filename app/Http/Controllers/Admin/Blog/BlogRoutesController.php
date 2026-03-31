<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Models\blogs\Blog;
use App\Models\blogs\BlogRoutes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\CommonController;

class BlogRoutesController extends Controller
{
    public function index()
    {
        return view('admin.blogs.blogRoutes');
    }

    public function dataTableView()
    {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];

        try {

            $txtSearch = htmlEncode(request('txtSearch'));

            $dataQuery = DB::table('odbusdev.blog_routes as br')
                ->select(
                    'br.id as blog_routes_id',
                    'br.blog_id',
                    'br.from_city_id',
                    'br.to_city_id',
                    'br.route_slug',
                    'br.created_at',
                    'br.created_by',
                    'br.updated_at',
                    'br.updated_by',
                    DB::raw('(SELECT title FROM odbusdev.blogs WHERE id = br.blog_id LIMIT 1) as blog_title'),
                    DB::raw('(SELECT city_name FROM odbusmaster.mst_cities WHERE id = br.from_city_id LIMIT 1) as from_city_name'),
                    DB::raw('(SELECT city_name FROM odbusmaster.mst_cities WHERE id = br.to_city_id LIMIT 1) as to_city_name'),
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = br.created_by LIMIT 1) as created_by_name'),
                    DB::raw('(SELECT name FROM odbusmaster.users WHERE id = br.updated_by LIMIT 1) as updated_by_name')
                );

            // return $dataQuery->get();

            // Filters
            if (!empty($txtSearch)) {
                $dataQuery->where(function ($q) use ($txtSearch) {
                    $q->where('br.route_slug', 'like', "%{$txtSearch}%");
                });
            }

            $count = $dataQuery->count('br.id');

            $start = request()->input('start', 0);
            $length = request()->input('length', 10);

            $start = is_numeric($start) ? (int)$start : 0;
            $length = is_numeric($length) ? (int)$length : 10;

            // Ordering
            if (!empty(request('order'))) {

                $columns = [2 => 'br.route_slug', 3 => 'br.created_at', 4 => 'br.created_by'];

                $orderBy = request('order');
                $orderColumn = $columns[$orderBy[0]['column']] ?? 'br.route_slug';
                $orderType = $orderBy[0]['dir'];
            } else {
                $orderColumn = 'br.route_slug';
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
                    // $val->is_active = ($val->active_status == 1) ? 'Active' : 'Inactive';
                    $val->enc_blog_routes_id = Crypt::encryptString($val->blog_routes_id);
                }
            }

            $recordsTotal = $count;
            $recordsFiltered = $count;
            $data = $arrRes;
        } catch (\Throwable $t) {

            Log::info("Exception occurred in BlogRoutesController@dataTableView", [
                'error_message' => $t->getMessage(),
                'trace' => $t->getTraceAsString()
            ]);

            $errorMsg = config('constants.SERVER_ERROR_MESSAGE');

            Log::error("Error", [
                'Controller' => 'BlogRoutesController',
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

                $redirectPage = "admin/blog-routes/edit/" . $encId;
                $data['strPage'] = $method = 'Edit';
                $data['strSubmit'] = 'Update';
                $data['strReset'] = 'Cancel';

                $dataResQry = BlogRoutes::select('id', 'blog_id', 'from_city_id', 'to_city_id', 'route_slug')
                    ->where('id', $id)
                    ->first();

                if (empty($dataResQry)) {
                    return redirect("blog-routes");
                }

                $data['row'] = $dataResQry;

            } else {
                $id = 0;
                $redirectPage = "admin/blog-routes";
            }

            if (request()->isMethod('post')) {

                $validator = Validator::make(request()->all(), [
                    'blog_id'       => 'bail|required',
                    'from_city_id'  => 'bail|required',
                    'to_city_id'    => 'bail|required',
                    'route_slug'    => 'bail|required'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();

                $blog_id       = request('blog_id');
                $from_city_id  = request('from_city_id');
                $to_city_id    = request('to_city_id');
                $route_slug    = htmlEncode(request('route_slug'));

                if ($id != 0) {

                    $oldData = BlogRoutes::find($id);

                    $newData = [
                        'blog_id'      => $blog_id,
                        'from_city_id' => $from_city_id,
                        'to_city_id'   => $to_city_id,
                        'route_slug'   => $route_slug,
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
                            'mst_blog_routes',
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
                        'blog_id'      => $blog_id,
                        'from_city_id' => $from_city_id,
                        'to_city_id'   => $to_city_id,
                        'route_slug'   => $route_slug,
                        'created_by'   => 1,
                        'created_at'   => now(),
                    ];

                    app(CommonController::class)->auditLog(
                        'mst_blog_routes',
                        null,
                        'INSERT',
                        [],
                        $row
                    );

                    BlogRoutes::create($row);
                }

                DB::commit();

                session()->flash('level', 'success');
                session()->flash('message', 'Blog Routes ' . ($id ? 'updated' : 'created') . ' successfully.');

                return redirect($redirectPage);
            }

        } catch (\Throwable $t) {

            DB::rollBack();

            Log::error("Error", [
                'Controller' => 'BlogRoutesController',
                'Method' => $method,
                'Error' => $t->getMessage()
            ]);

            return back()->with([
                'level' => 'danger',
                'message' => config('constants.SERVER_ERROR_MESSAGE')
            ])->withInput();
        }

        return view('admin.blogs.addBlogRoutes', compact('data'));
    }

    public function edit($encId)
    {
        return $this->add($encId);
    }
}
