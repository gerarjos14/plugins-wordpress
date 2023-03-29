<?php

use App\Models\BankAccount;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->string('last_name')->default('');
            $table->string('account_number')->default('');
            $table->enum('account_type',[BankAccount::SAVINGS, BankAccount::CHECKING])->default(BankAccount::SAVINGS);
            $table->string('bank_name')->default('');
            $table->string('identity_card')->default('');
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
        Schema::dropIfExists('bank_accounts');
    }
}
