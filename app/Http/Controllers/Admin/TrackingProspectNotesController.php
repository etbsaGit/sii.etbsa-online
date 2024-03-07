<?php

namespace App\Http\Controllers\Admin;

use App\Components\Common\Models\Notes;
use App\Components\Tracking\Models\TrackingProspect;
use Illuminate\Http\Request;
use Auth;

class TrackingProspectNotesController extends AdminController
{
    public function index(TrackingProspect $tracking)
    {
        $data = $tracking->notes->load('createdBy');
        return $this->sendResponseOk($data, "List of Notes Tracking");
    }

    public function store(Request $request, TrackingProspect $tracking)
    {
        $request['created_by'] = Auth::user()->id;
        $validate = validator($request->all(), [
            'body' => 'required|string',
        ]);

        if ($validate->fails())
            return $this->sendResponseBadRequest($validate->errors()->first());


        $message = $tracking->notes()->create($request->all());

        return $this->sendResponseOk(compact('message'), "Add Note to Tracking");
    }

    public function destroy(Notes $note)
    {
        $note->delete();

        return $this->sendResponseDeleted();

    }
}
