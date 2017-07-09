<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('sellers', 'description')) {
            Schema::table('sellers', function(Blueprint $table) {
                $table->LongText('description');
            });
        }
        
        Schema::table('sellers', function ($table) {
            $table->string('email')->default('N/A')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('sellers', 'description')) {
            Schema::table('sellers', function(Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
}
