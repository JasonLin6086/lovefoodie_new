<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickupMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('seller_id');
            $table->string('type');
            $table->Date('date')->nullable();
            $table->string('weekday')->nullable();
            $table->string('weekday_msg')->nullable();
            $table->boolean('no_time');
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->timestamps();
            
            $table->foreign('seller_id')->references('id')->on('sellers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pickup_methods');
    }
}
