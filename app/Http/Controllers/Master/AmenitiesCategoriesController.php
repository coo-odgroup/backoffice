<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AmenitiesCategoriesController extends Controller
{
    public function amenitiesCategories()
    {
        return view('master.amenitiesCategories');
    }
}
