<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourierDetailStoreTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_transactions', function (Blueprint $table) {
            $table->string('courier')->nullable()->after('shipping_cost');
            $table->string('shipping_service')->nullable()->after('courier');
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
            $table->dropColumn(['courier', 'shipping_service']);
        });
    }
}
