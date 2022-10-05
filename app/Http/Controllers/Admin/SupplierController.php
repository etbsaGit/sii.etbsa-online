<?php

namespace App\Http\Controllers\Admin;

use App;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Auth;

use App\Components\Purchase\Repositories\SupplierRepository;
use App\Components\Purchase\Models\Supplier;
use App\Exports\SupplierExport;
use Illuminate\Validation\Rule;

class SupplierController extends AdminController
{
    /**
     * @var SupplierRepository
     */
    private $supllierRepository;

    /**
     * UserController constructor.
     * @param SupplierRepository $supllierRepository
     */
    public function __construct(SupplierRepository $supllierRepository)
    {
        $this->supllierRepository = $supllierRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->supllierRepository->list(request()->all());
        return $this->sendResponseOk($data, "list suppliers ok.");
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
            'business_name' => 'required|unique:suppliers,business_name',
            'rfc' => 'required|min:12|unique:suppliers,rfc',
        ], [
            'rfc.unique' => 'El RFC ya existe en un Registro',
            'rfc.min' => 'RFC debe ser valido'
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $supplier = $this->supllierRepository->create($request->all());

        if (!$supplier) {
            return $this->sendResponseBadRequest("Failed to create.");
        }

        return $this->sendResponseCreated($supplier);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        //
    }

    public function edit(Supplier $supplier)
    {
        return $this->sendResponseOk($supplier, "Edit Supplier.");
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {

        $validate = validator(
            $request->all(),
            [
                'business_name' =>
                ['required', Rule::unique('suppliers')->ignore($supplier->id)],
                'rfc' =>
                ['required', 'min:12', Rule::unique('suppliers')->ignore($supplier->id)],
            ],
            [
                'rfc.unique' => 'El RFC ya existe en un Registro',
                'rfc.min' => 'RFC debe ser valido'
            ]
        );
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $updated = $supplier->update($request->all());
        if (!$updated) {
            return $this->sendResponseBadRequest("Error en la Actualizacion");
        }

        return $this->sendResponseUpdated();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        //
    }
}
