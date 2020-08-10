<?php

namespace App\Http\Controllers\Admin;

use App\Components\Gps\Models\GpsChips;
use App\Components\Gps\Repositories\GpsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GpsController extends AdminController
{
    /**
     * @var GpsRepository
     */
    private $gpsRepository;

    /**
     * FileGroupController constructor.
     * @param GpsRepository $gpsRepository
     */
    public function __construct(GpsRepository $gpsRepository)
    {
        $this->gpsRepository = $gpsRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $this->gpsRepository->index($request->all());

        return $this->sendResponseOk($data);
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
            'name' => 'required|string',
        ], [
            'name.required' => 'El campo nombre es obligatorio',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $file = $this->gpsRepository->create($request->all());

        if (!$file) {
            return $this->sendResponseBadRequest("Failed to create.");
        }

        return $this->sendResponseCreated($file);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $file = $this->gpsRepository->find($id, ['chip']);

        if (!$file) {
            return $this->sendResponseNotFound();
        }

        return $this->sendResponseOk($file);
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
            'name' => 'required|string',
            'amount' => 'numeric',
        ], [
            'name.required' => 'El campo nombre es obligatorio',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $gps = $this->gpsRepository->find($id, ['chip']);

        if (!$request->has('gps_group_id')) {
            $request['gps_group_id'] = null;
            $gps->gpsGroup()->dissociate();
        }
        if ($request->gps_chip_id == null) {
            $chip = GpsChips::find($gps->gps_chip_id);
            $chip->gps()->dissociate();
            $chip->save();
        }

        if ($request->has('renew')) {
            $renew = new Carbon($request->renew_date);
            $renew->setYear(Carbon::now()->year);
            $request['renew_date'] = $renew->addYear();
            if (!$renew) {
                return $this->sendResponseBadRequest("Fallo en Renovacion");
            }
        }

        $updated = $this->gpsRepository->update($id, $request->all());

        if (!$updated) {
            return $this->sendResponseBadRequest("Failed to update");
        }

        if ($request->gps_chip_id != null) {
            $chip = GpsChips::find($request['gps_chip_id']);
            $chip->gps()->associate($gps);
            $chip->save();
        }

        return $this->sendResponseOk([], "Updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->gpsRepository->delete($id);

        return $this->sendResponseOk([], "Deleted.");
    }

}
