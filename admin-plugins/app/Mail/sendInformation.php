<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class sendInformation extends Mailable
{
    use Queueable, SerializesModels;

    public $name_user;

    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name_user, $url)
    {
        $this->name_user = $name_user;
        $this->url= $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.dtes.sendToClient');
    }
}
