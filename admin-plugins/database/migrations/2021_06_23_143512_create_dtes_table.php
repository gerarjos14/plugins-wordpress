<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDtesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dtes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('envio_dte_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('company_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->integer('type')->index();
            $table->integer('folio');
            $table->string('rut')->nullable();
            $table->date('fecha_emision')->nullable();
            $table->bigInteger('monto')->default(0);
            $table->string('uuid');
            $table->text('xml');
            $table->text('status_xml')->nullable();
            $table->boolean('certification')->default(false);
            $table->string('estado')->nullable();
            $table->string('glosa')->nullable();
            $table->string('order_id')->nullable()->unique();
            $table->unsignedInteger('retry')->default(0);
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
        Schema::dropIfExists('dtes');
    }
}
