<?php

namespace App\Notifications;

use App\Components\User\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageTrackingSent extends Notification
{
    use Queueable;

    protected $msg;
    protected $sender;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($msg, User $sender)
    {
        $this->msg = $msg;
        $this->sender = $sender;
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
        // return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Mensaje de Apoyo en Seguimiento')
            ->line('Solicitud de Apoyo.')
            ->line($this->msg['body.message'] . ' Seguimiento #' . $this->msg['messageable_id'])
            ->line('Gracias por usas La Aplicacion!');
        // ->action('Notificacion', url('/'))
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
            'body' => $this->msg->msg,
            'tracking_id' => $this->msg->tracking_id,
            'sender' => $this->sender
        ];
    }
}
