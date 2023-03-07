<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTransactionScheme extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN payment_status ENUM('belum-bayar', 'menunggu-konfirmasi', 'dikemas','dikirim','selesai','dibatalkan','expired' ) DEFAULT 'belum-bayar'");

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
