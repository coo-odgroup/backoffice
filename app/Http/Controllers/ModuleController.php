<?php
    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;

    class ModuleController extends Controller
    {
        /**
         * Default admin landing page
         */
        public function index()
        {
            return view('admin.dashboard');
        }
    }
