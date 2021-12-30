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
use App\Notifications\PurchaseOrderCreatedNotification;
use App\Notifications\PurchaseOrderUpdatedNotification;

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
        return $this->sendResponseOk(compact(
            'suppliers',
            'agencies',
            'departments',
            'metodoPago',
            'usoCFDI',
            'formaPago'
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
            'total' => 'required',
            'concepts' => 'required|Array',
            'charges' => 'required|Array',
            'payment_condition' => 'required',
            'reason' => 'required',
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
        // $purchasesOrder->elaborated->notify(new PurchaseOrderCreatedNotification($purchasesOrder));

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
        return $this->sendResponseOk($purchase_order, "Edit Purchase Order.");
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request['updated_by'] = Auth::user()->id;
        $validate = validator($request->all(), [
            'estatus_key' => 'string',
            'updated_by' => 'required'
        ], []);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $estatus = Estatus::where('key', $request['estatus_key'])->first();
        $request['estatus_id'] = $estatus->id;
        // if ($request['estatus_key'] == 'verificado')
        //     $request['authorization_token'] = $this->purchaseOrderRepository->generateAutorizationToken($purchaseOrder);
        $updated = $purchaseOrder->update($request->all());
        if (!$updated) {
            return $this->sendResponseBadRequest("Error en la Actualizacion");
        }
        $purchaseOrder->estatus()->associate($estatus);
        $purchaseOrder->save();
        // $purchaseOrder->elaborated->notify(new PurchaseOrderUpdatedNotification($purchaseOrder->refresh()));

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
        $pdf = \PDF::loadView('pdf.purchase', compact('data'));
        return $pdf->stream();

        // return view('pdf.purchase', compact('purchaseOrder'));
    }
}
