<?php

namespace App\Http\Controllers\Admin;

use App\Components\Gps\Repositories\GpsChipsRepository;
use Illuminate\Http\Request;

class GpsChipController extends AdminController
{
    /**
     * @var GpsChipsRepository
     */
    private $gpsChipsRepository;

    /**
     * FileGroupController constructor.
     * @param GpsChipsRepository $gpsChipsRepository
     */
    public function __construct(GpsChipsRepository $gpsChipsRepository)
    {
        $this->gpsChipsRepository = $gpsChipsRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->has('order_by')) {
            $request['order_by'] = 'sim';
        }

        $data = $this->gpsChipsRepository->index($request->all());

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
            'sim' => 'required|string',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $chip = $this->gpsChipsRepository->create($request->all());

        if (!$chip) {
            return $this->sendResponseBadRequest("Failed to create.");
        }

        return $this->sendResponseCreated($chip);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $chip = $this->gpsChipsRepository->find($id);

        if (!$chip) {
            return $this->sendResponseNotFound();
        }

        return $this->sendResponseOk($chip);
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
            'sim' => 'required|string',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $updated = $this->gpsChipsRepository->update($id, $request->all());

        if (!$updated) {
            return $this->sendResponseBadRequest("Failed to update");
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
        $this->gpsChipsRepository->delete($id);

        return $this->sendResponseOk([], "Deleted.");
    }
}
