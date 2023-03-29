<?php

namespace App\Notifications;

use App\Models\Dte;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ErrorDteSend extends Notification implements ShouldQueue
{
  use Queueable;

  public $dte;
  public $error;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct(Dte $dte, string $error = '')
  {
    $this->dte = $dte;
    $this->error = $error;
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
    return (new MailMessage)->markdown('emails.users.error-dte-send', [
      'dte' => $this->dte,
      'error' => $this->error,
    ]);
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
