<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('couriers', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('stores_id');
            $table->boolean('jne_kilat')->default(false);
            $table->boolean('sicepat_kilat')->default(false);
            $table->boolean('jnt_kilat')->default(false);

            $table->boolean('jne_reguler')->default(false);
            $table->boolean('sicepat_reg')->default(false);
            $table->boolean('jnt_reg')->default(false);

             $table->boolean('jne_ekonomis')->default(false);
            $table->boolean('sicepat_ekonomis')->default(false);
            $table->boolean('jnt_ekonomis')->default(false);

            $table->boolean('jne_kargo')->default(false);
            $table->boolean('sicepat_kargo')->default(false);
            $table->boolean('jnt_kargo')->default(false);

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
        Schema::dropIfExists('couriers');
    }
}
