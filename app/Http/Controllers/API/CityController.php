<?php

namespace App\Http\Controllers\API;

use App\Models\City;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use PhpParser\Node\Stmt\Else_;

class CityController extends Controller
{
    public function fetch(Request $request)
    {
        $city = City::where('name', 'like', '%' . $request->name . '%')->limit(10)->get();

        return ResponseFormatter::success(
            $city,
            'Data kota berhasil diambil'
        );
    }
}
