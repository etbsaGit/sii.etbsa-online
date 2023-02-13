<?php

namespace App\Http\Controllers\Admin;

use App\Components\Tracking\Models\TrackingProspect;
use App\Components\User\Models\User;
use App\Notifications\MessageNotification;
use App\Notifications\MessageTrackingSent;
use Illuminate\Http\Request;
use Auth;

class TrackingProspectMessageController extends AdminController
{
    public function index(TrackingProspect $tracking)
    {
        $data = $tracking->messages;
        return $this->sendResponseOk($data, "List of Messsages Tracking");
    }

    public function store(Request $request, TrackingProspect $tracking)
    {
        $request['recipient_id'] = $tracking->attended->id;
        $request['sender_id'] = Auth::user()->id;
        $validate = validator($request->all(), [
            'body' => 'Array',
            'sender_id' => 'required',
            'recipient_id' => 'required',
        ]);

        if ($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        $message = $tracking->messages()->create($request->all());
        // Enviar Notificacion
        $recipient = User::find($request['recipient_id']);
        if ($recipient->id != auth()->id()) {
            // $recipient->notify(
            //     new MessageNotification($message, auth()->user())
            // );
            $recipient->notify(
                new MessageTrackingSent($message, auth()->user())
            );
        }

        return $this->sendResponseOk([$message, $recipient], "Add Message to Tracking");
    }
}
