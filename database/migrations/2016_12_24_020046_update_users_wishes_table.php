<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersWishesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'phone_number')) {
            Schema::table('users', function(Blueprint $table) {
                $table->string('phone_number')->nullable();
            });
        }

        if (!Schema::hasColumn('users', 'last_login_time')) {
            Schema::table('users', function(Blueprint $table) {
                $table->dateTime('last_login_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            });
        }
        
        if (Schema::hasColumn('wishes', 'serve')) {
            Schema::table('wishes', function(Blueprint $table) {
                $table->integer('serve')->default(0)->change();
            });
        }
        
        if (Schema::hasColumn('wishes', 'status')) {
            Schema::table('wishes', function(Blueprint $table) {
                $table->string('status')->default('OPEN')->change();
            });
        }
        
        if (Schema::hasColumn('bids', 'bid_status')) {
            Schema::table('bids', function(Blueprint $table) {
                $table->string('bid_status')->default('NEW')->change();
            });
        }
        
        if (!Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function(Blueprint $table) {
                $table->string('avatar')->nullable();
            });
        }
        
        if (!Schema::hasColumn('users', 'address')) {
            Schema::table('users', function(Blueprint $table) {
                $table->string('address')->nullable();
            });
        }

        if (Schema::hasColumn('users', 'google_place_ids')) {
            Schema::table('users', function(Blueprint $table) {
                $table->renameColumn('google_place_ids', 'google_place_id');
            });
        }       
        
        if (!Schema::hasColumn('users', 'google_place_id')) {
            Schema::table('users', function(Blueprint $table) {
                $table->string('google_place_id')->nullable();
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
        if (Schema::hasColumn('users', 'phone_number')) {
            Schema::table('users', function(Blueprint $table) {
                $table->dropColumn('phone_number');
            });
        }

        if (Schema::hasColumn('users', 'last_login_time')) {
            Schema::table('users', function(Blueprint $table) {
                $table->dropColumn('last_login_time');
            });
        }
    }
}
