<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickupLocationMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_location_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pickup_method_id');
            $table->unsignedInteger('pickup_location_id')->nullable();
            $table->LongText('description');
            $table->string('address');
            $table->string('google_place_id');
            $table->timestamps();
            
            $table->foreign('pickup_method_id')->references('id')->on('pickup_methods');
            $table->foreign('pickup_location_id')->references('id')->on('pickup_locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pickup_location_mappings');
    }
}
