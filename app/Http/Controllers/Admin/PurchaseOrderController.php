<?php

namespace App\Http\Controllers\Admin;

use App;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Auth;

use App\Components\Purchase\Repositories\PurchaseOrderRepository;
use App\Components\Purchase\Models\PurchaseOrder;
use App\Components\Purchase\Models\Supplier;
use App\Components\Common\Models\Estatus;
use App\Components\Purchase\Models\PurchaseConcept;
use App\Notifications\PurchaseOrderCreatedNotification;
use App\Notifications\PurchaseOrderUpdatedNotification;
// use App\Notifications\PurchaseOrderCreatedNotification;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;

class PurchaseOrderController extends AdminController
{
    /**
     * @var PurchaseOrderRepository
     */
    private $purchaseOrderRepository;

    /**
     * UserController constructor.
     * @param PurchaseOrderRepository $purchaseOrderRepository
     */
    public function __construct(PurchaseOrderRepository $purchaseOrderRepository)
    {
        $this->purchaseOrderRepository = $purchaseOrderRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->purchaseOrderRepository->list(request()->all());
        return $this->sendResponseOk($data, "list purchases orders ok.");
    }

    public function resources()
    {
        $suppliers = Supplier::with([])->get()->map->only('id', 'business_name', 'rfc', 'phone', 'email', 'credit_days');
        $agencies = DB::table('agencies')->get(['id', 'code', 'title']);
        $departments = DB::table('departments')->get(['id', 'title']);
        $metodoPago = DB::table('cat_metodo_pago')->get(['clave', 'description']);
        $usoCFDI = DB::table('cat_uso_cfdi')->get(['clave', 'description']);
        $formaPago = DB::table('cat_forma_pago')->get(['clave', 'description']);
        $unitSat = DB::table('cat_unit_sat')->get(['clave', 'name', 'type']);
        $purchase_concept = PurchaseConcept::all();
        return $this->sendResponseOk(compact(
            'suppliers',
            'agencies',
            'departments',
            'metodoPago',
            'usoCFDI',
            'formaPago',
            'unitSat',
            'purchase_concept'
        ), "list Resources orders ok.");
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $estatus = Estatus::where('key', Estatus::ESTATUS_PENDIENTE)->first();
        $request['estatus_id'] = $estatus->id;
        $request['created_by'] = Auth::user()->id;
        $validate = validator($request->all(), [
            'created_by' => 'required',
            'estatus_id' => 'required',
            'supplier_id' => 'required',
            'subtotal' => 'required',
            'tax' => 'required',
            'discount' => 'required',
            'total' => 'required',
            'concepts' => 'required|Array',
            'charges' => 'required|Array',
            'payment_condition' => 'required',
            'purchase_concept_id' => 'required',
            'observation' => 'required',
            'uso_cfdi' => 'required',
            'metodo_pago' => 'required',
            'forma_pago' => 'required',
        ], []);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $purchasesOrder = $this->purchaseOrderRepository->create($request->all());

        if (!$purchasesOrder) {
            return $this->sendResponseBadRequest("Failed to create.");
        }

        $purchasesOrder->estatus()->associate($estatus);
        $purchasesOrder->save();

        $charges = $request->get('charges', []);
        foreach ($charges as $key => $value) {
            # code... 
            $purchasesOrder->chargeAgency()->attach($value['agency_id'], [
                'department_id' => $value['depto_id'],
                'percent' => $value['percent']
            ]);
        }
        // $purchasesOrder->elaborated->notify(new PurchaseOrderCreatedNotification($purchasesOrder->refresh()));

        return $this->sendResponseCreated($purchasesOrder);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        //
    }

