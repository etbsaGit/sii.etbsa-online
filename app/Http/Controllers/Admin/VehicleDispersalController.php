<?php

namespace App\Http\Controllers\Admin;

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Department;
use App\Components\Common\Models\Estatus;
use App\Components\Vehicle\Models\Vehicle;
use App\Components\Vehicle\Models\VehicleDispersal;
use App\Components\Vehicle\Models\VehicleFuel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Components\Vehicle\Repositories\VehicleDispersalRepository;
use App\Notifications\VehicleDispersalCreatedNotification;
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
        $vehicle_fuel = VehicleFuel::all('id', 'name', 'cost_lt');

        return $this->sendResponseOk(compact('vehicles', 'agencies', 'departments', 'vehicle_fuel'), "options to create dispersal");
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
            "drum_dispersion" => 'required',
            "ticket_card" => 'required',
            "fuel_liters" => 'required',
            "fuel_name" => 'required',
            "fuel_cost_liter" => 'required',
            "date_to_disperse" => 'required',
            "agency_id" => 'required',
            "department_id" => 'required'
        ]);

        // return $this->sendResponseCreated($request->all());
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        try {
            DB::transaction(function () use ($request) {
                $dispersal = $this->vehicleDispersalRepository->create($request->all());
                $dispersal->vehicle->update([
                    'fuel_odometer' => $request['fuel_odometer']
                ]);
                // $dispersal->solicitante->notify(new VehicleDispersalCreatedNotification($dispersal));
            });
        } catch (\Throwable $th) {
            return $this->sendResponseBadRequest($th);
        }

        return $this->sendResponseCreated();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VehicleDispersal  $vehicleDispersal
     * @return \Illuminate\Http\Response
     */
    public function show(VehicleDispersal $vehicle_dispersal)
    {

        $data = [
            'drum_dispersal' => $vehicle_dispersal->drum_dispersion,
            'fuel_liters' => $vehicle_dispersal->fuel_liters,
            'liters_cost' => $vehicle_dispersal->fuel_cost_liter,
            'fuel' => $vehicle_dispersal->fuel->name,
            'fecha_dispersar' => $vehicle_dispersal->date_to_disperse,
            'fecha_solicitud' => $vehicle_dispersal->created_at,
            'estatus_key' => $vehicle_dispersal->estatus->key,
            'estatus_text' => $vehicle_dispersal->estatus->title,
            'solicitante' => $vehicle_dispersal->solicitante->profiable->full_name ?? '',
            'agency' => $vehicle_dispersal->agency->title,
            'department' => $vehicle_dispersal->department->title,
            'ticket_card' => $vehicle_dispersal->ticketcard->ticket_card,
            'ticket_balance' => $vehicle_dispersal->ticketcard->account_balance,
            'dispatched_amount' => $vehicle_dispersal->dispatched_amount,
            'motivo' => $vehicle_dispersal->reason_dispersal,
            'motivo_detalle' => $vehicle_dispersal->reason_data,
            'motivo_descripcion' => $vehicle_dispersal->reason_note,
            'odometro' => $vehicle_dispersal->fuel_odometer ?? '',
            'tickets' => $vehicle_dispersal->tickets_info,
            'mileage_actual' => $vehicle_dispersal->mileage_actual,
            'mileage_last' => $vehicle_dispersal->mileage_last,
            'vehiculo' => [
                'matricula' => $vehicle_dispersal->vehicle->matricula ?? '',
                'serie' => $vehicle_dispersal->vehicle->serie ?? '',
                'modelo' => $vehicle_dispersal->vehicle->model ?? '',
                'kms_anterior' => $vehicle_dispersal->vehicle->mileage_last ?? '',
                'kms_actual' => $vehicle_dispersal->vehicle->mileage_actual ?? '',
                'odometro' => $vehicle_dispersal->vehicle->fuel_odometer ?? '',
                'rendimiento' => $vehicle_dispersal->vehicle->performance_fuel ?? 0,
            ],
        ];
        return $this->sendResponseOk($data);
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
                if ($vehicle_dispersal->estatus->key == Estatus::ESTATUS_DESPACHADO) {
                    $vehicle_dispersal->ticketcard->update([
                        'account_balance' => $vehicle_dispersal->ticketcard->account_balance + ($vehicle_dispersal->fuel_liters * $vehicle_dispersal->fuel_cost_liter)
                    ]);
                }
                if ($vehicle_dispersal->estatus->key == Estatus::ESTATUS_DISPERSADO) {
                    $vehicle_dispersal->vehicle->update([
                        'fuel_odometer' => $request['fuel_odometer'],
                        'mileage_last' =>
                        $vehicle_dispersal->mileage_actual
                    ]);
                    $vehicle_dispersal->ticketcard->update([
                        'account_balance' => $vehicle_dispersal->ticketcard->account_balance + ($vehicle_dispersal->fuel_liters * $vehicle_dispersal->fuel_cost_liter)
                    ]);
                }
            });
        } catch (\Throwable $th) {
            return $this->sendResponseBadRequest($th);
        }

        return $this->sendResponseUpdated();
    }
    public function changeEstatus(Request $request, $id)
    {
        $request['updated_by'] = Auth::user()->id;
        $validate = validator($request->all(), [
            "estatus_key" => 'required',
            "fuel_liters" => ['numeric'],
            "tickets_info" => ['array'],
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        DB::transaction(function () use ($id, $request) {
            switch ($request['estatus_key']) {
                case Estatus::ESTATUS_AUTORIZADO:
                    $this->vehicleDispersalRepository->dispersalAurotize($id, $request->all());
                    break;
                case Estatus::ESTATUS_DENGAR:
                    $this->vehicleDispersalRepository->dispersalDenied($id, $request->all());
                    break;
                case Estatus::ESTATUS_DISPERSADO:
                    $this->vehicleDispersalRepository->dispersalDisperse($id, $request->all());
                    break;
                case Estatus::ESTATUS_DESPACHADO:
                    $this->vehicleDispersalRepository->dispersalDispatched($id, $request->all());
                    break;
                default:
                    return $this->sendResponseBadRequest("Estatus No Valido");
                    break;
            }
            // $estatus = Estatus::where('key', $request['estatus_key'])->first();
            // $dispersal = $this->vehicleDispersalRepository->find($id);
            // // $dispersal['gas_lts'] = $request['gas_lts'];
            // $dispersal->estatus()->associate($estatus)->save();
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
