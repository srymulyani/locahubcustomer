<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourierDetailAgainStoreTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_transactions', function (Blueprint $table) {
            $table->string('courier_name')->nullable()->after('courier');
            $table->string('shipping_etd')->nullable()->after('shipping_service');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_transactions', function (Blueprint $table) {
            $table->dropColumn(['courier_name', 'shipping_etd']);
        });
    }
}
