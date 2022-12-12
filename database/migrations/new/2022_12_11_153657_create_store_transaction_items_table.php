<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreTransactionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_transaction_id')->constrained()->references('id')->on('store_transactions')->onDelete('cascade');
            $table->string('product');
            $table->string('variation')->nullable();
            $table->integer('price');
            $table->integer('quantity');
            $table->integer('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_items');
    }
}
