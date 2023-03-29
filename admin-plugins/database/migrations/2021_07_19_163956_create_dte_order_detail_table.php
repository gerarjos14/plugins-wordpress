<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDteOrderDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dte_order_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dte_order')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->text('description');
            $table->boolean('exempt')->default(false);
            $table->double('discount')->default(0.00);;
            $table->boolean('discount_prec')->default(true);
            $table->double('quantity');
            $table->double('unit_price');
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
        Schema::dropIfExists('dte_order_detail');
    }
}
