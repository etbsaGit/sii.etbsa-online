<?php

namespace App\Http\Controllers\Admin;

use App\Components\Purchase\Models\PurchaseOrder;
use App\Components\User\Models\User;
use App\Notifications\PurchaseOrderMessageNotification;
use Auth;
use Illuminate\Http\Request;

class PurchaseOrderMessageController extends AdminController
{
    public function index(PurchaseOrder $purchaseOrder)
    {
        $data = $purchaseOrder->messages;
        return $this->sendResponseOk($data, "List of Messsages Purchase Order");
    }

    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request['recipient_id'] = $purchaseOrder->elaborated->id;
        $request['sender_id'] = Auth::user()->id;
        $validate = validator($request->all(), [
            'body' => 'Array',
            'sender_id' => 'required',
            'recipient_id' => 'required',
        ]);

        if ($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        $message = $purchaseOrder->messages()->create($request->all());
        // Enviar Notificacion
        $recipient = User::find($request['recipient_id']);
        // if ($recipient->id != auth()->id()) {
        $recipient->notify(
            new PurchaseOrderMessageNotification($message, auth()->user())
        );
        // }

        return $this->sendResponseOk($message, "Add Message to Purchase Order");
    }
}
