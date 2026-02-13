<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BoardingDroppingController extends Controller
{
    public function boardingDropping()
    {
        return view('master.boardingDropping');
    }
}
