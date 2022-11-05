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
            $table->bigInteger('address_id');
            $table->bigInteger('pay_method_id');
            $table->string('no_resi')->nullable();
            $table->string('jasa_antar');
            $table->float('price_total')->default(0);
            $table->float('disc_total')->default(0);
            $table->float('shipping_total')->default(0);
            $table->string('status')->default('BELUM BAYAR');
            $table->string('note')->nullable();

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
