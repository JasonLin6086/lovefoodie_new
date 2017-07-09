<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDishesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dishes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('seller_id');
            $table->unsignedInteger('category_id');
            $table->string('name');
            $table->decimal('price', 5, 2);
            $table->DateTime('available_time')->nullable();
            $table->LongText('description');
            $table->string('isactive')->default('true');
            $table->decimal('rating', 2, 1)->default('0');
            $table->integer('rating_count')->default(0);
            $table->timestamps();
            
            $table->foreign('seller_id')->references('id')->on('sellers');
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
        Schema::dropIfExists('dishes');
    }
}
