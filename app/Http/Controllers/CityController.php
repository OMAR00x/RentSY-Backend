<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Area;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::with('areas')->get();

        return response()->json($cities);
    }

    public function areas($cityId)
    {
        $areas = Area::where('city_id', $cityId)->get();

        return response()->json($areas);
    }
}
