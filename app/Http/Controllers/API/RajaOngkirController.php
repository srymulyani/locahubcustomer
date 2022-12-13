<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{City, Province};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller
{
    public function migrate()
    {
        $response = Http::get('https://api.rajaongkir.com/starter/city', [
            'key' => env('RAJA_ONGKIR_KEY')
        ]);

        foreach ($response['rajaongkir']['results'] as $city) {
            Province::updateOrCreate([
                'id' => $city['province_id']
            ],[
                'name' => $city['province']
            ]);

            City::updateOrCreate([
                'id' => $city['city_id']
            ],[
                'province_id' => $city['province_id'],
                'name' => $city['city_name'],
                'type' => $city['type'],
                'postal_code' => $city['postal_code']
            ]);
        }
        return response([
            'success' => true,
            'message' => 'Provinces and Cities data successfully updated from raja ongkir !'
        ], 200);
    }
}
