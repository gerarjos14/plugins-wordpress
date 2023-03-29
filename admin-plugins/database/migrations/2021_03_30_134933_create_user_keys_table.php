<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_keys', function (Blueprint $table) {
            $table->id();
            $table->string('alegra_user')->nullable();
            $table->string('alegra_token')->nullable();
            $table->string('wc_consumer_key')->nullable();
            $table->string('wc_consumer_secret')->nullable();            
            $table->string('website')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_keys');
    }
}
