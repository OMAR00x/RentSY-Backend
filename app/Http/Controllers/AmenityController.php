<?php

namespace App\Http\Controllers;

use App\Models\Amenity;

class AmenityController extends Controller
{
    public function index()
    {
        $amenities = Amenity::all();

        return response()->json($amenities);
    }
}
