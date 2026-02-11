<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CitiesController extends Controller
{

    public function cities()
    {
        return view('master.cities');
    }

    public function addCities()
    {
        return view('master.addCities');
    }

    public function dataTable()
    {
        return view('master.citiesDataTable');
    }
}
