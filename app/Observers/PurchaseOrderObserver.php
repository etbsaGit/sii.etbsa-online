<?php

namespace App\Observers;

use App\Components\Common\Models\Estatus;
use App\Components\Purchase\Models\PurchaseOrder;
use App\Components\User\Models\User;
use App\Mail\PurchaseOrder\PurchaseOrderCreated;
use Illuminate\Support\Facades\Mail;


class PurchaseOrderObserver
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
    public function saved(PurchaseOrder $purchaseOrder)
    {
        $toEmails = [];
        $ccEmails = [];
        $subjectMessage = '';
        $bodyMessage = '';
        $current_estatus = $purchaseOrder->estatus->key;
        $cargos = $purchaseOrder->charges;
        if ($current_estatus == Estatus::ESTATUS_VERIFICADO) {
            $subjectMessage = 'OC Verificada';
            $toEmails = User::All()
                ->map(function ($user) {
                    if ($user->hasPermission('compras.autorizar')) {
                        return $user->email;
                    }
                })->filter()->toArray();
        }
        if ($current_estatus == Estatus::ESTATUS_AUTORIZADO) {
            $subjectMessage = 'OC Autorizada';
            $toEmails = $purchaseOrder->elaborated;
        }
        if ($current_estatus == Estatus::ESTATUS_DENEGAR) {
            $subjectMessage = 'OC Rechazada';
            $toEmails = $purchaseOrder->elaborated;
        }
        if ($current_estatus == Estatus::ESTATUS_PROGRAMAR_PAGO) {
            $subjectMessage = 'OC Solictud de Prog. de Pago para OC:';
            $toEmails = User::All()
                ->map(function ($user) {
                    if ($user->hasPermission('compras.autorizar')) {
                        return $user->email;
                    }
                })->filter()->toArray();
        }
        if ($current_estatus == Estatus::ESTATUS_POR_PAGAR) {
            $subjectMessage = 'OC Programada para Pago para OC:';
            foreach ($cargos as $cargo) {
                $toEmails[] = $this->emailSucursales['contabilidad'][explode(' ', $cargo['agency']['code'])[0]];
            }
        }
        if ($current_estatus == Estatus::ESTATUS_PAGADA) {
            $subjectMessage = 'OC Pagada';
            $toEmails = [$purchaseOrder->elaborated];
            // $toEmails = [$purchaseOrder->elaborated, $purchaseOrder->supplier];
        }
        if ($current_estatus == Estatus::ESTATUS_POR_FACTURAR) {
            $subjectMessage = 'OC Solicitud  de Factura para OC:';
            $toEmails = [$purchaseOrder->elaborated];
            // $toEmails = [$purchaseOrder->elaborated, $purchaseOrder->supplier];
        }
        if ($current_estatus == Estatus::ESTATUS_PAGADA_PORFACTURAR) {
            $subjectMessage = 'OC Pagada, Pendiente de Factura para OC:';
            $toEmails = [$purchaseOrder->elaborated];
            // $toEmails = [$purchaseOrder->elaborated, $purchaseOrder->supplier];
        }
        Mail::to($toEmails)->cc($ccEmails)->send(new PurchaseOrderCreated($purchaseOrder, $subjectMessage));
    }
    /**
     * Handle the purchase order "created" event.
     *
     * @param  \App\Components\Purchase\Models\PurchaseOrder  $purchaseOrder
     * @return void
     */
    public function created(PurchaseOrder $purchaseOrder)
    {
        // Mail::to(auth()->user())->send(new PurchaseOrderCreated());
        $toEmails = [];
        $ccEmails = [];
        $subjectMessage = '';
        $bodyMessage = '';
        if ($purchaseOrder->estatus->key == Estatus::ESTATUS_PENDIENTE) {
            $subjectMessage = 'OC CREADA';
            $toEmails = User::All()
                ->map(function ($user) {
                    if ($user->hasPermission('compras.validar')) {
                        return $user->email;
                    }
                })->filter()->toArray();
        }

        

        Mail::to($toEmails)->cc($ccEmails)->send(new PurchaseOrderCreated($purchaseOrder, $subjectMessage));
    }

    /**
     * Handle the purchase order "updated" event.
     *
     * @param  \App\Components\Purchase\Models\PurchaseOrder  $purchaseOrder
     * @return void
     */
    public function updated(PurchaseOrder $purchaseOrder)
    {
        // Mail::to(auth()->user())->send(new PurchaseOrderCreated());
        $toEmails = [];
        $ccEmails = [];
        $subjectMessage = '';
        $bodyMessage = '';
        if ($purchaseOrder->estatus->key == Estatus::ESTATUS_PENDIENTE) {
            $subjectMessage = 'OC MODIFICADA';
            $toEmails = User::All()
                ->map(function ($user) {
                    if ($user->hasPermission('compras.validar')) {
                        return $user->email;
                    }
                })->filter()->toArray();
        }
        
        Mail::to($toEmails)->cc($ccEmails)->send(new PurchaseOrderCreated($purchaseOrder, $subjectMessage));
    }

    // /**
    //  * Handle the purchase order "deleted" event.
    //  *
    //  * @param  \App\Components\Purchase\Models\PurchaseOrder  $purchaseOrder
    //  * @return void
    //  */
    // public function deleted(PurchaseOrder $purchaseOrder)
    // {
    //     //
    // }

    // /**
    //  * Handle the purchase order "restored" event.
    //  *
    //  * @param  \App\Components\Purchase\Models\PurchaseOrder  $purchaseOrder
    //  * @return void
    //  */
    // public function restored(PurchaseOrder $purchaseOrder)
    // {
    //     //
    // }

    // /**
    //  * Handle the purchase order "force deleted" event.
    //  *
    //  * @param  \App\Components\Purchase\Models\PurchaseOrder  $purchaseOrder
    //  * @return void
    //  */
    // public function forceDeleted(PurchaseOrder $purchaseOrder)
    // {
    //     //
    // }
}