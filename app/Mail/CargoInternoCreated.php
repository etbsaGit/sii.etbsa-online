<?php

namespace App\Mail;

use App\Components\CargosInternos\Models\CargosInternos;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CargoInternoCreated extends Mailable
{
    use Queueable, SerializesModels;


    public $cargo_interno;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(CargosInternos $cargo_interno)
    {
        $this->cargo_interno = $cargo_interno;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->cargo_interno->estatus->title . ": Cargo Interno")
            ->markdown('emails.cargosInternos.created');
    }
}