<?php

namespace App\Http\Controllers\Admin;

use App\Components\Common\Models\Estatus;
use App\Components\Purchase\Models\Invoice;
use App\Components\Purchase\Repositories\PurchaseInvoiceRepository;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends AdminController
{
    /**
     * @var purchaseInvoiceRepository
     */
    private $purchaseInvoiceRepository;

    public function __construct(PurchaseInvoiceRepository $purchaseInvoiceRepository)
    {
        $this->purchaseInvoiceRepository = $purchaseInvoiceRepository;
    }

    public function index()
    {
        $data = $this->purchaseInvoiceRepository->list(request()->all());
        return $this->sendResponseOk($data, "list purchases invoices ok.");
    }

    public function updateDateToPayment(Request $request, Invoice $invoice)
    {
        // $data = $this->purchaseInvoiceRepository->list(request()->all());
        $estatus = Estatus::where('key', Estatus::ESTATUS_POR_PAGAR)->first();
        $updated = $invoice->update(['date_to_pay' => $request->date_to_pay]);
        if (!$updated) {
            return $this->sendResponseBadRequest("Error en la Actualizacion");
        }
        // return dd($invoice);
        $purchase_order = $invoice->invoiceable;
        $purchase_order->estatus()->associate($estatus);
        $purchase_order->save();
        return $this->sendResponseOk($invoice, "Factura(s) Programadas a Pago.");
    }

    public function updateDatePayment(Request $request, Invoice $invoice)
    {
        // $data = $this->purchaseInvoiceRepository->list(request()->all());

        $estatus = Estatus::where('key', Estatus::ESTATUS_PAGADA)->first();
        $updated = $invoice->update([
            'payment_date' => $request->payment_date,
        ]);
        if (!$updated) {
            return $this->sendResponseBadRequest("Error en la Actualizacion Fecha de Fac Pagada");
        }
        $purchase_order = $invoice->invoiceable;
        $purchase_order->estatus()->associate($estatus);
        $purchase_order->save();
        // return dd($invoice);
        return $this->sendResponseOk($invoice, "Factura Pagada con exito!");
    }
}
