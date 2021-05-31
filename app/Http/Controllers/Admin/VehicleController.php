<?php

namespace App\Http\Controllers\Admin;

use App\Components\Vehicle\Models\Vehicle;
use App\Components\Vehicle\Repositories\VehicleRepository;
use DB;
use Illuminate\Http\Request;

class VehicleController extends AdminController
{

    private $vehicleRepository;

    /**
     * vehicleController constructor.
     * @param vehicleRepository $vehicleRepository
     */
    public function __construct(VehicleRepository $vehicleRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->vehicleRepository->list(request()->all());

        return $this->sendResponseOk($data, "list vehicles ok.");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = validator($request->all(), []);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $vehicle = $this->vehicleRepository->create($request->all());

        if (!$vehicle) {
            return $this->sendResponseBadRequest("Failed create.");
        }

        return $this->sendResponseCreated($vehicle);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vehicle = $this->vehicleRepository->find($id, ['dispersals.vehicle', 'services']);
        if (!$vehicle) {
            return $this->sendResponseNotFound();
        }
        return $this->sendResponseOk($vehicle);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $validate = validator($request->all(), [
            'matricula' => 'required',
            'model' => 'required',
            'brand' => 'required',
            'serie' => 'required',
            'year' => 'required',
            'fuel' => 'required',

            // 'actual_mileage',
            // 'fuel_odometer',
            // 'last_mileage',
            // 'max_lts_fuel',
            // 'mileage_last_service',
            // 'mileage_range_service',
            // 'ticket_card',
            // 'agency_id',
            // 'user_id',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $updated = $vehicle->update($request->all());

        if (!$updated) {
            return $this->sendResponseBadRequest("Failed update");
        }

        return $this->sendResponseUpdated();
    }

    public function assignedUser(Request $request, Vehicle $vehicle)
    {
        $validate = validator($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $updated = $vehicle->update($request->all());

        if (!$updated) {
            return $this->sendResponseBadRequest("Failed update");
        }

        return $this->sendResponseUpdated();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->vehicleRepository->delete($id);
        } catch (\Exception $e) {
            return $this->sendResponseBadRequest("Failed to delete");
        }

        return $this->sendResponseDeleted();
    }

    public function options()
    {
        $users = DB::table('users')->get(['id', 'name']);
        $agencies = DB::table('agencies')->get(['id', 'code', 'title']);
        return $this->sendResponseOk(compact(
            'users',
            'agencies'
        ), "list Resources orders ok.");
    }
}
