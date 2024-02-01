<?php

namespace App\Http\Controllers\Admin;

use App;
use App\Components\Common\Services\PurchaseNumberService;
use App\Components\Purchase\Pivots\PurchasePivotCharge;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use Illuminate\Http\Request;

use App\Components\Core\Utilities\Helpers;
use App\Components\File\Services\FileService;
use App\Components\Purchase\Pivots\PurchasePivotProduct;

use App\Http\Requests\PurchaseOrder\StorePurchaseOrderRequest;
use App\Http\Requests\PurchaseOrder\UpdatePurchaseOrderRequest;
use App\Http\Requests\PurchaseOrder\UpdateStatusPurchaseOrderRequest;

use App\Http\Resources\PurchaseOrderCollection;

use App\Components\Purchase\Repositories\PurchaseOrderRepository;
use App\Components\Purchase\Models\PurchaseOrder;
use App\Components\Purchase\Models\Supplier;
use App\Components\Common\Models\Estatus;
use App\Components\Purchase\Models\PurchaseConcept;
use App\Components\Purchase\Models\PurchaseType;
use App\Components\File\Models\File as FilePurchase;

use Carbon\Carbon;
use Auth;

class PurchaseOrderController extends AdminController
{
    /**
     * @var PurchaseOrderRepository
     */
    private $purchaseOrderRepository;
    private $fileService;
    private $purchaseNumberService;

