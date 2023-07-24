<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceiptStoreTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_transactions', function (Blueprint $table) {
            $table->string('receipt')->nullable()->after('shipping_etd');
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
            $table->dropColumn(['receipt']);
        });
    }
}
