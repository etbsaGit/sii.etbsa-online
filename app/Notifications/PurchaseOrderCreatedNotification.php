<?php

namespace App\Notifications;

use App\Components\Purchase\Models\PurchaseOrder;
use App\Components\User\Models\Group;
use App\Components\User\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PurchaseOrderCreatedNotification extends Notification
{
    use Queueable;

    private $purchaseOrder;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
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
                if ($user->hasPermission('compras.admin') || $user->hasPermission('compras.aux')) {
                    return $user->email;
                }
            })->filter()->toArray();

        $uri = url('/admin#/purchases/list');
        return (new MailMessage)
            ->subject('Solicitud Orden de Compra - ' . $this->purchaseOrder->id)
            ->cc($ccUsers)
            ->greeting('Buen dia, Se registro una nueva solictud de OC')
            ->line('Folio Orden de Compra: #' . $this->purchaseOrder->id)
            ->action('Ir a Ordenes de Compra', $uri)
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
