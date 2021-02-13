<?php

namespace App\Notifications;

use App\Components\Tracking\Models\TrackingProspect;
use App\Components\User\Models\Group;
use App\Components\User\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrackingNewHistorical extends Notification
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
        $group_id = Group::where('name', 'Gerente')->first('id')->id;
        $ccUsers = User::ofGroups([$group_id])
            ->ofSellerAgency([$this->Tracking->agency_id])
            ->ofSellerType([$this->Tracking->department_id])
            ->get()->map->only('email')->pluck('email');

        $url = url('/admin#/tracking-prospect/tracking/prospect/' . $this->Tracking->id);
        return (new MailMessage)
            ->subject('Notificacion de Seguimiento SIIETBSA Folio:' . $this->Tracking->id)
            ->cc($ccUsers)
            ->greeting('Hola')
            ->line('Hay un nuevo Mensaje en su Seguimiento: ' . $this->Tracking->title)
            ->line('Referencia: ' . $this->Tracking->reference)
            ->line('Folio: ' . $this->Tracking->id)
            ->line('Prospecto: ' . $this->Tracking->prospect->full_name)
            ->line('Nuevo Estatus: ' . $this->Tracking->estatus->title)
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
