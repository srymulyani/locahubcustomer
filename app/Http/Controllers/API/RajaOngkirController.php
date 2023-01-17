<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{Address, City, Product, Province};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:address,id',
            'courier' => 'required|in:jne,pos,tiki',
            'products' => 'required|array|min:1',
            'products.*' => 'required|array|min:2|max:2',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
        ],[
            'products.required' => 'Pilih produk !',
            'products.array' => 'Produk tidak valid !',
            'products.min' => 'Pilih produk !',
            'products.*.required' => 'Pilih produk !',
            'products.*.array' => 'Produk tidak valid !',
            'products.*.min' => 'Pilih produk !',
            'products.*.product_id.required' => 'Pilih produk !',
            'products.*.product_id.exists' => 'Produk tidak ditemukan !',
            'products.*.quantity.required' => 'Pilih jumlah produk !',
            'products.*.quantity.numeric' => 'Jumlah produk tidak valid !',
            'products.*.quantity.min' => 'Jumlah produk minimal 1 !',
        ]);

        $product_ids = array_column($request->products, 'product_id');
        $store_count = Product::whereIn('id', $product_ids)->distinct('store_id')->count('store_id');

        if($store_count != 1){
            return response([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'products' => 'Products must be from the same store !'
                ]
            ], 422);
        }

        $products = Product::whereIn('id', $product_ids)->get();
        $weight = 0;
        foreach ($products as $product) {
            $key = array_search($product->id, array_column($request->products, 'product_id'));
            $quantity = $request->products[$key]['quantity'];

            $weight = $weight + ($product->weight * $quantity);
        }

        $origin = $products[0]->store->city_id;
        $destination = Address::find($request->address_id)->city_id;
        
        $response = Http::post('https://api.rajaongkir.com/starter/cost', [
            'key' => env('RAJAONGKIR_API_KEY'),
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $request->courier
        ]);
        
        return response([
            'success' => true,
            'response' => json_decode($response)
        ], 200);
    }

    public function migrate()
    {
        $response = Http::get('https://api.rajaongkir.com/starter/city', [
            'key' => env('RAJAONGKIR_API_KEY')
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
