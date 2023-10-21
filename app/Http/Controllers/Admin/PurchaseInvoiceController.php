<?php

namespace App\Http\Controllers\Admin;

use App\Components\Common\Models\Estatus;
use App\Components\Purchase\Models\Invoice;
use App\Components\Purchase\Models\PurchaseOrder;
use App\Components\Purchase\Repositories\PurchaseInvoiceRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    public function storeInvoicePurchase(Request $request, PurchaseOrder $purchase_order)
    {
        $request->validate([
            'file' => 'required|mimes:xml|max:2048',
        ]);
        $xml = simplexml_load_file($request->file);
        $ns = $xml->getNamespaces(true);
        $xml->registerXPathNamespace('c', $ns['cfdi']);
        $xml->registerXPathNamespace('t', $ns['tfd']);
        $data_xml = array();
        if ($ns['cfdi'] && $ns['tfd']) {
            foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor')[0]->attributes() as $Emisor => $value) {
                $data_xml[$Emisor . "_Emisor"] = $value;
            }
            foreach ($xml->xpath('//cfdi:Comprobante')[0]->attributes() as $Comprobante => $value) {
                $data_xml[$Comprobante] = $value;
            }
            foreach ($xml->xpath('//t:TimbreFiscalDigital')[0]->attributes() as $TimbreFiscalDigital => $value) {
                $data_xml[$TimbreFiscalDigital] = $value;
            }
            foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Receptor')[0]->attributes() as $Receptor => $value) {
                $data_xml[$Receptor . "_Receptor"] = $value;
            }
        }

        $payload = [
            'folio' => $data_xml['Folio'][0],
            'serie' => $data_xml['Serie'][0] ?? null,
            'invoice_date' => $data_xml['Fecha'][0],
            // 'folio_fiscal' => $data_xml['UUID'][0],
            'folio_fiscal' => json_decode(json_encode(($data_xml['UUID'])), true)[0],
            'metodo_pago' => $data_xml['MetodoPago'][0],
            'forma_pago' => $data_xml['FormaPago'][0],
            'uso_cfdi' => $data_xml['UsoCFDI_Receptor'][0],
            'amount' => $data_xml['Total'][0],
            'balance' => $data_xml['Total'][0],
            'currency' => $data_xml['Moneda'][0],
            'owner_id' => \Auth::user()->id,
            'date_to_pay' => $purchase_order->date_to_pay ?? null,
            'payment_date' => $purchase_order->payment_date ?? null,
            'estatus_id' => $purchase_order->estatus->id

        ];
        $validate = Validator(
            $payload,
            [
                'folio_fiscal' => "required|unique:invoices"
            ],
            [
                'folio_fiscal.unique' => "El Folio Fiscal ya fue Registrado anteriormente"
            ]
        );

        if ($validate->fails()) {
            return $this->sendResponseBadRequest("Folio Fiscal Duplicado");
        }
        return DB::transaction(function () use ($request, $purchase_order, $payload) {
            $invoice_purchase = $purchase_order->invoice()->create($payload);
            if ($purchase_order->estatus->key == Estatus::ESTATUS_POR_PAGAR) {
                $estatus = Estatus::where('key', Estatus::ESTATUS_POR_PAGAR)->first();
            } else if ($purchase_order->estatus->key == Estatus::ESTATUS_PAGADA_PORFACTURAR) {
                $estatus = Estatus::where('key', Estatus::ESTATUS_PAGADA)->first();
            } else {
                $estatus = Estatus::where('key', Estatus::ESTATUS_PROGRAMAR_PAGO)->first();
            }
            $purchase_order->estatus()->associate($estatus);
            $purchase_order->updated_by = \Auth::user()->id;
            $purchase_order->save();

            $message = 'Factura Registrada con Exito!';
            return $this->sendResponseUpdated(compact('invoice_purchase'), $message);
        });
    }


    public function updateDateToPayment(Request $request, Invoice $invoice)
    {
        return DB::transaction(function () use ($invoice, $request) {

            $estatus = Estatus::where('key', Estatus::ESTATUS_POR_PAGAR)->first();
            $updated = $invoice->update([
                'date_to_pay' => $request->date_to_pay,
                'estatus_id' => $estatus->id,
            ]);
            if (!$updated) {
                return $this->sendResponseBadRequest("Error en la Actualizacion");
            }
            $purchase_order = $invoice->invoiceable;
            $purchase_order->estatus()->associate($estatus);
            $purchase_order->date_to_pay = $request->date_to_pay;
            $purchase_order->update();
            return $this->sendResponseOk(compact('invoice'), "Factura(s) Programadas a Pago.");
        });
    }

    public function updateDatePayment(Request $request, Invoice $invoice)
    {

        return DB::transaction(function () use ($invoice, $request) {

            $estatus = Estatus::where('key', Estatus::ESTATUS_PAGADA)->first();
            $updated = $invoice->update([
                'payment_date' => $request->payment_date,
                'estatus_id' => $estatus->id,
            ]);
            if (!$updated) {
                return $this->sendResponseBadRequest("Error en la Actualizacion Fecha de Fac Pagada");
            }
            $purchase_order = $invoice->invoiceable;
            $purchase_order->estatus()->associate($estatus);
            $purchase_order->payment_date = $request->payment_date;
            $purchase_order->update();
            return $this->sendResponseOk(compact('invoice'), "Factura Pagada con exito!");
        });
    }

    public function resetDateToPayment(Invoice $invoice)
    {
        return DB::transaction(function () use ($invoice) {

            $estatus = Estatus::where('key', Estatus::ESTATUS_PROGRAMAR_PAGO)->first();
            $updated = $invoice->update([
                'date_to_pay' => null,
                'estatus_id' => $estatus->id,
            ]);
            if (!$updated) {
                return $this->sendResponseBadRequest("Error en la Actualizacion");
            }
            $purchase_order = $invoice->invoiceable;
            $purchase_order->estatus()->associate($estatus);
            $purchase_order->date_to_pay = null;
            $purchase_order->update();
            return $this->sendResponseOk(compact('invoice'), "Se Resetio la Programacion de Pago");
        });
    }

    public function resetDatePayment(Invoice $invoice)
    {
        return DB::transaction(function () use ($invoice) {

            $estatus = Estatus::where('key', Estatus::ESTATUS_POR_PAGAR)->first();
            $updated = $invoice->update([
                'payment_date' => null,
                'estatus_id' => $estatus->id,
            ]);
            if (!$updated) {
                return $this->sendResponseBadRequest("Error en la Actualizacion Fecha de Fac Pagada");
            }
            $purchase_order = $invoice->invoiceable;
            $purchase_order->estatus()->associate($estatus);
            $purchase_order->payment_date = null;
            $purchase_order->update();
            return $this->sendResponseOk(compact('invoice'), "Se Resetio la Fecha de Pago");
        });
    }

    public function validInvoicePurchaseOrder(Request $request, PurchaseOrder $purchase_order)
    {

        $request->validate([
            'file' => 'required|mimes:xml|max:2048',
        ]);

        $xml = simplexml_load_file($request->file);
        $ns = $xml->getNamespaces(true);
        $xml->registerXPathNamespace('c', $ns['cfdi']);
        $xml->registerXPathNamespace('t', $ns['tfd']);
        $data_xml = array();
        $valid_invoice = false;

        if ($ns['cfdi'] && $ns['tfd']) {

            foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor')[0]->attributes() as $Emisor => $value) {
                $data_xml[$Emisor . "_Emisor"] = $value;
            }
            foreach ($xml->xpath('//cfdi:Comprobante')[0]->attributes() as $Comprobante => $value) {
                $data_xml[$Comprobante] = $value;
            }
            foreach ($xml->xpath('//t:TimbreFiscalDigital')[0]->attributes() as $TimbreFiscalDigital => $value) {
                $data_xml[$TimbreFiscalDigital] = $value;
            }
            foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Receptor')[0]->attributes() as $Receptor => $value) {
                $data_xml[$Receptor . "_Receptor"] = $value;
            }

            $date_xml = Carbon::parse($data_xml['Fecha'][0]);
            $authorization_date = Carbon::parse($purchase_order->authorization_date);
            // dd(
            //     // (is_object($data_xml['UUID'])) ? xmlToArray($data_xml['UUID']) : $data_xml['UUID'],
            //     json_decode(json_encode(($data_xml['UUID']))),
            //     $data_xml,
            //     $data_xml['Rfc_Emisor'][0],
            //     $data_xml['Fecha'][0],
            //     Carbon::parse($data_xml['Fecha'][0]),
            //     $data_xml['Total'][0],
            //     $data_xml['MetodoPago'][0],
            //     $data_xml['FormaPago'][0],
            //     $data_xml['UsoCFDI_Receptor'][0],
            //     $purchase_order->supplier->rfc,
            //     $purchase_order->total,
            //     $purchase_order->metodo_pago,
            //     $purchase_order->uso_cfdi,
            //     $purchase_order->forma_pago,
            //     $purchase_order->authorization_date,
            //     $date_xml->gt($authorization_date),
            //     $date_xml->lt($authorization_date),
            // );
            // ($date_xml->gt($authorization_date)) &&
            $folio_fiscal = json_decode(json_encode(($data_xml['UUID'])), true);
            $payload = ['folio_fiscal' => $folio_fiscal[0]];

            $validate = Validator(
                $payload,
                [
                    'folio_fiscal' => "required|unique:invoices"
                ],
                [
                    'folio_fiscal.unique' => "El Folio Fiscal ya fue Registardo anteriormente"
                ]
            );
            // dd($folio_fiscal[0],$payload, $validate->fails());
            if ($validate->fails()) {
                // return $this->sendResponseBadRequest("Folio Fiscal Duplicado");
                // $valid_invoice = false;
                return $this->sendResponse(compact('data_xml', 'valid_invoice'), 'Folio Fiscal Duplicado, Registrado Anteriormente');
            }
            $valid_invoice = ($purchase_order->supplier->rfc == $data_xml['Rfc_Emisor'][0]) &&
                ($purchase_order->uso_cfdi == $data_xml['UsoCFDI_Receptor'][0]) &&
                ($purchase_order->metodo_pago == $data_xml['MetodoPago'][0]) &&
                ($purchase_order->forma_pago == $data_xml['FormaPago'][0]) &&
                ($purchase_order->total == $data_xml['Total'][0]);

            $valid_invoice = true;
            if ($valid_invoice) {
                return $this->sendResponseOk(compact('data_xml', 'valid_invoice'), 'El XML Valido');
            } else {
                return $this->sendResponse(compact('data_xml', 'valid_invoice'), 'Factura no es Valida');
            }


            if ($validate->fails()) {
                return $this->sendResponseBadRequest("Folio Fiscal Duplicado");
            }
        } else {
            return $this->sendResponseBadRequest('El XML debe ser un CFDI');
        }
    }


    public function printReport(Request $request)
    {
        $data = $this->purchaseInvoiceRepository->list($request->all());

        $pdf = \PDF::loadView('pdf.purchase-report', compact('data'));
        return $pdf->stream();
    }

    public function destroy(Invoice $invoice)
    {
        return DB::transaction(function () use ($invoice) {
            $purchase_order = $invoice->invoiceable;
            $purchase_order->fill([
                'date_to_payment' => null,
                'payment_date' => null,
                'estatus_id' => Estatus::where('key', $purchase_order->payment_date ?
                    Estatus::ESTATUS_PAGADA_PORFACTURAR
                    : Estatus::ESTATUS_POR_FACTURAR)
                    ->first()->id,
            ])->save();
            $invoice->delete();
            return $this->sendResponseDeleted();
        });
    }
}