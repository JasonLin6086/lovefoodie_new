<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSellerDishTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('sellers', 'rating_count')) {
            Schema::table('sellers', function(Blueprint $table) {
                $table->integer('rating_count')->default(0);
            });
        }
        
        if (!Schema::hasColumn('dishes', 'rating_count')) {
            Schema::table('dishes', function(Blueprint $table) {
                $table->boolean('rating_count')->default(0);
            });
        }        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
