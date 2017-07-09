<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'verified')) {
            Schema::table('users', function(Blueprint $table) {
                $table->boolean('verified')->default(false);
            });
        }

        if (!Schema::hasColumn('users', 'verify_token')) {
            Schema::table('users', function(Blueprint $table) {
                $table->string('verify_token', 40)->nullable(); 
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
