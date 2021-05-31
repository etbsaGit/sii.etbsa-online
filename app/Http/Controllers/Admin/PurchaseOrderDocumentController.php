<?php

namespace App\Http\Controllers\Admin;

use App\Components\Common\Models\Document;
use App\Components\Common\Models\Estatus;
use App\Components\Purchase\Models\PurchaseOrder;
use App\Notifications\PurchaseOrderUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PurchaseOrderDocumentController extends AdminController
{

    public function index(PurchaseOrder $purchaseOrder)
    {
        $data = $purchaseOrder->documents;
        return $this->sendResponseOk($data, "List of document Purchase Order");
    }

    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {

        $validate = validator($request->all(), [
            'file_info' => 'Array',
            'uuid' => 'unique:documents,uuid',
        ]);

        if ($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        $document = $purchaseOrder->documents()->create($request->all());
        // Cambiar Estatus PurchaseOrder
        $estatus = Estatus::where('key', Estatus::ESTATUS_FACTURADO)->first();
        $purchaseOrder->update([
            'estatus_id' => $estatus->id
        ]);
        $purchaseOrder->estatus()->associate($estatus);
        $purchaseOrder->save();
        // Enviar Notificacion
        $purchaseOrder->elaborated->notify(new PurchaseOrderUpdatedNotification($purchaseOrder->refresh()));
        return $this->sendResponseOk($document, "Add document to Purchase Order");
    }
    public function destroy(Document $document)
    {
        $document->delete();
        // Cambiar Estatus PurchaseOrder
        // Enviar Notificacion
        return $this->sendResponseOk([], "Add document to Purchase Order");
    }
}
