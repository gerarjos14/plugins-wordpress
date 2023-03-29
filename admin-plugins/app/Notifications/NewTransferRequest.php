<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTransferRequest extends Notification
{
    use Queueable;

    public $amount;

    public $created;
    
    public $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($transferRequest)
    {
        $this->amount = $transferRequest->amount;
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
        $subject = env("APP_NAME")." - Nueva solicitud de transferencia";
        $greeting = 'El usuario '. $this->user->name . ' ha solicitado una tranferencia.';
        $first_line = 'El monto de la tranferencia solicitada es: $'. $this->amount;
        $second_line = 'Fecha de la solicitud: '.$this->created;
        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($first_line)
                    ->line($second_line)
                    ->action('Ver solicitudes', route('admin.transfer-request.index'))
                    ->salutation('Saludos, '. config('app.name'));
    }
}
