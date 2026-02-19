<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AmenityCategoryController extends Controller
{
    public function amenityCategory()
    {
        return view('master.amenityCategory');
    }
}
