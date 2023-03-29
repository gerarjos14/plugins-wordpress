<?php

namespace App\Observers;

use App\Models\User;
use App\Models\TransferRequest;
use App\Notifications\NewTransferRequest;
use App\Notifications\PendingTransferRequest;
use App\Notifications\ConfirmedTransferRequest;

class TransferRequestObserver
{
    /**
     * Handle the transfer request "created" event.
     *
     * @param  \App\Models\TransferRequest  $transferRequest
     * @return void
     */
    public function created(TransferRequest $transferRequest)
    {
        $admin = User::where('role', User::ADMIN)->first();
        $admin->notify(new NewTransferRequest($transferRequest));
    }

    /**
     * Handle the transfer request "updated" event.
     *
     * @param  \App\Models\TransferRequest  $transferRequest
     * @return void
     */
    public function updated(TransferRequest $transferRequest)
    {
        $user = $transferRequest->user;
        if($transferRequest->status == TransferRequest::PENDING){
            $user->notify(new PendingTransferRequest($transferRequest));
        }elseif($transferRequest->status == TransferRequest::CONFIRMED){
            if($user->balance >= $transferRequest->amount){
                $user->balance = $user->balance - $transferRequest->amount;
                $user->save();
                $user->notify(new ConfirmedTransferRequest($transferRequest));
            }
        }
    }

    /**
     * Handle the transfer request "deleted" event.
     *
     * @param  \App\Models\TransferRequest  $transferRequest
     * @return void
     */
    public function deleted(TransferRequest $transferRequest)
    {
        //
    }

    /**
     * Handle the transfer request "restored" event.
     *
     * @param  \App\Models\TransferRequest  $transferRequest
     * @return void
     */
    public function restored(TransferRequest $transferRequest)
    {
        //
    }

    /**
     * Handle the transfer request "force deleted" event.
     *
     * @param  \App\Models\TransferRequest  $transferRequest
     * @return void
     */
    public function forceDeleted(TransferRequest $transferRequest)
    {
        //
    }
}
