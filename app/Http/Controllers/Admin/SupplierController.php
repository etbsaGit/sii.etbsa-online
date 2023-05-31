<?php

namespace App\Http\Controllers\Admin;

use App;
use App\Components\Common\Models\Estatus;
use App\Components\Requirements\Models\RequirementDocuments;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Auth;

use App\Components\Purchase\Repositories\SupplierRepository;
use App\Components\Purchase\Models\Supplier;
use App\Exports\SupplierExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SupplierController extends AdminController
{
    /**
     * @var SupplierRepository
     */
    private $supplierRepository;

    /**
     * UserController constructor.
     * @param SupplierRepository $supplierRepository
     */
    public function __construct(SupplierRepository $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->supplierRepository->list(request()->all());
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
            'code_equip' => 'required|unique:suppliers,code_equip',
            'business_name' => 'required|unique:suppliers,business_name',
            'rfc' => 'required|min:12|unique:suppliers,rfc',
        ], [
                'code_equip.required' => 'La Clave de Equip es Obligatoria',
                'code_equip.unique' => 'La Clave de Equip esta Duplicada',
                'rfc.unique' => 'El RFC ya existe en un Registro',
                'rfc.min' => 'RFC debe ser valido'
            ]);


        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        return DB::transaction(function ($result) use ($request) {

            $supplier = $this->supplierRepository->create($request->all());
            if (!$supplier) {
                return $this->sendResponseBadRequest("Failed to create.");
            }
            $this->supplierRepository->syncDocuments($supplier, $request->documents);

            return $this->sendResponseCreated([$supplier]);
        });

    }

    public function create()
    {
        $requirements = $this->supplierRepository->getSupplierRequirementDefault();
        return $this->sendResponse(compact('requirements'));
    }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  \App\Supplier  $supplier
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show(Supplier $supplier)
    // {
    //     //
    // }

    public function edit(Supplier $supplier)
    {
        $supplier->documents = $this->supplierRepository->getSupplierDocuments($supplier);
        unset($supplier->requirements);
        return $this->sendResponseOk(compact('supplier'), "Recurso Encontrado");
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Components\Purchase\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {

        $validate = validator(
            $request->all(),
            [
                'code_equip' =>
                ['required', Rule::unique('suppliers')->ignore($supplier->id)],
                'business_name' =>
                ['required', Rule::unique('suppliers')->ignore($supplier->id)],
                'rfc' =>
                ['required', 'min:12', Rule::unique('suppliers')->ignore($supplier->id)],
            ],
            [
                'code_equip.required' => 'La Clave de Equip es Obligatoria',
                'code_equip.unique' => 'La Clave de Equip esta Duplicada',
                'rfc.unique' => 'El RFC ya existe en un Registro',
                'rfc.min' => 'RFC debe ser valido'
            ]
        );
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        return DB::transaction(function ($result) use ($request, $supplier) {
            $updated = $supplier->update($request->all());
            if (!$updated) {
                return $this->sendResponseBadRequest("Error en la Actualizacion");
            }
            $supplier->refresh();
            $syncDocument = [];
            if ($documents = $request->documents ?? []) {
                foreach ($documents as $index => $document) {
                    if ($document) {
                        $current_path = $supplier->requirements()
                            ->wherePivot('requirement_documents_id', $document['id'])
                            ->first()->pivot->file_path ?? null;
                        $newFile = $document['file'] ?? null;
                        $pivot = [
                            'file_path' => !!$newFile ?
                            $document['file']->store('supplier/id_' . $supplier->id . '/' . $document['key'], 's3')
                            : $current_path ?? null,
                            'status_id' => Estatus::where('key', $document['status_key'])->first()->id,
                            'date_due' => !!$newFile ? Carbon::now()->addMonths(3) : $document['date_due'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        // delete old file if exists and if has new File or if status document is none
                        if ((Storage::disk('s3')->exists($current_path) && !!$newFile) || $document['status_key'] == 'documento.none') {
                            Storage::disk('s3')->delete($current_path);
                            $pivot['file_path'] = null;
                        }
                        $syncDocument[$document['id']] = $pivot;
                    }
                }
                $supplier->requirements()->sync($syncDocument);
            }
            return $this->sendResponseUpdated();
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Components\Purchase\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return $this->sendResponseDeleted();
    }
}