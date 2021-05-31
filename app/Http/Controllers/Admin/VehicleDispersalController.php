<?php

namespace App\Http\Controllers\Admin;

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Department;
use App\Components\Common\Models\Estatus;
use App\Components\Vehicle\Models\Vehicle;
use App\Components\Vehicle\Models\VehicleDispersal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Components\Vehicle\Repositories\VehicleDispersalRepository;
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

    public function create()
    {
        $vehicles = Vehicle::query()->filterPermission(Auth::user())->get();
        $agencies = Agency::all('id', 'title');
        $departments = Department::all('id', 'title');

        return $this->sendResponseOk(compact('vehicles', 'agencies', 'departments'), "options to create dispersal");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['estatus_id'] = Estatus::where('key', Estatus::ESTATUS_PENDIENTE)->first()->id;
        $request['created_by'] = Auth::user()->id;
        $validate = validator($request->all(), [
            "vehicle_id" => 'required',
            "actual_mileage" => 'required',
            "reason" => 'required',
            "fuel_odometer" => 'required',
            "agency_id" => 'required',
            "department_id" => 'required'
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        // $dispersal = $this->vehicleDispersalRepository->create($request->all());
        // if (!$dispersal) {
        //     return $this->sendResponseBadRequest("Failed to create.");
        // }
        $dispersal = [];
        try {
            DB::transaction(function () use ($request, $dispersal) {
                $dispersal = $this->vehicleDispersalRepository->create($request->all());
                $vehicle = Vehicle::find($request['vehicle_id']);
                $vehicle->update(['fuel_odometer' => $request['fuel_odometer']]);
            });
        } catch (\Throwable $th) {
            return $this->sendResponseBadRequest($th);
        }

        return $this->sendResponseCreated($dispersal);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VehicleDispersal  $vehicleDispersal
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $dispersal = $this->vehicleDispersalRepository->find($id, ['vehicle', 'estatus']);

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
    public function update(Request $request, VehicleDispersal $vehicle_dispersal)
    {
        $request['updated_by'] = Auth::user()->id;
        $validate = validator($request->all(), [
            'estatus_key' => 'string',
            'updated_by' => 'required'
        ], []);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $request['estatus_id'] = Estatus::where('key', $request['estatus_key'])->first()->id;

        try {
            DB::transaction(function () use ($request, $vehicle_dispersal) {
                $vehicle_dispersal->update($request->all());
                $vehicle_dispersal->refresh();
                $vehicle = Vehicle::find($request['vehicle_id']);
                $vehicle->update([
                    'fuel_odometer' => $request['fuel_odometer'],
                    'last_mileage' =>  $vehicle_dispersal->estatus->key == Estatus::ESTATUS_DISPERSADO ?
                        $vehicle_dispersal->actual_mileage :
                        $vehicle_dispersal->last_mileage
                ]);
            });
        } catch (\Throwable $th) {
            return $this->sendResponseBadRequest("Ocurrio un Error al Crear la Dispercion");
        }

        return $this->sendResponseUpdated([]);
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
