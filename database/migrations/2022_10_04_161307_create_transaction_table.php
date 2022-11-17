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
            $table->bigInteger('user_id');
            $table->bigInteger('code');
            $table->bigInteger('store_id');
            $table->bigInteger('address_id');
            $table->bigInteger('pay_method_id');
            $table->string('payment_url');
            $table->string('payment_token');
            $table->string('payment_due');
            $table->string('payment_status');
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('cancelled_note');
            $table->foreignId('cancelled_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable();
            $table->bigInteger('customer_city_id');
    
            $table->float('total_shop')->default(0);
            $table->float('disc_total')->default(0);
            $table->float('shipping_total')->default(0);
            $table->float('shipping_disc')->default(0);
            $table->float('price_total')->default(0);
            $table->string('status')->default('BELUM BAYAR');
            $table->string('note')->nullable();
            $table->string('invoice')->nullable();

            $table->softDeletes();
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
