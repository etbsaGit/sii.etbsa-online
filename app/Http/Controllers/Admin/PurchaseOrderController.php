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
use App\Components\Purchase\Models\PurchaseType;
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
        $suppliers = Supplier::with([])->get()->map->only(['id', 'code_equip', 'business_name', 'rfc', 'phone', 'email', 'credit_days']);
        // $suppliers = Supplier::IsActive()->get()->map->only(['id', 'code_equip', 'business_name', 'rfc', 'phone', 'email', 'credit_days']);
        $agencies = DB::table('agencies')->get(['id', 'code', 'title']);
        $departments = DB::table('departments')->get(['id', 'title']);
        $metodoPago = DB::table('cat_metodo_pago')->get(['clave', 'description']);
        $usoCFDI = DB::table('cat_uso_cfdi')->get(['clave', 'description']);
        $formaPago = DB::table('cat_forma_pago')->get(['clave', 'description']);
        $unitSat = DB::table('cat_unit_sat')->get(['id', 'clave', 'name', 'type']);
        $purchase_concept = PurchaseConcept::with('usocfdi', 'purchaseType', 'conceptProduct')->get();
        $purchase_types = PurchaseType::with('purchaseConcept')->get()->transform(function ($model) {
            return [
                'id' => $model->id,
                'name' => $model->name,
                'purchase_concept' => $model->purchaseConcept->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'concept_product' => $item->conceptProduct->map->only('id', 'name'),
                        'usocfdi' => $item->usocfdi->map->only('id', 'clave', 'description')
                    ];
                }),
            ];
        });
        return $this->sendResponseOk(
            compact(
                'suppliers',
                'agencies',
                'departments',
                'metodoPago',
                'usoCFDI',
                'formaPago',
                'unitSat',
                'purchase_concept',
                'purchase_types'
            ),
            "list Resources orders ok."
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        return DB::transaction(
            function () use ($request) {
                $validate = validator($request->all(), [
                    'supplier_id' => 'required',
                    'subtotal' => 'required',
                    'discount' => 'required',
                    'total' => 'required',
                    'charges' => 'required|Array',
                    'products' => 'required|Array',
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
                $request['created_by'] = Auth::user()->id;
                $purchasesOrder = $this->purchaseOrderRepository->create($request->all());

                if (!$purchasesOrder) {
                    return $this->sendResponseBadRequest("Failed to create.");
                }
                $estatus = Estatus::where('key', Estatus::ESTATUS_PENDIENTE)->first();
                $purchasesOrder->estatus()->associate($estatus);
                $purchasesOrder->save();

                // $charges = $request->get('charges', []);
                if ($charges = $request->get('charges', [])) {
                    foreach ($charges as $key => $value) {
                        # code... 
                        $purchasesOrder->chargeAgency()->attach($value['agency_id'], [
                            'department_id' => $value['depto_id'],
                            'percent' => $value['percent']
                        ]);
                    }
                }
                if ($products = $request->get('products', [])) {
                    foreach ($products as $product => $value) {
                        if ($value) {
                            $purchasesOrder->detailPurchase()->attach(
                                $value['concept_product_id'],
                                [
                                    'unit_id' => $value['unit_id'],
                                    'description' => $value['description'],
                                    'qty' => $value['qty'],
                                    'price' => $value['price'],
                                    'discount' => $value['discount'],
                                    'subtotal' => $value['subtotal']
                                ]
                            );
                        }
                    }
                }
                $purchasesOrder->refresh();
                return $this->sendResponseCreated([$purchasesOrder]);
            }
        );

        // $purchasesOrder->elaborated->notify(new PurchaseOrderCreatedNotification($purchasesOrder->refresh()));

    }

    /**
     * Display the specified resource.
     *
     * @param  App\Components\Purchase\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    // public function show(PurchaseOrder $purchaseOrder)
    // {
    //     //
    // }

    public function edit(PurchaseOrder $purchase_order)
    {

        $purchase = [
            'supplier' => $purchase_order->supplier,
            'charges' => $purchase_order->charges,
            'products' => $purchase_order->products,
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
                "with_isr" => $purchase_order->with_isr,
                "tax_isr" => $purchase_order->tax_isr,
                "with_iva_retenido" => $purchase_order->with_iva_retenido,
                "tax_iva_retenido" => $purchase_order->tax_iva_retenido,
                "with_retencion_cedular" => $purchase_order->with_retencion_cedular,
                "tax_retencion_cedular" => $purchase_order->tax_retencion_cedular,
                "with_retencion_125" => $purchase_order->with_retencion_125,
                "tax_retencion_125" => $purchase_order->tax_retencion_125,
                "with_flete" => $purchase_order->with_flete,
                "tax_flete" => $purchase_order->tax_flete,
                "total" => $purchase_order->total,
            ],
            "purchase_concept" => $purchase_order->purchase_concept,
            "purchase_type_id" => $purchase_order->purchase_type_id,
            "agency_id" => $purchase_order->ship->id,
            "observation" => $purchase_order->observation,
            "note" => $purchase_order->note,
            "payment_condition" => $purchase_order->payment_condition,
            "estatus" => $purchase_order->estatus,
            "created_by" => $purchase_order->created_by,
            "invoice_info" => $purchase_order->invoice ?? [],
        ];
        return $this->sendResponseOk(compact('purchase'), "Get Edit PurchaseOrder");
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Components\Purchase\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PurchaseOrder $purchase_order)
    {
        return DB::transaction(
            function () use ($purchase_order, $request) {
                $request['updated_by'] = Auth::user()->id;
                $validate = validator($request->all(), [
                    'supplier_id' => 'required',
                    'subtotal' => 'required',
                    'discount' => 'required',
                    'total' => 'required',
                    'charges' => 'required|Array',
                    'products' => 'required|Array',
                    'payment_condition' => 'required',
                    'purchase_concept_id' => 'required',
                    'observation' => 'required',
                    'uso_cfdi' => 'required',
                    'metodo_pago' => 'required',
                    'forma_pago' => 'required',
                    'updated_by' => 'required'
                ], []);

                if ($validate->fails()) {
                    return $this->sendResponseBadRequest($validate->errors()->first());
                }
                if ($request['estatus.key'] == 'denegar') {
                    $estatus = Estatus::where('key', Estatus::ESTATUS_PENDIENTE)->first();
                    $purchase_order->estatus()->associate($estatus);
                    $purchase_order->save();
                }
                $updated = $purchase_order->update($request->all());
                if (!$updated) {
                    return $this->sendResponseBadRequest("Error en la Actualizacion");
                }
                $purchase_order->refresh();
                $syncCharges = [];
                $syncProducts = [];
                if ($charges = $request->get('charges', [])) {
                    foreach ($charges as $key => $value) {
                        if ($value) {
                            $pivot = [
                                'department_id' => $value['depto_id'],
                                'percent' => $value['percent']
                            ];
                            $syncCharges[$value['agency_id']] = $pivot;
                        }
                    }
                }
                if ($products = $request->get('products', [])) {
                    foreach ($products as $product => $value) {
                        if ($value) {
                            $pivotProducts = [
                                'unit_id' => $value['unit_id'],
                                'description' => $value['description'],
                                'qty' => $value['qty'],
                                'price' => $value['price'],
                                'discount' => $value['discount'],
                                'subtotal' => $value['subtotal']
                            ];
                            $syncProducts[$value['concept_product_id']] = $pivotProducts;
                        }
                    }
                }
                $purchase_order->detailPurchase()->sync($syncProducts);
                $purchase_order->chargeAgency()->sync($syncCharges);
                return $this->sendResponseUpdated();
            }
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Components\Purchase\Models\PurchaseOrder $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();
        return $this->sendResponseDeleted();
    }

    public function print(PurchaseOrder $purchaseOrder)
    {
        $data = $purchaseOrder->load(['supplier', 'ship', 'elaborated']);
        $pdf = \PDF::loadView('pdf.purchase', compact('data'));
        return $pdf->stream();
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
}