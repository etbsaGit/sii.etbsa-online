<?php

namespace App\Http\Controllers\Admin;

use App\Components\Common\Models\Estatus;
use App\Components\Vehicle\Models\Vehicle;
use App\Components\Vehicle\Repositories\VehicleServicesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleServiceController extends AdminController
{

    private $vehicleServicesrepository;

    /**
     * vehicleController constructor.
     * @param vehicleServicesrepository $vehicleServicesrepository
     */
    public function __construct(VehicleServicesRepository $vehicleServicesrepository)
    {
        $this->vehicleServicesrepository = $vehicleServicesrepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->vehicleServicesrepository->list(request()->all());

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
        $request['created_by'] = Auth::user()->id;
        $request['estatus_id'] = Estatus::where('key', Estatus::ESTATUS_PENDIENTE)->first()->id;

        $validate = validator($request->all(), [
            "vehicle_id" => 'required',
            "agency_id" => 'required',
            "department_id" => 'required',
            "reason" => 'string|required',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $service = $this->vehicleServicesrepository->create($request->all());
        // DB::transaction(function () use ($request) {
        //     $vehicle = new VehicleRepository(new Vehicle());
        //     $vehicle->update($request['vehicle_id'], [
        //         'ultimo_kilometraje_servicio' => $request['kilometraje_servicio']
        //     ]);
        //     $dispersal->estatus()->associate($request['estatus'])->save();
        // });

        return $this->sendResponseCreated($service);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VehicleService  $vehicleService
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service = $this->vehicleServicesrepository->find($id, ['vehicle:id,matricula,model,year']);

        if (!$service) {
            return $this->sendResponseNotFound();
        }

        return $this->sendResponseOk($service);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VehicleService  $vehicleService
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
            $services = $this->vehicleServicesrepository->find($id);

            $services->estatus()->associate($estatus)->save();
        });


        return $this->sendResponseOk([], "Estatus Actualizado Correctamente.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VehicleService  $vehicleService
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->vehicleServicesrepository->delete($id);
        } catch (\Exception $e) {
            return $this->sendResponseBadRequest("Failed to delete");
        }

        return $this->sendResponseDeleted();
    }

    public function create()
    {
        $vehicles = Vehicle::all('id', 'matricula', 'last_mileage');
        $agencies = DB::table('agencies')->get(['id', 'code', 'title']);
        $departments = DB::table('departments')->get(['id', 'code', 'title']);
        return $this->sendResponseOk(compact(
            'vehicles',
            'agencies',
            'departments'
        ), "Create form Option to Vehicle Services.");
    }
}
