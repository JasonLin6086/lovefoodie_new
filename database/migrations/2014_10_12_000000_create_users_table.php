<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('provider')->nullable();
            $table->string('provider_user_id')->nullable();
            $table->string('image')->nullable();
            $table->string('avatar')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();
            $table->string('google_place_id')->nullable();
            $table->boolean('isseller');
//            $table->dateTime('last_login_time');
            $table->boolean('verified')->default(false);
            $table->string('verify_token', 40)->nullable();            
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
