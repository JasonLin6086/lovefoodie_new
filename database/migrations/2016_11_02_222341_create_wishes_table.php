<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWishesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wishes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('category_id');
            $table->string('topic');
            $table->LongText('description');
            $table->dateTime('pickup_time');
            $table->string('pickup_method');
            $table->string('address');
            $table->string('google_place_id');
            $table->integer('quantity')->default(0);
            $table->dateTime('end_date');
            $table->string('status')->default('OPEN'); //closed is 0, open is 1
            $table->decimal('price_from', 8, 2);
            $table->decimal('price_to', 8, 2);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wishes');
    }
}