    public function edit(PurchaseOrder $purchase_order)
    {
        $data = [
            'created_by' => $purchase_order->created_by,
            'supplier' => $purchase_order->supplier,
            'concepts' => $purchase_order->concepts,
            'charges' => $purchase_order->charges,
            // 'charges' => $purchase_order->charge,
            "invoice" => [
                'metodo_pago' => $purchase_order->metodopago,
                'uso_cfdi' => $purchase_order->usocfdi,
                'forma_pago' => $purchase_order->formapago,
            ],
            'amounts' => [
                "subtotal" => $purchase_order->subtotal,
                "discount" => $purchase_order->discount,
                "with_tax" => $purchase_order->with_tax,
                "tax" => $purchase_order->tax,
                "total" => $purchase_order->total,
            ],
            "purchase_concept" => $purchase_order->purchase_concept,
            "agency_id" => $purchase_order->ship->id,
            "observation" => $purchase_order->observation,
            "note" => $purchase_order->note,
            "payment_condition" => $purchase_order->payment_condition,
            "estatus" => $purchase_order->estatus,
            "invoice_info" => $purchase_order->invoice ?? [],

            // "invoice_info" => [
            //     'id' => $purchase_order->invoice->id ?? null,
            //     'folio_fiscal' => $purchase_order->invoice->folio_fiscal ?? '',
            //     'folio' => $purchase_order->invoice->folio ?? '',
            //     'invoice_date' => $purchase_order->invoice->invoice_date ?? '',
            //     'date_to_payment' => $purchase_order->invoice->date_to_payment ?? '',
            //     'payment_date' => $purchase_order->invoice->payment_date ?? '',
            //     'amount' => $purchase_order->invoice->amount ?? 0,
            // ]
        ];
        return $this->sendResponseOk($data, "Edit Purchase Order.");
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PurchaseOrder $purchase_order)
    {
        $request['updated_by'] = Auth::user()->id;
        $validate = validator($request->all(), [
            'estatus_key' => 'string',
            'charges' => 'required|Array',
            'updated_by' => 'required'
        ], []);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $updated = $purchase_order->update($request->all());
        if (!$updated) {
            return $this->sendResponseBadRequest("Error en la Actualizacion");
        }
        $charges = $request->get('charges', []);
        $syncCharges = [];
        foreach ($charges as $key => $value) {
            if ($value) {
                $pivot =  [
                    'department_id' => $value['depto_id'],
                    'percent' => $value['percent']
                ];
                $syncCharges[$value['agency_id']] =  $pivot;
            }
        }
        $purchase_order->chargeAgency()->sync($syncCharges);

        return $this->sendResponseUpdated();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        //
    }

    public function print(PurchaseOrder $purchaseOrder)
    {

        // $pdf = App::make('dompdf.wrapper');
        // $pdf->loadView('pdf.purchase', compact('purchaseOrder'));
        // return $pdf->stream();

        $data = $purchaseOrder->load('supplier');
        // dd($data);
        $pdf = \PDF::loadView('pdf.purchase', compact('data'));
        return $pdf->stream();

        // return view('pdf.purchase', compact('purchaseOrder'));
    }

    public function updateEstatus(Request $request, PurchaseOrder $purchase_order)
    {
        $request['updated_by'] = Auth::user()->id;
        $validate = validator($request->all(), [
            'estatus_key' => 'string|required',
            'updated_by' => 'required'
        ], []);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $estatus = Estatus::where('key', $request['estatus_key'])->first();
        $request['estatus_id'] = $estatus->id;
        // if ($request['estatus_key'] == 'verificado')
        //     $request['authorization_token'] = $this->purchaseOrderRepository->generateAutorizationToken($purchaseOrder);
        if ($request['estatus_key'] == Estatus::ESTATUS_AUTORIZADO)
            $request['authorization_date'] = Carbon::now();
        if ($request['estatus_key'] == Estatus::ESTATUS_DENGAR)
            $request['authorization_date'] = null;

        $updated = $purchase_order->update($request->all());
        if (!$updated) {
            return $this->sendResponseBadRequest("Error en la Actualizacion");
        }
        $purchase_order->estatus()->associate($estatus);
        $purchase_order->save();
        // $purchase_order->elaborated->notify(new PurchaseOrderUpdatedNotification($purchase_order->refresh()));
        return $this->sendResponseUpdated([], 'Estatus Actualizado');
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
            $valid_invoice = ($date_xml->gt($authorization_date)) &&
                ($purchase_order->supplier->rfc == $data_xml['Rfc_Emisor'][0]) &&
                ($purchase_order->uso_cfdi == $data_xml['UsoCFDI_Receptor'][0]) &&
                ($purchase_order->metodo_pago == $data_xml['MetodoPago'][0]) &&
                ($purchase_order->forma_pago == $data_xml['FormaPago'][0]) &&
                ($purchase_order->total == $data_xml['Total'][0]);

            if ($valid_invoice) {
                return $this->sendResponseOk(compact('data_xml', 'valid_invoice'), 'El XML Valido');
            } else {
                return $this->sendResponse(compact('data_xml', 'valid_invoice'), 'Factura no es Valida');
            }
        } else {
            return $this->sendResponseBadRequest('El XML debe ser un CFDI');
        }
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
            'serie' => $data_xml['Serie'][0],
            'invoice_date' => $data_xml['Fecha'][0],
            'folio_fiscal' => $data_xml['UUID'][0],
            'amount' => $data_xml['Total'][0],
            'currency' => $data_xml['Moneda'][0],
            'owner_id' => Auth::user()->id,

        ];
        $estatus = Estatus::where('key', Estatus::ESTATUS_FACTURADO)->first();
        $purchase_order->updated_by = Auth::user()->id;
        $invoice_purchase = $purchase_order->invoice()->create($payload);
        $purchase_order->estatus()->associate($estatus);
        $purchase_order->save();

        $message = 'Factura Registrada con Exito!';
        return $this->sendResponseUpdated(compact('invoice_purchase'), $message);
    }
}
