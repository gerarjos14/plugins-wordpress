<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->string('run');
            $table->string('name');
            $table->string('issuer');
            $table->timestamp('from')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('to')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->string('email');
            $table->text('file');
            $table->string('password');
            $table->foreignId('company_id')
                  ->constrained()
                  ->onDelete('cascade');
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
        Schema::dropIfExists('signatures');
    }
}
