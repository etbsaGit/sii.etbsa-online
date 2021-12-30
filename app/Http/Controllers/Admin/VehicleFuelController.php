<?php

namespace App\Http\Controllers\Admin;

use App\Components\Vehicle\Models\VehicleFuel;
use App\Components\Vehicle\Repositories\VehicleFuelRepository;
use Illuminate\Http\Request;

class VehicleFuelController extends AdminController
{
    private $vehicleFuelRepository;
    /**
     * vehicleController constructor.
     * @param vehicleFuelRepository $vehicleFuelRepository
     */
    public function __construct(VehicleFuelRepository $vehicleFuelRepository)
    {
        $this->vehicleFuelRepository = $vehicleFuelRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->vehicleFuelRepository->list(request()->all());
        return $this->sendResponseOk($data, "list vehicle fuels config");
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
            'name' => 'required',
            'cost_lt' => 'required',
        ]);
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $vehicle_fuel = $this->vehicleFuelRepository->create($request->all());
        if (!$vehicle_fuel) {
            return $this->sendResponseBadRequest("Failed create.");
        }
        return $this->sendResponseCreated($vehicle_fuel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VehicleFuel $vehicles_fuel)
    {
        $validate = validator($request->all(), []);
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $updated = $vehicles_fuel->update($request->all());
        // $updated = $this->vehicleFuelRepository->update($vehicle_fuel,$request->all());
        if (!$updated) {
            return $this->sendResponseBadRequest("Failed update");
        }
        return $this->sendResponseUpdated();
    }
}
