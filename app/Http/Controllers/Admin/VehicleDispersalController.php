<?php

namespace App\Http\Controllers\Admin;

use App\Components\Common\Models\Estatus;
use App\Components\Vehicle\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Components\Vehicle\Repositories\VehicleDispersalRepository;
use App\Components\Vehicle\Repositories\VehicleRepository;
use Illuminate\Support\Facades\DB;

class VehicleDispersalController extends AdminController
{

    private $vehicleDispersalRepository;

    /**
     * vehicleController constructor.
     * @param vehicleDispersalRepository $vehicleDispersalRepository
     */
    public function __construct(VehicleDispersalRepository $vehicleDispersalRepository)
    {
        $this->vehicleDispersalRepository = $vehicleDispersalRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->vehicleDispersalRepository->list(request()->all());

        return $this->sendResponseOk($data, "list dispersal vehicles ok.");
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
            "vehicle_id" => 'required',
            "kilometraje_actual" => 'required'
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $request['solicitante'] = Auth::user()->id;
        $request['estatus'] = Estatus::where('key', 'dispersal:pendiente')->first();
        DB::transaction(function () use ($request) {
            $dispersal = $this->vehicleDispersalRepository->create($request->all());
            $vehicle = new VehicleRepository(new Vehicle());
            $vehicle->update($request['vehicle_id'], [
                'ultimo_kilometraje' => $request['kilometraje_actual']
            ]);
            $dispersal->estatus()->associate($request['estatus'])->save();
        });

        return $this->sendResponseCreated([]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VehicleDispersal  $vehicleDispersal
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $dispersal = $this->vehicleDispersalRepository->find($id, ['vehicle', 'user', 'cargoA', 'estatus']);

        if (!$dispersal) {
            return $this->sendResponseNotFound();
        }

        return $this->sendResponseOk($dispersal);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VehicleDispersal  $vehicleDispersal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
    public function changeEstatus(Request $request, $id)
    {
        $validate = validator($request->all(), [
            "estatus_key" => 'required',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        DB::transaction(function () use ($id, $request) {
            $estatus = Estatus::where('key', $request['estatus_key'])->first();
            $dispersal = $this->vehicleDispersalRepository->find($id);
            // $dispersal['gas_lts'] = $request['gas_lts'];
            $dispersal->estatus()->associate($estatus)->save();
        });


        return $this->sendResponseOk([], "Estatus Actualizado Correctamente.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VehicleDispersal  $vehicleDispersal
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->vehicleDispersalRepository->delete($id);
        } catch (\Exception $e) {
            return $this->sendResponseBadRequest("Failed to delete");
        }

        return $this->sendResponseDeleted();
    }
}
