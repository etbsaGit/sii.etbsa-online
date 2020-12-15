<?php

namespace App\Http\Controllers\Admin;

use App\Components\Vehicle\Models\Vehicle;
use App\Components\Vehicle\Repositories\VehicleRepository;
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
        $validate = validator($request->all(), [
        ]);

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
        $vehicle = $this->vehicleRepository->find($id, ['user','agency']);

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
    public function update(Request $request, $id)
    {
        $validate = validator($request->all(), [
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $payload = $request->all();

        $updated = $this->vehicleRepository->update($id, $payload);

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
}