    /**
     * UserController constructor.
     * @param PurchaseOrderRepository $purchaseOrderRepository
     */
    public function __construct(PurchaseOrderRepository $purchaseOrderRepository, FileService $fileService, PurchaseNumberService $purchaseNumberService)
    {
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->fileService = $fileService;
        $this->purchaseNumberService = $purchaseNumberService;
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
        $departments = DB::table('departments')->whereNotNull('code')->get(['id', 'title']);
        $metodoPago = DB::table('cat_metodo_pago')->get(['clave', 'description']);
        $usoCFDI = DB::table('cat_uso_cfdi')->get(['clave', 'description']);
        $formaPago = DB::table('cat_forma_pago')->get(['clave', 'description']);
        $unitSat = DB::table('cat_unit_sat')->get(['id', 'clave', 'name', 'type']);
        $purchase_concept = PurchaseConcept::with('usocfdi', 'purchaseType', 'conceptProduct')->get();
        $payment_condition = [
            ['text' => "Contado", 'value' => 5],
            ['text' => "8 Dias", 'value' => 8],
            ['text' => "15 Dias", 'value' => 15],
            ['text' => "30 Dias", 'value' => 30],
            ['text' => "60 Dias", 'value' => 60],
            ['text' => "90 Dias", 'value' => 90],
        ];
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
                'purchase_types',
                'payment_condition'
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
    public function store(StorePurchaseOrderRequest $request)
    {
        return DB::transaction(
            function () use ($request) {
                $request['created_by'] = Auth::user()->id;
                $request['estatus_id'] = Estatus::where('key', Estatus::ESTATUS_PENDIENTE)->first()->id;
                $request['purchase_number'] = $this->purchaseNumberService->setNextPurchaseNumber();
                $purchaseOrder = $this->purchaseOrderRepository->create($request->all());


                if (!$purchaseOrder) {
                    return $this->sendResponseBadRequest("Failed to create.");
                }
                if ($charges = $request->get('charges', [])) {
                    foreach ($charges as $chargeData) {
                        $charge = new PurchasePivotCharge($chargeData);
                        $charge->purchaseOrder()->associate($purchaseOrder);
                        $charge->save();
                    }
                }
                if ($products = $request['products'] ?? []) {
                    foreach ($products as $productData) {
                        $product = new PurchasePivotProduct($productData);
                        $product->purchaseOrder()->associate($purchaseOrder);
                        $product->save();
                    }
                }

                if ($request->hasFile('file')) {
                    $files = $request->file('file');
                    $error = false;
                    $errorMessage = '';
                    $fileRecord = null;
                    foreach ($files as $file) {
                        // dd($file);
                        try {
                            $fileOriginalName = $file->getClientOriginalName();
                            $filePath = $file->store('purchase_orders/id_' . $purchaseOrder->id . '/files', 's3');

                            if (!$filePath) {
                                $this->addError("Failed to upload.", 400);
                                return false;
                            }

                            // other data
                            $ext = pathinfo(config('filesystems.disks.local.root') . '/' . $filePath, PATHINFO_EXTENSION);
                            $size = Storage::disk('s3')->getSize($filePath);
                            $type = Storage::disk('s3')->getMimetype($filePath);

                            $fileData = [
                                'original_name' => $fileOriginalName,
                                'path' => $filePath,
                                'ext' => $ext,
                                'size' => $size,
                                'type' => $type,
                            ];
                        } catch (FileNotFoundException $e) {
                            $error = true;
                            $errorMessage = $e->getMessage();
                            break;
                        }
                        if (!$fileData) {
                            $error = true;
                            $errorMessage = $this->fileService->getErrors()->first();
                            break;
                        }
                        $fileRecord = $purchaseOrder->files()->create([
                            'name' => $fileData['original_name'],
                            'uploaded_by' => Auth::user()->id,
                            'file_type' => $fileData['type'],
                            'extension' => $fileData['ext'],
                            'size' => $fileData['size'],
                            'path' => $fileData['path'],
                        ]);

                        if (!$fileRecord) {
                            if (Storage::disk('s3')->exists($fileData['path'])) {
                                Storage::disk('s3')->delete($fileData['path']);
                            }
                            $error = true;
                            $errorMessage = "Failed to create record.";
                            break;
                        }

                    }
                    if ($error) {
                        return $this->sendResponseBadRequest($errorMessage);
                    }
                }

                $purchaseOrder->refresh();
                // TODO: Despachar Evento de nueva creacion enviar Notificacion a las Sucusales y Admons Correpondientes
                // Mail::to(auth()->user())->send(new PurchaseOrderCreated());
                return $this->sendResponseCreated([$purchaseOrder]);
            }
        );

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
        $purchase = new PurchaseOrderCollection($purchase_order);
        return $this->sendResponseOk(compact('purchase'), "Get Edit PurchaseOrder");
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Components\Purchase\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePurchaseOrderRequest $request, PurchaseOrder $purchase_order)
    {
        return DB::transaction(
            function () use ($purchase_order, $request) {
                $request['updated_by'] = Auth::user()->id;
                if ($request['estatus.key'] == 'denegar') {
                    $estatus = Estatus::where('key', Estatus::ESTATUS_PENDIENTE)->first();
                    $purchase_order->estatus()->associate($estatus);
                }
                $updated = $purchase_order->update($request->all());
                if (!$updated) {
                    return $this->sendResponseBadRequest("Error en la Actualizacion");
                }
                $purchase_order->refresh();

                if ($charges = $request->get('charges', [])) {
                    $primaryKeyPivotCharge = PurchasePivotCharge::getPrimaryKey();
                    $chargesIdsToUpdate = array_map(function ($item) use ($primaryKeyPivotCharge, $purchase_order) {
                        $result = array_intersect_key($item, array_flip($primaryKeyPivotCharge));
                        $result['purchase_order_id'] = $purchase_order->id;
                        return $result;
                    }, $charges);

                    foreach ($purchase_order->pivotCharge as $charge) {
                        $chargeId = $charge->getPrimaryKeyValues();
                        if (in_array($chargeId, $chargesIdsToUpdate)) {
                            // El charge existe en el array, actualízalo
                            $chargeData = $charges[array_search($chargeId, $chargesIdsToUpdate)];
                            DB::statement("
                                        UPDATE {$charge->getTable()}
                                        SET percent = :percent
                                        WHERE purchase_order_id = :purchase_order_id
                                        AND agency_id = :agency_id
                                        AND department_id = :department_id
                                    ", [
                                'percent' => $chargeData['percent'],
                                'purchase_order_id' => $purchase_order->id,
                                'agency_id' => $chargeData['agency_id'],
                                'department_id' => $chargeData['department_id']
                            ]);
                        } else {
                            // El charge no existe en el array, elimínalo
                            DB::statement("
                                        DELETE FROM {$charge->getTable()}
                                        WHERE purchase_order_id = :purchase_order_id
                                        AND agency_id = :agency_id
                                        AND department_id = :department_id
                                    ", $charge->getPrimaryKeyValues());
                        }
                    }

                    $newCharges = [];
                    foreach ($charges as $postData) {
                        $chargeIdToInsert = array_diff_key($postData, ['percent' => '']);
                        $chargeIdToInsert['purchase_order_id'] = $purchase_order->id;
                        $pivotCharge = PurchasePivotCharge::where('purchase_order_id', $chargeIdToInsert['purchase_order_id'])
                            ->where('agency_id', $chargeIdToInsert['agency_id'])
                            ->where('department_id', $chargeIdToInsert['department_id'])
                            ->get();
                        if ($pivotCharge->isEmpty()) {
                            $newCharges[] = $postData;
                        }
                    }
                    $purchase_order->pivotCharge()->createMany($newCharges);
                }
                if ($products = $request->get('products', [])) {
                    $productsIdsToUpdate = array_column($products, 'id');
                    foreach ($purchase_order->pivotProduct as $product) {
                        $productId = $product->id;
                        if (in_array($productId, $productsIdsToUpdate)) {
                            // El product existe en el array, actualízalo
                            $productData = $products[array_search($productId, $productsIdsToUpdate)];
                            $product->update($productData);
                        } else {
                            // El product no existe en el array, elimínalo
                            $product->delete();
                        }
                    }
                    // Agrega los nuevos elementos al final de la relación
                    $newProducts = [];
                    foreach ($products as $postData) {
                        if (!isset($postData['id'])) {
                            // El post no tiene un ID, es un nuevo registro
                            $newProducts[] = $postData;
                        }
                    }
                    $purchase_order->pivotProduct()->createMany($newProducts);
                }
                // Actualiza Archivos si existe
                // if ($request->get('file') == []) {
                //     if (!!$purchase_order->files) {
                //         $purchase_order->files->each(function ($file) {
                //             $file->delete();
                //             if (Storage::disk('s3')->exists($file->path)) {
                //                 Storage::disk('s3')->delete($file->path);
                //             }
                //         });
                //     }
                // }
                // if ($request->hasFile('file')) {
                //     $files = $request->file('file');
                //     $error = false;
                //     $errorMessage = '';
                //     $fileRecord = null;
                //     if (!!$purchase_order->files) {
                //         $purchase_order->files->each(function ($file) {
                //             $file->delete();
                //             if (Storage::disk('s3')->exists($file->path)) {
                //                 Storage::disk('s3')->delete($file->path);
                //             }
                //         });
                //     }
                //     foreach ($files as $file) {
                //         try {
                //             $fileOriginalName = $file->getClientOriginalName();
                //             $filePath = $file->store('purchase_orders/id_' . $purchase_order->id . '/files', 's3');
    
                //             if (!$filePath) {
                //                 $this->addError("Failed to upload.", 400);
                //                 return false;
                //             }
    
                //             // other data
                //             $ext = pathinfo(config('filesystems.disks.local.root') . '/' . $filePath, PATHINFO_EXTENSION);
                //             $size = Storage::disk('s3')->getSize($filePath);
                //             $type = Storage::disk('s3')->getMimetype($filePath);
    
                //             $fileData = [
                //                 'original_name' => $fileOriginalName,
                //                 'path' => $filePath,
                //                 'ext' => $ext,
                //                 'size' => $size,
                //                 'type' => $type,
                //             ];
                //         } catch (FileNotFoundException $e) {
                //             $error = true;
                //             $errorMessage = $e->getMessage();
                //             break;
                //         }
                //         if (!$fileData) {
                //             $error = true;
                //             $errorMessage = $this->fileService->getErrors()->first();
                //             break;
                //         }
                //         $fileRecord = $purchase_order->files()->create([
                //             'name' => $fileData['original_name'],
                //             'uploaded_by' => Auth::user()->id,
                //             'file_type' => $fileData['type'],
                //             'extension' => $fileData['ext'],
                //             'size' => $fileData['size'],
                //             'path' => $fileData['path'],
                //         ]);
    
                //         if (!$fileRecord) {
                //             if (Storage::disk('s3')->exists($fileData['path'])) {
                //                 Storage::disk('s3')->delete($fileData['path']);
                //             }
                //             $error = true;
                //             $errorMessage = "Failed to create record.";
                //             break;
                //         }
    
                //     }
                //     if ($error) {
                //         return $this->sendResponseBadRequest($errorMessage);
                //     }
    
                // }
                $purchaseOrderUpdated = new PurchaseOrderCollection($purchase_order->refresh());
                return $this->sendResponseUpdated(compact('purchaseOrderUpdated'));
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

        $purchase = [
            'id' => $purchaseOrder->id,
            'purchase_number' => $purchaseOrder->purchase_number,
            'currency' => $purchaseOrder->currency,
            'estatus' => $purchaseOrder->estatus->only('id', 'key', 'title'),
            'elaborated' => $purchaseOrder->elaborated->only('id', 'name', 'email'),
            'supplier' => $purchaseOrder->supplier->only('id', 'code_equip', 'business_name', 'rfc', 'email', 'credit_days'),
            'amounts' => $purchaseOrder->only(
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
            'products' => $purchaseOrder->products->toArray(),
            'charges' => $purchaseOrder->charges->toArray(),
            'invoice' => [
                'metodo_pago' => $purchaseOrder->metodopago->toArray(),
                'uso_cfdi' => $purchaseOrder->usocfdi->toArray(),
                'forma_pago' => $purchaseOrder->formapago->toArray(),
            ],
            'purchase_concept' => $purchaseOrder->purchase_concept->only('id', 'name'),
            'purchase_type' => $purchaseOrder->purchaseType->only('id', 'name'),
            'ship' => $purchaseOrder->ship->only('id', 'title', 'address'),
            'observation' => $purchaseOrder->observation,
            'note' => $purchaseOrder->note,
            'payment_condition' => $purchaseOrder->payment_condition,
            'authorization_date' => $purchaseOrder->authorization_date,
        ];
        $item = Helpers::arrayToObject($purchase);
        // dd($item);
        $pdf = App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('pdf.purchase', compact('item'));
        return $pdf->stream();

    }
    public function updateEstatus(UpdateStatusPurchaseOrderRequest $request, PurchaseOrder $purchase_order)
    {
        return DB::transaction(
            function () use ($purchase_order, $request) {

                

                $estatus = Estatus::where('key', $request['estatus_key'])->first();
                if ($request['estatus_key'] == Estatus::ESTATUS_AUTORIZADO)
                    $purchase_order->authorization_date = Carbon::now();
                if ($request['estatus_key'] == Estatus::ESTATUS_DENEGAR)
                    $purchase_order->authorization_date = null;
                if ($request['estatus_key'] == Estatus::ESTATUS_PROGRAMAR_PAGO) {
                    if ($purchase_order->invoice()->exists()) {
                        foreach ($purchase_order->invoice as $invoice) {
                            $invoice->fill([
                                'date_to_pay' => null,
                                'payment_date' => null,
                            ])->update();
                        }
                    }
                    $purchase_order->date_to_pay = null;
                    $purchase_order->payment_date = null;
                }
                if ($request['estatus_key'] == Estatus::ESTATUS_POR_FACTURAR) {
                    if ($purchase_order->invoice()->exists()) {
                        foreach ($purchase_order->invoice as $invoice) {
                            $invoice->fill([
                                'date_to_pay' => null,
                                'payment_date' => null,
                            ])->update();
                        }
                    }
                    $purchase_order->date_to_pay = null;
                    $purchase_order->payment_date = null;
                }
                if ($request['estatus_key'] == Estatus::ESTATUS_POR_PAGAR) {
                    if ($purchase_order->invoice()->exists()) {
                        foreach ($purchase_order->invoice as $invoice) {
                            $invoice->fill([
                                'date_to_pay' => $request->get('date_to_pay', null),
                                'payment_date' => null,
                            ])->update();
                        }
                    }
                    $purchase_order->date_to_pay = $request->get('date_to_pay', null);
                    $purchase_order->payment_date = null;
                }
                if ($request['estatus_key'] == Estatus::ESTATUS_PAGADA_PORFACTURAR) {
                    if ($purchase_order->invoice()->exists()) {
                        foreach ($purchase_order->invoice as $invoice) {
                            $invoice->fill([
                                'payment_date' => $request['payment_date'],
                            ])->update();
                        }
                    }
                    $purchase_order->payment_date = $request['payment_date'];
                }

                $purchase_order->estatus()->associate($estatus);
                $purchase_order->updated_by = Auth::user()->id;
                
                $updated = $purchase_order->save();
                if (!$updated) {
                    return $this->sendResponseBadRequest("Error en la Actualizacion");
                }
                // $purchase_order->elaborated->notify(new PurchaseOrderUpdatedNotification($purchase_order->refresh()));
                return $this->sendResponseUpdated([], 'Estatus Actualizado');
            }
        );
    }

    public function destroyFile(FilePurchase $file)
    {

        // dd($file,Storage::disk('s3')->exists($file->path));
        try {
            $file->delete();
            if (Storage::disk('s3')->exists($file->path)) {
                Storage::disk('s3')->delete($file->path);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendResponseDeleted("Error de Servidor");
        }
        return $this->sendResponseDeleted("Archivo Eliminado");
    }

    public function attachFiles(Request $request, PurchaseOrder $purchase_order)
    {
        // dd($request->hasFile('file'), $request->file('file'));

        if ($request->hasFile('file')) {
            $files = $request->file('file');
            $error = false;
            $errorMessage = '';
            $fileRecord = null;
            foreach ($files as $file) {
                try {
                    $fileOriginalName = $file->getClientOriginalName();
                    $filePath = $file->store('purchase_orders/id_' . $purchase_order->id . '/files', 's3');

                    if (!$filePath) {
                        $this->addError("Failed to upload.", 400);
                        return false;
                    }

                    // other data
                    $ext = pathinfo(config('filesystems.disks.local.root') . '/' . $filePath, PATHINFO_EXTENSION);
                    $size = Storage::disk('s3')->getSize($filePath);
                    $type = Storage::disk('s3')->getMimetype($filePath);

                    $fileData = [
                        'original_name' => $fileOriginalName,
                        'path' => $filePath,
                        'ext' => $ext,
                        'size' => $size,
                        'type' => $type,
                    ];
                } catch (FileNotFoundException $e) {
                    $error = true;
                    $errorMessage = $e->getMessage();
                    break;
                }
                if (!$fileData) {
                    $error = true;
                    $errorMessage = $this->fileService->getErrors()->first();
                    break;
                }
                $fileRecord = $purchase_order->files()->create([
                    'name' => $fileData['original_name'],
                    'uploaded_by' => Auth::user()->id,
                    'file_type' => $fileData['type'],
                    'extension' => $fileData['ext'],
                    'size' => $fileData['size'],
                    'path' => $fileData['path'],
                ]);

                if (!$fileRecord) {
                    if (Storage::disk('s3')->exists($fileData['path'])) {
                        Storage::disk('s3')->delete($fileData['path']);
                    }
                    $error = true;
                    $errorMessage = "Failed to create record.";
                    break;
                }

            }
            if ($error) {
                return $this->sendResponseBadRequest($errorMessage);
            }

            return $this->sendResponseCreated();
        }
    }
}