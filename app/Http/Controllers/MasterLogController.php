<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class MasterLogController extends Controller
{
    /**
     * Default admin landing page
     */
    public function index()
    {
        return view('admin.masterLog');
    }

}