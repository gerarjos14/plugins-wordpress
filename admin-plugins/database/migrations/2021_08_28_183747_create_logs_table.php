<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');

            $table->string('visitor_id')->nullable();
            $table->mediumText('big_data');
            $table->string('web_url');

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDeleted('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDeleted('cascade');



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
        Schema::dropIfExists('logs');
    }
}
