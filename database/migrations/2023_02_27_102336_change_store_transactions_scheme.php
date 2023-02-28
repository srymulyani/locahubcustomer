<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStoreTransactionsScheme extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE store_transactions MODIFY COLUMN status ENUM('menunggu pembayaran', 'dibayar', 'dikemas','diproses','dikirim','selesai','dibatalkan','expired' ) DEFAULT 'menunggu pembayaran'");
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
