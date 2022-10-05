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
        $suppliers = Supplier::with([])->get()->map->only('id', 'business_name', 'rfc', 'phone', 'email');
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
            'updated_by' => 'required'
        ], []);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $updated = $purchase_order->update($request->all());
        if (!$updated) {
            return $this->sendResponseBadRequest("Error en la Actualizacion");
        }

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
        $updated = $purchase_order->update($request->all());
        if (!$updated) {
            return $this->sendResponseBadRequest("Error en la Actualizacion");
        }
        $purchase_order->estatus()->associate($estatus);
        $purchase_order->save();
        // $purchase_order->elaborated->notify(new PurchaseOrderUpdatedNotification($purchase_order->refresh()));
        return $this->sendResponseUpdated([], 'Estatus Actualizado');
    }

    public function storeInvoicePurchase(Request $request, PurchaseOrder $purchase_order)
    {
        $request['updated_by'] = Auth::user()->id;
        $validate = validator($request->all(), [
            'updated_by' => 'required',
            'folio_fiscal' => ['required_without_all:date_to_payment,payment_date|max:36'],
            'folio' => 'required_without_all:date_to_payment,payment_date',
            'amount' => 'required_without_all:date_to_payment,payment_date',
            'invoice_date' => 'required_without_all:date_to_payment,payment_date',
            'date_to_payment' => 'required_without_all:folio_fiscal,folio,amount,invoice_date,payment_date',
            'payment_date' => 'required_without_all:folio_fiscal,folio,amount,invoice_date,date_to_payment'
        ], []);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $message = '';

        if ($request['payment_date'] != null) {
            $estatus = Estatus::where('key', Estatus::ESTATUS_PAGADA)->first();
            $invoice_purchase = $purchase_order->invoice()->update([
                'payment_date' => $request->payment_date,
            ]);
            $message = 'Factura Pagada con exito!';
        } elseif ($request['date_to_payment'] != null) {
            $estatus = Estatus::where('key', Estatus::ESTATUS_POR_PAGAR)->first();
            $invoice_purchase = $purchase_order->invoice()->update([
                'date_to_payment' => $request->date_to_payment,
            ]);
            $message = 'Factura Programada a Pago con exito!';
        } elseif ($request['id'] != null) {
            $estatus = Estatus::where('key', Estatus::ESTATUS_FACTURADO)->first();
            $invoice_purchase = $purchase_order->invoice()->update([
                'folio_fiscal' => $request->folio_fiscal,
                'folio' => $request->folio,
                'amount' => $request->amount,
                'invoice_date' => $request->invoice_date,
                'date_to_payment' => null,
                'payment_date' => null,
            ]);
            $message = 'Factura Actualizada con exito!';
        } else {
            $estatus = Estatus::where('key', Estatus::ESTATUS_FACTURADO)->first();
            $invoice_purchase = $purchase_order->invoice()->create($request->all());
            $message = 'Factura Registrada con Exito!';
        }
        $purchase_order->updated_by = $request->updated_by;
        $purchase_order->estatus()->associate($estatus);
        $purchase_order->save();

        return $this->sendResponseUpdated(compact('invoice_purchase'), $message);
    }
}
