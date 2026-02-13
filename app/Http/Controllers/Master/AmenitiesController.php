<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AmenitiesController extends Controller
{
 public function amenities()
    {
        return view('master.amenities');
    }
}
