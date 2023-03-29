<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();

            $table->enum('role',[User::ADMIN, User::AGENCY, User::CUSTOMER])->default(User::CUSTOMER);

            $table->boolean('allow_lifetime')->default(0);

            $table->boolean('connected_account')->default(0);
            $table->string('account_id')->nullable();

            $table->float('balance')->default(0);
            
            $table->string('image')->nullable();

            // para usuarios de agencias
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('users');
            
            // Pertenecen a un pais
            $table->unsignedBigInteger('country_id')->nullable();
            $table->foreign('country_id')->references('id')->on('countries');
            
            $table->integer('qty_of_plan_documents')->default(0); //Se trabaja si tiene un plan
            
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
