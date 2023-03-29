<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class success_buy_planbeon24 extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = 'Nuevo plan BillConnector';
    public $user_data;
    public $plan_data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user_data, $plan_data)
    {
        $this->user_data = $user_data;
        $this->plan_data = $plan_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.beon24.success_buy_plan');
    }
}
