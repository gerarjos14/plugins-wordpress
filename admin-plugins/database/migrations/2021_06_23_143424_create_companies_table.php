<?php

use App\Models\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('rut')->index();
            $table->string('name');
            $table->string('address');
            $table->string('email');
            $table->string('phone');
            $table->boolean('is_wordpress')->default(1); // se marca como defecto que es un wordpress
            $table->boolean('is_shopify')->nullable(); // se marca como defecto que es un wordpress
            $table->integer('economy_activity')->nullable();
            $table->string('gr');
            $table->string('resolution_date')->default('');
            $table->integer('resolution_nro')->default(0);
            $table->boolean('certification')->default(false);
            $table->enum('type_document', [ Company::BALLOT, Company::INVOICE, Company::EXEMPT_INVOICE ] )->default( Company::INVOICE );

            $table->foreignId('user_id')
                    ->constrained()
                    ->nullable()
                    ->onDelete('cascade');

            $table->foreignId('state_id')
                ->constrained()
                ->nullable()
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
        Schema::dropIfExists('companies');
    }
}
