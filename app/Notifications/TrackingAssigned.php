<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Components\Tracking\Models\TrackingProspect;

class TrackingAssigned extends Notification
{
    use Queueable;

    private $tracking;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TrackingProspect $tracking)
    {
        $this->tracking = $tracking;
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
        $url = url('/admin#/tracking-prospect/tracking/prospect/' . $this->tracking->id);
        return (new MailMessage)
            ->subject('Asignacion de Seguimiento SIIETBSA')
            ->line('Se le ha asignado un nuevo Seguimiento')
            ->action('Ir a SIIETBSA', $url)
            ->line('Gracias por usar nuestro Sistema Integral.');
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
