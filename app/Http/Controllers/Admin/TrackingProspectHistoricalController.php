<?php

namespace App\Http\Controllers\Admin;

use App\Components\Common\Models\Estatus;
use App\Components\Tracking\Models\TrackingProspect;
use App\Notifications\TrackingNewHistorical;
use Auth;
use DB;
use Illuminate\Http\Request;

class TrackingProspectHistoricalController extends AdminController
{
    public function store(Request $request, TrackingProspect $tracking)
    {
        $request['user_id'] = Auth::user()->id;
        $validate = validator($request->all(), [
            'estatus' => 'required|string',
            'message' => 'required|string',
            'type_contacted' => ['required_if:estatus,"activo"|string'],
            'assertiveness' => ['required_if:estatus,"activo"|numeric'],
            'price' => ['required_if:estatus,"activo"|numeric'],
            'currency' => ['required_if:estatus,"activo"'],
            'last_assertiveness' => ['required_if:estatus,"activo"|numeric'],
            'last_price' => ['required_if:estatus,"activo"|numeric'],
            'last_currency' => ['required_if:estatus,"activo"'],
            'date_next_tracking' => ['required_if:estatus,"activo"|date'],
            'date_lost_sale' => ['required_if:estatus,"finalizado"|date'],
            'invoice' => ['exclude_unless:estatus,"fomalizado"|required'],
            'date_invoice' => ['exclude_unless:estatus,"fomalizado"|date'],
            'date_won_sale' => ['exclude_unless:estatus,"fomalizado"|date'],
        ], [
            'message.required' => 'El mensaje es requerido',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        DB::transaction(function () use ($tracking, $request) {
            $estatus = Estatus::where('key', $request['estatus'])->first();
            $tracking->historical()->create($request->all());
            $tracking->update($request->all());
            $tracking->estatus()->associate($estatus)->save();

            $tracking->attended->notify(new TrackingNewHistorical($tracking));
        });

        return $this->sendResponseOk([], "Seguimiento Guardado.");
    }
}
