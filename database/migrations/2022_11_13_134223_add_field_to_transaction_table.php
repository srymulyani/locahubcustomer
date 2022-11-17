<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->string('code')->after('user_id')->unique;
            $table->string('payment_status')->after('pay_method_id');

            $table->string('payment_token')->after('pay_method_id')->nullable;
            $table->string('payment_url')->after('pay_method_id')->nullable;
            $table->string('customer_city_id')->after('jasa_antar')->nullable; //relasi table city
            $table->string('customer_province_id')->after('jasa_antar')->nullable; //relasi table province
            $table->bigInteger('customer_postcode')->after('jasa_antar')->nullable; 
            $table->foreignId('approved_by')->after('jasa_antar')->nullable()->constrained('users');
            $table->foreignId('cancelled_by')->after('jasa_antar')->nullable()->constrained('users');
            $table->text('cancellation_note')->after('jasa_antar')->nullable; 
            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction', function (Blueprint $table) {
            //
        });
    }
}
