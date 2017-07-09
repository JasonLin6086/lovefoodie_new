<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('wish_id');
            $table->unsignedInteger('seller_id');
            $table->decimal('bid_price', 8, 2);
            $table->LongText('bid_description');
            $table->string('bid_status')->default("NEW");
            $table->timestamps();
            
            $table->unique(array('wish_id', 'seller_id'));
            $table->foreign('wish_id')->references('id')->on('wishes');
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
        Schema::dropIfExists('bids');
    }
}
