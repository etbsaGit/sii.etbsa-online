<?php

namespace App\Http\Controllers\Admin;

use App\Components\Common\Models\Estatus;
use App\Components\Tracking\Repositories\TrackingRepository;
use App\Notifications\TrackingAssigned;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Components\User\Models\User;

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
            'description_topic' => 'required|string',
            'price' => 'numeric',
        ], [
            'title.required' => 'El Titulo es requerido',
            'description_topic.required' => 'Descripcion de seguimiento es Requerido',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $request['registered_by'] = Auth::user()->id;
        $request['assigned_by'] = Auth::user()->id;
        // $request['estatus_id'] = 1;

        /** @var Prospect $tracking */
        $tracking = $this->trackingRepository->create($request->all());

        if (!$tracking) {
            return $this->sendResponseBadRequest("Failed create.");
        }
        $estatus = Estatus::where('key', Estatus::ESTATUS_ACTIVO)->first();
        $tracking->estatus()->associate($estatus);
        $tracking->save();

        $attended_by = User::find($request['attended_by']);
        $attended_by->notify(new TrackingAssigned($tracking));

        return $this->sendResponseCreated($tracking);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tracking = $this->trackingRepository->find($id, [
            'prospect', 'estatus', 'attended_by',
            'agency', 'department', 'registered_by', 'assigned_by', 'historical',
        ]);

        if (!$tracking) {
            return $this->sendResponseNotFound();
        }

        return $this->sendResponseOk($tracking);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validate = validator($request->all(), [
            'message' => 'required|string',
        ], [
            'message.required' => 'El mensaje es requerido',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        //Evaluar Estatus

        $updated = false;
        DB::transaction(function () use ($id, $request, $updated) {
            $estatus = Estatus::where('key', $request['estatus'])->first();
            $tracking = $this->trackingRepository->find($id);

            $request['user_id'] = Auth::user()->id;
            $tracking->historical()->create($request->all());
            $tracking->date_next_tracking = $request['date_next_tracking'];
            $tracking->price = $request['last_price'];
            $tracking->estatus()->associate($estatus)->save();
            // $updated = $this->trackingRepository->update($id, $request->all());
        });

        return $this->sendResponseOk([], "Updated.");
    }
    public function assignSeller(Request $request, $id)
    {
        $validate = validator($request->all(), [
            'attended_by' => 'required',
        ], [
            'attended_by.required' => 'Vendedor es Requerido',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $request['assigned_by'] = Auth::user()->id;
        $updated = $this->trackingRepository->update($id, $request->all());

        if (!$updated) {
            return $this->sendResponseBadRequest("Failed update");
        }

        return $this->sendResponseOk([], "Se Asigno Vendedor Correctamente.");
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

    public function historical($id)
    {
        $tracking = $this->model->find($id);

        if (!$tracking) {
            return false;
        };

        $tracking->historical()->create($historical->toArray());
    }

    public function resources()
    {
        // $trackings = $this->trackingRepository->resource(request()->all());
        $agencies = DB::table('agencies')->get(['id', 'code', 'title']);
        $departments = DB::table('departments')->get(['id', 'title']);
        $prospects = DB::table('prospect')->get(['id', 'full_name']);
        // $type = DB::table('marketing_import')->distinct()->get(['SUCURSAL']);
        return $this->sendResponseOk(compact('agencies', 'departments', 'prospects'));
    }
}
