<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDteOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dte_order', function (Blueprint $table) {
            $table->id();
            $table->text('type');
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('dtes_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
            $table->string('order_id')->unique();
            $table->text('rut');
            $table->text('classification');
            $table->text('name');
            $table->text('email')->nullable();
            $table->text('phone')->nullable();
            $table->text('address');
            $table->text('state');
            $table->text('city')->nullable();
            $table->double('discount')->default(0.00);
            $table->boolean('discount_prec')->default(true);
            $table->text('log')->nullable();


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
        Schema::dropIfExists('dte_order');
    }
}
