<?php

namespace App\Mail\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendError extends Mailable
{
    use Queueable, SerializesModels;


    public $name_user;
    public $msj;
    public $url;
    public $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name_user, $url , $msj, $type)
    {
        $this->name_user = $name_user;
        $this->msj = $msj;
        $this->url= $url;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.dtes.error_nc');
    }
}
