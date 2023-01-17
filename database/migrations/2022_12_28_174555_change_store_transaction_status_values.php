<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStoreTransactionStatusValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE store_transactions MODIFY COLUMN status ENUM('menunggu konfirmasi', 'diproses', 'dikirim', 'selesai', 'dibatalkan', 'menunggu pembayaran', 'expired') DEFAULT 'menunggu pembayaran'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
