<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCafsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cafs', function (Blueprint $table) {
            $table->id();
            $table->text('xml');
            $table->integer('type');
            $table->integer('from');
            $table->integer('to');
            $table->integer('available');
            $table->date('authorized_at');
            $table->integer('next');
            $table->foreignId('company_id')
                    ->constrained()
                    ->onDelete('cascade');
            $table->boolean('certification')->default(false);
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
        Schema::dropIfExists('cafs');
    }
}
