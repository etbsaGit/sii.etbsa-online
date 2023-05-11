<?php

namespace App\Events;

use App\Components\CargosInternos\Models\CargosInternos;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CargoInternoEstatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cargo_interno;
    public $toEmails;
    public $ccEmails;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CargosInternos $cargo_interno, $toEmails = [], $ccEmails = [])
    {
        $this->cargo_interno = $cargo_interno;
        $this->toEmails = $toEmails;
        $this->ccEmails = $ccEmails;
    }

/**
 * Get the channels the event should broadcast on.
 *
 * @return \Illuminate\Broadcasting\Channel|array
 */
// public function broadcastOn()
// {
//     return new PrivateChannel('channel-name');
// }
}