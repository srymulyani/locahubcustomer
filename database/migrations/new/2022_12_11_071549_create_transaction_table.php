<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('address_id')->references('id')->on('address')->onDelete('cascade');
            $table->string('code');
            $table->string('note')->nullable();
            $table->integer('grand_total');
            $table->enum('payment_status', ['menunggu pembayaran','dibayar','expired','dibatalkan'])->default('menunggu pembayaran');
            $table->string('snap_token')->nullable();
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
        Schema::dropIfExists('transaction');
    }
}
