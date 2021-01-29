<?php

namespace App\Http\Controllers\Admin;

use App\Components\Tracking\Repositories\ProspectRepository;
use Illuminate\Http\Request;
use Auth;

class ProspectController extends AdminController
{

    /**
     * @var ProspectRepository
     */
    private $prospectRepository;

    /**
     * ProspectController constructor.
     * @param ProspectRepository $prospectRepository
     */
    public function __construct(ProspectRepository $prospectRepository)
    {
        $this->prospectRepository = $prospectRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->prospectRepository->listProspect(request()->all());
        return $this->sendResponseOk($data, "list prospect ok.");
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
            'full_name' => 'required',
            'township_id' => 'required',
            'phone' => 'size:10|required|unique:prospect,phone',
        ], [
            'phone.unique' => 'El Telefono ya se encuentra registrado',
            'phone.size' => 'el Telefono debe tener 10 Digitos',
            'phone.required' => 'El Telefono es requerido',
            'township_id.required' => 'El Municipio es requerido',
            'full_name.required' => 'El Nombre es requerido',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $request['registered_by'] = Auth::user()->id;

        /** @var Prospect $prospect */
        $prospect = $this->prospectRepository->create($request->all());

        if (!$prospect) {
            return $this->sendResponseBadRequest("Failed create.");
        }

        return $this->sendResponseCreated($prospect);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $prospect = $this->prospectRepository->find($id, []);

        if (!$prospect) {
            return $this->sendResponseNotFound();
        }

        return $this->sendResponseOk($prospect);
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
            'full_name' => 'required',
        ]);

        if ($validate->fails()) return $this->sendResponseBadRequest($validate->errors()->first());

        $payload = $request->all();

        $updated = $this->prospectRepository->update($id, $payload);

        if (!$updated) return $this->sendResponseBadRequest("Failed update");
        return $this->sendResponseUpdated();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try {
            $this->prospectRepository->delete($id);
        } catch (\Exception $e) {
            return $this->sendResponseBadRequest("Failed to delete");
        }

        return $this->sendResponseDeleted();
    }
}
