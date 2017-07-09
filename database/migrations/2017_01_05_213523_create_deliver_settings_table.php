<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliverSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliver_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('seller_id')->unique();
            $table->boolean('is_free_delivery');
            $table->decimal('free_delivery_price', 8, 2);
            $table->decimal('free_delivery_mile', 8, 2);
            $table->boolean('is_delivery_fee');
            $table->string('store_open_hour');
            $table->boolean('is_at_store');
            $table->decimal('order_before_hour', 5, 1);
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
        Schema::dropIfExists('deliver_settings');
    }
}
