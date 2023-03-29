<?php

use App\Models\Plan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->float('amount');
            $table->string('currency')->default('USD');

            // El ID del product en stripe
            $table->string('product_id')->nullable();
            // El ID del plan(tambien se le puede decir precio) del product en stripe
            $table->string('plan_id')->nullable();

            $table->enum('interval', [Plan::MONTH, Plan::YEAR, Plan::LIFETIME])->default(Plan::MONTH);

            $table->string('description')->nullable();
            $table->enum('platform', [
                Plan::ALEGRA,
                Plan::SIIGO,
                Plan::FAC_CHILE,
                Plan::FAC_PERU,
                Plan::BEON,
                Plan::ANALITYCS,
                Plan::PAGUE_A_TIEMPO
            ])->default(Plan::ALEGRA);

            $table->integer('qty_documents')->default(0);

            $table->boolean('active')->default(1);

            $table->unsignedBigInteger('country_id');
            $table->foreign('country_id')->references('id')->on('countries');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('plans');
    }
}
