<?php

namespace App\Notifications;

use App\Components\Tracking\Models\TrackingProspect;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrackingAssigned extends Notification
{
    use Queueable;

    private $Tracking;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TrackingProspect  $id)
    {
        $this->Tracking = $id;
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
        $url = url('/admin#/tracking-prospect/tracking/prospect/' . $this->Tracking->id);
        return (new MailMessage)
            ->subject('Notificacion de Seguimiento SIIETBSA')
            ->greeting('Hola')
            ->line('Tiene una nueva Notificacion en un Seguimientos de Prospectos')
            ->action('Ir a SIIETBSA', $url)
            ->line('Gracias por usar nuestro Sistema Integral SIIETBSA.')
            ->salutation('Saludos Cordiales por parte del Departamento de Sistemas');
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
