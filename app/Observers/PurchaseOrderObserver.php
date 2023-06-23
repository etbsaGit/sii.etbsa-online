<?php

namespace App\Observers;

use App\Components\Common\Models\Estatus;
use App\Components\Purchase\Models\PurchaseOrder;
use App\Components\User\Models\User;
use App\Http\Resources\PurchaseOrderCollection;
use App\Mail\PurchaseOrder\PurchaseOrderCreated;
use Illuminate\Support\Facades\Mail;


class PurchaseOrderObserver
{
    public $emailSucursales = [
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
    public function saved(PurchaseOrder $purchaseOrder)
    {
        $toEmails = [];
        $ccEmails = [];
        $subjectMessage = '';
        $bodyMessage = '';
        $current_estatus = $purchaseOrder->estatus->key;
        $cargos = $purchaseOrder->charges;
        // if ($current_estatus == Estatus::ESTATUS_PENDIENTE) {
        //     $subjectMessage = 'Orden de Compra Creada';
        //     $toEmails = User::All()
        //         ->map(function ($user) {
        //             if ($user->hasPermission('compras.validar')) {
        //                 return $user->email;
        //             }
        //         })->filter()->toArray();
        // }
        if ($current_estatus == Estatus::ESTATUS_VERIFICADO) {
            $subjectMessage = 'Orden de Compra Verificada';
            $toEmails = User::All()
                ->map(function ($user) {
                    if ($user->hasPermission('compras.autorizar')) {
                        return $user->email;
                    }
                })->filter()->toArray();
        }
        if ($current_estatus == Estatus::ESTATUS_AUTORIZADO) {
            $subjectMessage = 'Orden de Compra Autorizada';
            $toEmails = $purchaseOrder->elaborated;
        }
        if ($current_estatus == Estatus::ESTATUS_DENEGAR) {
            $subjectMessage = 'Orden de Compra Rechazada';
            $toEmails = $purchaseOrder->elaborated;
        }
        if ($current_estatus == Estatus::ESTATUS_PROGRAMAR_PAGO) {
            $subjectMessage = 'Orden de Compra Solictud de Prog. de Pago para OC:';
            $toEmails = User::All()
                ->map(function ($user) {
                    if ($user->hasPermission('compras.autorizar')) {
                        return $user->email;
                    }
                })->filter()->toArray();
        }
        if ($current_estatus == Estatus::ESTATUS_POR_PAGAR) {
            $subjectMessage = 'Orden de Compra Programada para Pago para OC:';
            foreach ($cargos as $cargo) {
                $toEmails[] = $this->emailSucursales['contabilidad'][explode(' ', $cargo['agency']['code'])[0]];
            }
        }
        if ($current_estatus == Estatus::ESTATUS_PAGADA) {
            $subjectMessage = 'Orden de Compra Pagada';
            $toEmails = [$purchaseOrder->elaborated];
            // $toEmails = [$purchaseOrder->elaborated, $purchaseOrder->supplier];
        }
        if ($current_estatus == Estatus::ESTATUS_POR_FACTURAR) {
            $subjectMessage = 'Orden de Compra Solicitud  de Factura para OC:';
            $toEmails = [$purchaseOrder->elaborated];
            // $toEmails = [$purchaseOrder->elaborated, $purchaseOrder->supplier];
        }
        if ($current_estatus == Estatus::ESTATUS_PAGADA_PORFACTURAR) {
            $subjectMessage = 'Orden de Compra Pagada, Pendiente de Factura para OC:';
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
            $subjectMessage = 'Orden de Compra CREADA';
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
            $subjectMessage = 'Orden de Compra MODIFICADA';
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