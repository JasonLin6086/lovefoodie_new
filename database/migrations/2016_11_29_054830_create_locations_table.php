<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('table_name');
            $table->unsignedInteger('table_id');
            $table->string('google_place_id');
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->string('address');
            $table->string('city');
            $table->string('zipcode');
            $table->string('state');
            $table->string('country');
            $table->timestamps();
            
            //$table->foreign('table_id')->references('id')->on('sellers','wishes','pickup_methods','orders');
            $table->unique(array('table_name', 'table_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
