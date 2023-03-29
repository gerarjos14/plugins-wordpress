<?php

use App\Models\TransferRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->float('amount');
            $table->enum('status', [TransferRequest::WAITING, TransferRequest::PENDING, TransferRequest::CONFIRMED])->default(TransferRequest::WAITING);    
            $table->dateTime('created_at');
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('pending_at')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_requests');
    }
}
