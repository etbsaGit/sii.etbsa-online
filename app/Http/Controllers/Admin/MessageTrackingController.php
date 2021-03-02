<?php

namespace App\Http\Controllers\Admin;

use App\Components\Tracking\Models\MessageTracking;
use App\Components\Tracking\Models\TrackingProspect;
use App\Components\User\Models\Group;
use App\Components\User\Models\User;
use App\Notifications\MessageTrackingSent;
use Illuminate\Http\Request;

class MessageTrackingController extends AdminController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = validator($request->all(), [
            'recipient_id' => 'required|exists:users,id',
            'tracking_id' => 'required|exists:tracking_prospect,id',
            'msg' => 'required|string',

        ], [
            'msg.required' => 'Mensaje es Requerido',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $message = MessageTracking::create([
            'recipient_id' => $request->recipient_id,
            'tracking_id' => $request->tracking_id,
            'msg' => $request->msg,
            'sender_id' => auth()->id()
        ]);

        $recipient = User::find($request->recipient_id);
        if ($recipient->id != auth()->id()) {
            $recipient->notify(
                new MessageTrackingSent($message, auth()->user())
            );
        } else {
            $group_id = Group::where('name', 'Gerente')->first('id')->id;
            $Users = User::ofGroups([$group_id])
                ->ofSellerAgency([auth()->user()->agency_id])
                ->ofSellerType([auth()->user()->department_id])
                ->get();

            foreach ($Users as $user) {
                $user->notify(
                    new MessageTrackingSent($message, auth()->user())
                );
            }
        }

        return $this->sendResponseOk([], 'mensaje Enviado');
    }

    public function getMessagesTracking(TrackingProspect $tracking)
    {
        $messages = $tracking->messages;

        return $this->sendResponseOk(compact('messages'), 'messages del seguimiento');
    }
}
