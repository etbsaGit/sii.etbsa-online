<?php

namespace App\Http\Controllers\Admin;

use App\Components\Common\Models\Estatus;
use App\Components\Tracking\Models\Prospect;
use App\Components\Tracking\Models\TrackingProspect;
use App\Components\Tracking\Repositories\TrackingRepository;
use App\Components\User\Models\User;
use App\Notifications\TrackingAssigned;
use App\Notifications\TrackingNewHistorical;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingProspectController extends AdminController
{
    /**
     * @var ProspectRepository
     */
    private $trackingRepository;

    /**
     * ProspectController constructor.
     * @param ProspectRepository $trackingRepository
     */
    public function __construct(TrackingRepository $trackingRepository)
    {
        $this->trackingRepository = $trackingRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->trackingRepository->listTracking(request()->all());
        return $this->sendResponseOk($data, "list tracking ok.");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = validator($request->all(), [
            'title' => 'required|string',
            'reference' => 'required|string',
            'description_topic' => 'required|string',
            'price' => 'required|numeric',
            'agency_id' => 'required',
            'department_id' => 'required',
            'attended_by' => 'required',
            'prospect_id' => 'required',
        ], [
            'description_topic.required' => 'Motivo del seguimiento es Requerido',
            'attended_by.required' => 'Seleccione a un Vendedor',
            'reference.required' => 'Especifique una referencia',
            'title.required' => 'Especifique una Categoria',
            'agency_id.required' => 'Agencia Asignada es Requerido',
            'department_id.required' => 'A quien Corresponde es Requerido',
            'prospect_id.required' => 'El Prospecto es Requerido',
            'price.required' => 'El Precio es Requerido',
            'price.numeric' => 'El Precio debe ser un Numero Valido',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        DB::transaction(function () use ($request) {
            $request['registered_by'] = Auth::user()->id;
            $request['assigned_by'] = Auth::user()->id;

            /** @var Prospect $tracking */
            $created = $this->trackingRepository->create($request->all());
            $tracking = $this->trackingRepository->find($created->id);

            if (!$created) {
                return $this->sendResponseBadRequest("Failed create.");
            }
            $estatus = Estatus::where('key', Estatus::ESTATUS_ACTIVO)->first();
            $tracking->estatus()->associate($estatus);
            $tracking->save();

            $tracking->attended->notify(new TrackingAssigned($tracking));
        });

        return $this->sendResponseCreated([], 'Se Registro Nuevo Seguimiento');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TrackingProspect $tracking)
    {
        $data = [
            'detail' => $tracking->only(
                'id',
                'title',
                'reference',
                'description_topic',
                'price',
                'currency',
                'first_contact',
                'assertiveness',
                'invoice',
                'tracking_condition',
                'created_at'
            ),
            'agency' => $tracking->agency->title,
            'department' => $tracking->department->title,
            'attended_by' => $tracking->attended->name,
            'assigned_by' => $tracking->assigned->name,
            'registered_by' => $tracking->registered->name,
            'estatus' => $tracking->estatus->only('id', 'title', 'key'),
            'prospect' => $tracking->prospect()->with('township')->first()
                ->only('full_name', 'email', 'is_moral', 'company', 'rfc', 'town', 'phone', 'township'),
            'historical' => $tracking->historical->map(function ($H) {
                return [
                    'id' => $H->id,
                    'message' => $H->message,
                    'last_price' => $H->last_price,
                    'last_currency' => $H->last_currency,
                    'last_assertiveness' => $H->last_assertiveness,
                    'invoice' => $H->invoice,
                    'type_contacted' => $H->type_contacted,
                    'user' => $H->user->name,
                    'created_at' => $H->created_at,
                ];
            }),
        ];

        if (!$tracking) {
            return $this->sendResponseNotFound();
        }

        return $this->sendResponseOk($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addHistoricalTracking(Request $request, $id)
    {

        $validate = validator($request->all(), [
            'estatus' => 'required|string',
            'message' => 'required|string',
            'currency' => 'required',
            'assertiveness' => 'required|numeric',
            'last_assertiveness' => 'required|numeric',
            'date_next_tracking' => ['required_if:estatus,"activo"|date'],
            'invoice' => ['exclude_unless:estatus,"fomalizado"|required'],
        ], [
            'message.required' => 'El mensaje es requerido',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        DB::transaction(function () use ($id, $request) {
            $estatus = Estatus::where('key', $request['estatus'])->first();
            $tracking = $this->trackingRepository->find($id);

            $request['user_id'] = Auth::user()->id;
            $tracking->historical()->create($request->all());
            $this->trackingRepository->update($id, $request->all());
            $tracking->estatus()->associate($estatus)->save();

            $tracking->attended->notify(new TrackingNewHistorical($tracking));
        });

        return $this->sendResponseOk([], "Seguimiento Guardado.");
    }

    public function edit(TrackingProspect $tracking)
    {
        $data = $tracking->only(
            'id',
            'price',
            'title',
            'currency',
            'reference',
            'agency_id',
            'prospect_id',
            'attended_by',
            'department_id',
            'first_contact',
            'description_topic',
            'tracking_condition',
        );

        if (!$tracking) {
            return $this->sendResponseNotFound();
        }

        return $this->sendResponseOk($data);
    }

    public function update(Request $request, $id)
    {
        $validate = validator($request->all(), [
            'title' => 'required|string',
            'reference' => 'required|string',
            'description_topic' => 'required|string',
            'price' => 'required|numeric',
            'agency_id' => 'required',
            'department_id' => 'required',
            'attended_by' => 'required',
            'prospect_id' => 'required',
        ], [
            'description_topic.required' => 'Motivo del seguimiento es Requerido',
            'attended_by.required' => 'Seleccione a un Vendedor',
            'reference.required' => 'Especifique una referencia',
            'title.required' => 'Especifique una Categoria',
            'agency_id.required' => 'Agencia Asignada es Requerido',
            'department_id.required' => 'A quien Corresponde es Requerido',
            'prospect_id.required' => 'El Prospecto es Requerido',
            'price.required' => 'El Precio es Requerido',
            'price.numeric' => 'El Precio debe ser un Numero Valido',
        ]);


        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        DB::transaction(function () use ($id, $request) {
            $this->trackingRepository->update($id, $request->all());
        });

        return $this->sendResponseOk([], "Se actualizo el Seguimiento.");
    }


    public function resetToActive(Request $request, $id)
    {
        $updated = $this->trackingRepository->update($id, ['estatus_id' => 1]);

        if (!$updated) {
            return $this->sendResponseBadRequest("Failed update");
        }

        return $this->sendResponseOk([], "Se Asigno el Estatus a Activo");
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function resources()
    {
        if (Auth::user()->isSuperUser()) {
            $agencies = DB::table('agencies')->get(['id', 'code', 'title']);
            $departments = DB::table('departments')->get(['id', 'title']);
        } else {
            $agencies = Auth::user()->seller_agency->map(function ($i, $k) {
                return ['id' => $i->id, 'code' => $i->code, 'title' => $i->title];
            });
            $departments = Auth::user()->seller_type->map(function ($i, $k) {
                return ['id' => $i->id, 'title' => $i->title];
            });
        }
        $prospects = Prospect::with('township')->get()->map->only('id', 'full_name', 'email', 'company', 'rfc', 'town', 'phone', 'township');
        return $this->sendResponseOk(compact('agencies', 'departments', 'prospects'));
    }
}
