<?php

namespace App\Mail\PurchaseOrder;

use App;
use App\Components\Common\Models\Estatus;
use App\Components\Core\Utilities\Helpers;
use App\Components\Purchase\Models\PurchaseOrder;
use App\Http\Resources\PurchaseOrderCollection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Console\StubPublishCommand;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $subjectMessage;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PurchaseOrder $order, $subjectMessage = '')
    {
        $this->order = new PurchaseOrderCollection($order);
        $this->subjectMessage = $subjectMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = url('/admin#/purchases/' . $this->order->id . '/edit');
        $subject = $this->subjectMessage . ' OC#' . $this->order->purchase_number . ' ' . $this->order->ship->title . ' ' . $this->order->elaborated->name;
        return $this->subject($subject)
            ->markdown('emails.purchaseOrders.created')
            ->with(['url' => $url])
            ->when($this->hasAttachment($this->order->estatus->key), function ($mail) {
                return $mail->attachData($this->makePDF($this->order), 'orden_compra.pdf', [
                    'mime' => 'application/pdf',
                ]);
            });

    }

    public function hasAttachment($current_estatus)
    {
        return $current_estatus == Estatus::ESTATUS_PAGADA_PORFACTURAR || $current_estatus == Estatus::ESTATUS_POR_FACTURAR;
    }

    public function makePDF(PurchaseOrderCollection $order)
    {
        $purchase = [
            'id' => $order->id,
            'purchase_number' => $order->purchase_number,
            'currency' => $order->currency,
            'estatus' => $order->estatus->only('id', 'key', 'title'),
            'elaborated' => $order->elaborated->only('id', 'name', 'email'),
            'supplier' => $order->supplier->only('id', 'code_equip', 'business_name', 'rfc', 'email', 'credit_days'),
            'amounts' => $order->only(
                'subtotal',
                'discount',
                'with_tax',
                'tax',
                'with_isr',
                'tax_isr',
                'with_iva_retenido',
                'tax_iva_retenido',
                'with_retencion_cedular',
                'tax_retencion_cedular',
                'with_retencion_125',
                'tax_retencion_125',
                'with_flete',
                'tax_flete',
                'total',
            ),
            'products' => $order->products->toArray(),
            'charges' => $order->charges->toArray(),
            'invoice' => [
                'metodo_pago' => $order->metodopago->toArray(),
                'uso_cfdi' => $order->usocfdi->toArray(),
                'forma_pago' => $order->formapago->toArray(),
            ],
            'purchase_concept' => $order->purchase_concept->only('id', 'name'),
            'purchase_type' => $order->purchaseType->only('id', 'name'),
            'ship' => $order->ship->only('id', 'title', 'address'),
            'observation' => $order->observation,
            'note' => $order->note,
            'payment_condition' => $order->payment_condition,
            'authorization_date' => $order->authorization_date,
        ];
        $item = Helpers::arrayToObject($purchase);
        // dd($item);
        $pdf = App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('pdf.purchase', compact('item'));
        return $pdf->output();

    }
}