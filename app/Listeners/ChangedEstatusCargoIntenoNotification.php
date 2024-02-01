<?php

namespace App\Listeners;

use App\Events\CargoInternoEstatusChanged;
use App\Mail\CargoInternoCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class ChangedEstatusCargoIntenoNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CargoInternoEstatusChanged $event)
    {
        Mail::to($event->toEmails)->cc($event->ccEmails)
            ->send(new CargoInternoCreated($event->cargo_interno));
    }
}