<?php

namespace App\Notifications;

use App\Components\User\Models\User;
use App\Components\Vehicle\Models\VehicleDispersal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleDispersalCreatedNotification extends Notification
{
    use Queueable;

    private $vehicleDispersal;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(VehicleDispersal $vehicleDispersal)
    {
        $this->vehicleDispersal = $vehicleDispersal;
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
        $ccUsers = User::All()
            ->map(function ($user) {
                if ($user->hasPermission('flotilla.admin') || $user->hasPermission('compras.aux')) {
                    return $user->email;
                }
            })->filter()->toArray();

        $uri = url('/admin#/dispersion/list');
        return (new MailMessage)
            ->subject('Solicitud de Dispercion - ' . $this->vehicleDispersal->id)
            ->cc($ccUsers)
            ->greeting('Buen dia, Se registro una nueva solictud de Dispercion')
            ->line('Folio Dispercion: #' . $this->vehicleDispersal->id)
            ->action('Ir a Listado de Disperciones', $uri)
            ->line('Gracias por usar nuestra Aplicacion.');
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
