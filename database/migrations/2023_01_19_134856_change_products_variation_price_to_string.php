<?php

use App\Models\ProductVariation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProductsVariationPriceToString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          foreach(ProductVariation::all() as $product_variation){
            $product_variation->update([
                'products_price' => str_replace(".00","",$product_variation->products_price)
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('string', function (Blueprint $table) {
            //
        });
    }
}
