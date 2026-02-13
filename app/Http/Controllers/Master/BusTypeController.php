<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BusTypeController extends Controller
{
 public function bustype()
    {
        return view('master.bustype');
    }
}
