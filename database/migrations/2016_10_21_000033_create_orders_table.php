<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('seller_id');
            $table->string('type'); 
            $table->decimal('total', 8, 2);
            $table->decimal('deliver_fee', 8, 2);
            $table->DateTime('pickup_time');
            $table->string('pickup_type');
            $table->string('pickup_location_desc')->nullable();
            $table->string('address');
            $table->string('google_place_id');
            $table->string('status')->default("NEW");
            $table->string('payment_method');
            $table->DateTime('complete_time');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('orders');
    }
}
