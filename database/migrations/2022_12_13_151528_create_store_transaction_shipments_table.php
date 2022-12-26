<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreTransactionShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_transaction_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_transaction_id')->constrained()->references('id')->on('store_transactions')->onDelete('cascade');
            $table->string('track_number');
            $table->string('origin');
            $table->string('destination');
            $table->string('weight');
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
        Schema::dropIfExists('store_transaction_shipments');
    }
}
