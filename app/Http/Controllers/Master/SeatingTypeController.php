<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SeatingTypeController extends Controller
{
 public function seatingType()
    {
        return view('master.seatingtype');
    }
}
