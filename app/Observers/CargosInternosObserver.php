<?php

namespace App\Observers;

use App\Components\CargosInternos\Models\CargosInternos;
use App\Components\Common\Models\Estatus;
use App\Events\CargoInternoEstatusChanged;
use App\Mail\CargoInternoCreated;
use Illuminate\Support\Facades\Mail;



class CargosInternosObserver
{

    public $emailSucursales = [
        'contabilidad' => [
            '01' => 'cupalaura@etbsa.com.mx',
            '51' => 'cupalaura@etbsa.com.mx',
            '04' => 'contabilidadirapuato@etbsa.com.mx',
            '02' => 'ContabilidadSalamanca@etbsa.com.mx',
            '03' => 'contabilidadirapuato@etbsa.com.mx',
            '53' => 'contabilidadirapuato@etbsa.com.mx',
            '55' => 'ContabilidadSalamanca@etbsa.com.mx',
            '09' => 'AContableMatriz@etbsa.com.mx',
            '57' => 'ContabilidadQro@etbsa.com.mx',
            '58' => 'ContabilidadQro@etbsa.com.mx',
            '59' => '',
            '05' => 'ContabilidadSalamanca@etbsa.com.mx',
            '06' => 'ContabilidadSilao@etbsa.com.mx',
            '56' => 'ContabilidadSilao@etbsa.com.mx',
            '99' => 'AContableMatriz@etbsa.com.mx',
            '08' => 'ContabilidadSalamanca@etbsa.com.mx',

        ],
        'gerentes' => [
            '01' => 'barrosoernesto@etbsa.com.mx',
            '51' => 'barrosoernesto@etbsa.com.mx',
            '04' => 'ayalaelena@etbsa.com.mx',
            '02' => '',
            '03' => 'ayalaelena@etbsa.com.mx',
            '53' => 'ayalaelena@etbsa.com.mx',
            '55' => '',
            '09' => 'sanchezfelipe@etbsa.com.mx',
            '57' => 'jacoboalejandro@etbsa.com.mx',
            '58' => 'jacoboalejandro@etbsa.com.mx',
            '59' => '',
            '05' => 'rodriguezluzmaria@etbsa.com.mx',
            '06' => 'sanchezdavid@etbsa.com.mx',
            '56' => 'alvarezrogelio@etbsa.com.mx',
            '99' => 'albertoaugusto@etbsa.com.mx',
            '08' => 'ocanagaspar@etbsa.com.mx',
        ],
        'admin' => [
            'admin@etbsa-online.com.mx',
        ]
    ];
    /**
     * Handle the cargos internos "saved" event.
     *
     * @param  \App\Components\CargosInternos\Models\CargosInternos  $cargosInternos
     * @return void
     */
    public function saved(CargosInternos $cargosInternos)
    {
       
        $toEmails = [];
        $ccEmails = $this->emailSucursales['admin'];

        if (count($cargosInternos->sucursales) > 0) {
            $estatus = $cargosInternos->estatus->key;
            $sucursales = $cargosInternos->sucursales;

            foreach ($sucursales as $sucursal) {
                if ($estatus == Estatus::ESTATUS_AUTORIZADO)
                    $toEmails[] = $this->emailSucursales['contabilidad'][explode(' ', $sucursal->code)[0]];
                if ($estatus == Estatus::ESTATUS_DENEGAR || $estatus == Estatus::ESTATUS_PENDIENTE)
                    $toEmails[] = $this->emailSucursales['gerentes'][explode(' ', $sucursal->code)[0]];
                if ($estatus == Estatus::ESTATUS_PROGRAMAR_PAGO)
                    $toEmails[] = $this->emailSucursales['gerentes'][explode(' ', $sucursal->code)[0]];
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