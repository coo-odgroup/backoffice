<?php
    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;

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
