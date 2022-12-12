<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->references('id')->on('store')->onDelete('cascade');
            $table->foreignId('transaction_id')->references('id')->on('transaction')->onDelete('cascade');
            $table->enum('status', ['menunggu pembayaran','menunggu konfirmasi','diproses','dikirim','selesai','dibatalkan'])->default('menunggu pembayaran');
            $table->integer('total')->default(0);
            $table->integer('shipping_price')->nullable();
            $table->string('cancellation_note')->nullable();
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
        Schema::dropIfExists('store_transactions');
    }
}
