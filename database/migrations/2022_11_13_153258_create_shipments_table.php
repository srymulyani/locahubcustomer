<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('transaction_id');
            $table->string('track_number')->nullable();
            $table->string('status');
            $table->bigInteger('qty');
            $table->bigInteger('total_weight');
            $table->string('address_id')->nullable();
            $table->string('city_id')->nullable();
            $table->string('province_id')->nullable();
            $table->bigInteger('postcode')->nullable();
            $table->datetime('shipped_at')->nullable();
            $table->bigInteger('shipped_by')->nullable();

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
        Schema::dropIfExists('shipments');
    }
}
