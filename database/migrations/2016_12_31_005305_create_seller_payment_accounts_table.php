<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellerPaymentAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_payment_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('seller_id')->unique();
            $table->foreign('seller_id')->references('id')->on('sellers');
            //table->string('PaymentProvider');
            $table->string('Acoount_id');
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
        Schema::dropIfExists('seller_payment_accounts');
    }
}
