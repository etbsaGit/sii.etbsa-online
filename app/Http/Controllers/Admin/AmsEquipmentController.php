<?php

namespace App\Http\Controllers\Admin;

use App\Components\NT\Models\AmsEquipment;
use App\Components\NT\Repositories\AmsEquipmentRepository;
use App\Http\Controllers\Admin\AdminController;
use Auth;
use Illuminate\Http\Request;

class AmsEquipmentController extends AdminController
{
    private $amsEquipmentRepository;

    public function __construct(AmsEquipmentRepository $amsEquipmentRepository)
    {
        $this->amsEquipmentRepository = $amsEquipmentRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->amsEquipmentRepository->list(request()->all());
        return $this->sendResponseOk($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        $validate = validator($request->all(), []);
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $ams_equipment = $this->amsEquipmentRepository->create($request->all());
        if (!$ams_equipment) {
            return $this->sendResponseBadRequest("Failed create.");
        }
        return $this->sendResponseCreated($ams_equipment);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NT\AmsEquipment  $ams_equipment
     * @return \Illuminate\Http\Response
     */
    public function show(AmsEquipment $ams_equipment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NT\AmsEquipment  $ams_equipment
     * @return \Illuminate\Http\Response
     */
    public function edit(AmsEquipment $ams_equipment)
    {
        return $this->sendResponseOk($ams_equipment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NT\AmsEquipment  $ams_equipment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AmsEquipment $ams_equipment)
    {
        $request['updated_by'] = Auth::user()->id;
        $validate = validator($request->all(), []);
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $updated = $ams_equipment->update($request->all());
        if (!$updated) {
            return $this->sendResponseBadRequest("Failed update.");
        }
        return $this->sendResponseUpdated($updated);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NT\AmsEquipment  $ams_equipment
     * @return \Illuminate\Http\Response
     */
    public function destroy(AmsEquipment $ams_equipment)
    {
        $ams_equipment->delete();
        return $this->sendResponseDeleted();
    }

    public function resource()
    {
        // $categories = collect(['Arado', 'Rastra', 'Cultivadora', 'Niveladora', 'Sembradora', 'Fumigadora']);

        return $this->sendResponseOk();
    }
}
