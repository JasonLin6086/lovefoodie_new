<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('dish_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('seller_id');
            $table->unique(array('dish_id', 'user_id'));
            $table->decimal('rating', 2, 1);
            $table->LongText('description');
            $table->timestamps();
            
            $table->foreign('seller_id')->references('id')->on('sellers');
            $table->foreign('dish_id')->references('id')->on('dishes');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
