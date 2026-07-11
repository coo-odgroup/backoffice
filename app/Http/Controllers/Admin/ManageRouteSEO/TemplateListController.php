<?php

namespace App\Http\Controllers\Admin\ManageRouteSEO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CommonController;


class TemplateListController extends Controller
{
    public function index()
    {
        return view('admin.ManageRouteSEO.templateList');
    }

    public function dataTableView()
    {
        $recordsTotal    = 0;
        $recordsFiltered = 0;
        $data            = [];

        try {

            $routeId = (int) request('route_id');

            $dataQuery = DB::table('mst_seo_content as seo')
                ->select(
                    'seo.id',
                    'seo.route_id',
                    'seo.content',
                    'seo.meta_title',
                    'seo.meta_description',
                    'seo.is_publised',
                    'rd.source',
                    'rd.destination'
                )
                ->leftJoin('odbusmaster.mst_routes_details as rd', 'rd.id', '=', 'seo.route_id');

            if ($routeId > 0) {
                $dataQuery->where('seo.route_id', $routeId);
            }

            $dataQuery->whereNotNull('rd.id');

            $count = (clone $dataQuery)->count();

            $start  = (int) request('start', 0);
            $length = (int) request('length', 10);

            if ($length != -1) {
                $dataQuery->offset($start)->limit($length);
            }

            $arrRes = $dataQuery
                ->orderBy('rd.source')
                ->orderBy('rd.destination')
                ->get();

            foreach ($arrRes as $row) {
                $row->route_name = $row->source . ' to ' . $row->destination;
                $row->enc_id = Crypt::encryptString($row->id); // mst_seo_content.id
            }

            $recordsTotal    = $count;
            $recordsFiltered = $count;
            $data            = $arrRes;
        } catch (\Throwable $t) {

            Log::error([
                'Controller' => 'ManageTemplateController',
                'Method'     => 'dataTableView',
                'Error'      => $t->getMessage()
            ]);

            $recordsTotal    = 0;
            $recordsFiltered = 0;
            $data            = [];
        }

        return response()->json([
            'draw'            => intval(request('draw')),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function view(Request $request)
    {
        $id = Crypt::decryptString($request->enc_id);

        $seo = DB::table('mst_seo_content as seo')
            ->join(
                'odbusmaster.mst_routes_details as rd',
                'rd.id',
                '=',
                'seo.route_id'
            )
            ->select(
                'seo.content',
                'seo.meta_title',
                'seo.meta_description',
                'rd.source',
                'rd.destination',
                'rd.breadcrumb_schema',
                'rd.faq_schema'
            )
            ->where('seo.id', $id)
            ->first();

        return response()->json([
            'status' => true,
            'data' => $seo
        ]);
    }
}
