<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=0;$i<15;$i++){
            $data[$i] = [
            'name' => Str::random(10),
            'price' => 100*$i,
            'products_information' => Str::random(10).'adalaha product',
            'categories_id' => $i,
            'store_id' => "1",
            'tags' => "",
            'height' => 5 * $i,
            'wide' => 6 * $i,
            'long' => 7 * $i,
            'weight' => 8 * $i,
            'status' => ''
            ]; 
            DB::table('products')->insert($data[$i]);
        }
    }
}
