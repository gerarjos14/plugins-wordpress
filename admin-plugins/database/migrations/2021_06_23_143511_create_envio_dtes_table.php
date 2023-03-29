<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnvioDtesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('envio_dtes', function (Blueprint $table) {
            $table->id();
            $table->text('xml');
            $table->string('track_id')->nullable();
            $table->string('estado')->nullable();
            $table->string('glosa')->nullable();
            $table->text('status_xml')->nullable();
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
        Schema::dropIfExists('envio_dtes');
    }
}
