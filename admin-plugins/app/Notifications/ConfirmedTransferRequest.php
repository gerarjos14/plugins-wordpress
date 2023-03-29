<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConfirmedTransferRequest extends Notification
{
    use Queueable;

    public $user;
    public $created;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($transferRequest)
    {
        $this->user = $transferRequest->user;
        $this->created = date('d-m-Y', strtotime($transferRequest->created_at));
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subject = env("APP_NAME")." - Solicitud de transferencia";
        $greeting = 'Hola '.$this->user->name.'.';
        $first_line = 'Hemos realizado la transferencia a su cuenta bancaria, correspondiente a su solicitud realizada durante la fecha '.
        $this->created.'.';
        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($first_line)
                    ->salutation('Saludos, '. config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
