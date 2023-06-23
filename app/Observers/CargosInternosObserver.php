<?php

namespace App\Observers;

use App\Components\CargosInternos\Models\CargosInternos;
use App\Components\Common\Models\Estatus;
use App\Events\CargoInternoEstatusChanged;
use App\Mail\CargoInternoCreated;
use Illuminate\Support\Facades\Mail;


class CargosInternosObserver
{
    /**
     * Handle the cargos internos "saved" event.
     *
     * @param  \App\Components\CargosInternos\Models\CargosInternos  $cargosInternos
     * @return void
     */
    public function saved(CargosInternos $cargosInternos)
    {
        $emailSucursales = [
            'contabilidad' => [
                '01' => 'contabilidadCELAYA@example.com.mx',
                '51' => 'contabilidadCELAYA@example.com.mx',
                '04' => 'contabilidadABASOLO@example.com.mx',
                '02' => 'contabilidadACAMBARO@example.com.mx',
                '03' => 'contabilidadIRAPUATO@example.com.mx',
                '53' => 'contabilidadIRAPUATO@example.com.mx',
                '55' => 'contabilidadMORELIA@example.com.mx',
                '09' => 'contabilidadNUEVAS@example.com.mx',
                '57' => 'contabilidadQUERETARO@example.com.mx',
                '58' => 'contabilidadQUERETARO@example.com.mx',
                '59' => 'contabilidadRENTA@example.com.mx',
                '05' => 'contabilidadSALAMANCA@example.com.mx',
                '06' => 'contabilidadSILAO@example.com.mx',
                '56' => 'contabilidadSILAO@example.com.mx',
                '99' => 'contabilidadMatriz@example.com.mx',
                '08' => 'contabilidadSAN@example.com.mx',
            ],
            'gerentes' => [
                '01' => 'gerenteCELAYA@example.com.mx',
                '51' => 'gerenteCELAYA@example.com.mx',
                '04' => 'gerenteABASOLO@example.com.mx',
                '02' => 'gerenteACAMBARO@example.com.mx',
                '03' => 'gerenteIRAPUATO@example.com.mx',
                '53' => 'gerenteIRAPUATO@example.com.mx',
                '55' => 'gerenteMORELIA@example.com.mx',
                '09' => 'gerenteNUEVAS@example.com.mx',
                '57' => 'gerenteQUERETARO@example.com.mx',
                '58' => 'gerenteQUERETARO@example.com.mx',
                '59' => 'gerenteRENTA@example.com.mx',
                '05' => 'gerenteSALAMANCA@example.com.mx',
                '06' => 'gerenteSILAO@example.com.mx',
                '56' => 'gerenteSILAO@example.com.mx',
                '99' => 'gerenteMatriz@example.com.mx',
                '08' => 'gerenteSAN@example.com.mx',
            ],
            'admin' => [
                'admin_1@example.com.mx',
                'admin_2@example.com.mx',
            ]
        ];
        $toEmails = [];
        $ccEmails = $emailSucursales['admin'];

        if (count($cargosInternos->sucursales) > 0) {
            $estatus = $cargosInternos->estatus->key;
            $sucursales = $cargosInternos->sucursales;

            foreach ($sucursales as $sucursal) {
                if ($estatus == Estatus::ESTATUS_AUTORIZADO)
                    $toEmails[] = $emailSucursales['contabilidad'][explode(' ', $sucursal->code)[0]];
                if ($estatus == Estatus::ESTATUS_DENEGAR || $estatus == Estatus::ESTATUS_PENDIENTE)
                    $toEmails[] = $emailSucursales['gerentes'][explode(' ', $sucursal->code)[0]];
                if ($estatus == Estatus::ESTATUS_PROGRAMAR_PAGO)
                    $toEmails[] = $emailSucursales['gerentes'][explode(' ', $sucursal->code)[0]];
            }

            CargoInternoEstatusChanged::dispatch($cargosInternos, $toEmails, $ccEmails);
        }
        // dd('Saved', array_unique($toEmails),$estatus, count($cargosInternos->sucursales),$cargosInternos->id);

    }

    /**
     * Handle the cargos internos "created" event.
     *
     * @param  \App\Components\CargosInternos\Models\CargosInternos  $cargosInternos
     * @return void
     */
    public function created(CargosInternos $cargosInternos)
    {

    }

    /**
     * Handle the cargos internos "updated" event.
     *
     * @param  \App\Components\CargosInternos\Models\CargosInternos  $cargosInternos
     * @return void
     */
    public function updated(CargosInternos $cargosInternos)
    {
        // dd('updated');
    }

    /**
     * Handle the cargos internos "deleted" event.
     *
     * @param  \App\Components\CargosInternos\Models\CargosInternos  $cargosInternos
     * @return void
     */
    public function deleted(CargosInternos $cargosInternos)
    {
        //
    }

    /**
     * Handle the cargos internos "restored" event.
     *
     * @param  \App\Components\CargosInternos\Models\CargosInternos  $cargosInternos
     * @return void
     */
    public function restored(CargosInternos $cargosInternos)
    {
        //
    }

    /**
     * Handle the cargos internos "force deleted" event.
     *
     * @param  \App\Components\CargosInternos\Models\CargosInternos  $cargosInternos
     * @return void
     */
    public function forceDeleted(CargosInternos $cargosInternos)
    {
        //
    }
}