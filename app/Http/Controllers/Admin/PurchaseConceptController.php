<?php

namespace App\Http\Controllers\Admin;

use App\Components\Common\Models\CatUsoCfdi;
use App\Components\Purchase\Models\PurchaseConcept;
use App\Components\Purchase\Models\PurchaseType;
use App\Components\Purchase\Repositories\PurchaseConceptRepository;
use Illuminate\Http\Request;

class PurchaseConceptController extends AdminController
{

    /**
     * @var purchaseConceptRepository
     */
    private $purchaseConceptRepository;

    /**
     * UserController constructor.
     * @param purchaseConceptRepository $purchaseConceptRepository
     */
    public function __construct(PurchaseConceptRepository $purchaseConceptRepository)
    {
        $this->purchaseConceptRepository = $purchaseConceptRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = $this->purchaseConceptRepository->list(request()->all());
        $optionsfilters = [
            'uso_cfdi' => CatUsoCfdi::all(),
            'purchase_types' => PurchaseType::all('id', 'name')
        ];
        return $this->sendResponseOk(compact('items', 'optionsfilters'), "list Purchase Concept ok.");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $uso_cfdi = CatUsoCfdi::all();
        $purchase_types = PurchaseType::all('id', 'name');
        return $this->sendResponseOk(compact('uso_cfdi', 'purchase_types'));
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
        ], [
            'name.unique' => 'El concepto ya se encuentra Registrado',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $created = $this->purchaseConceptRepository->create($request->all());

        if (!$created) {
            return $this->sendResponseBadRequest('Concepto no Credo');
        }

        return $this->sendResponseCreated([], 'Concepto Registrado');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Components\Purchase\Models\PurchaseConcept  $purchaseConcept
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseConcept $purchaseConcept)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Components\Purchase\Models\PurchaseConcept  $purchaseConcept
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseConcept $purchaseConcept)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Components\Purchase\Models\PurchaseConcept  $purchaseConcept
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PurchaseConcept $purchaseConcept)
    {
        $validate = validator($request->all(), [
            'name' => 'required',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $updated = $purchaseConcept->update($request->all());

        if (!$updated)
            return $this->sendResponseBadRequest();
        return $this->sendResponseUpdated();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Components\Purchase\Models\PurchaseConcept  $purchaseConcept
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseConcept $purchaseConcept)
    {
        //
    }

    public function updateUsoCfdi(Request $request, PurchaseConcept $purchaseConcept)
    {
        $validate = validator($request->all(), [
            'usocfdi' => 'array',
        ]);

        if ($validate->fails())
            return $this->sendResponseBadRequest($validate->errors()->first());

        // $seller = $this->sellerRepository->find($id);

        $usoCfdiClaves = [];

        if ($usocfdi = $request->get('usocfdi', [])) {
            foreach ($usocfdi as $usoCfdiClave => $shouldAttach) {
                if ($shouldAttach)
                    $usoCfdiClaves[] = $usoCfdiClave;
            }
        }

        $purchaseConcept->usocfdi()->sync($usoCfdiClaves);

        return $this->sendResponseUpdated();
    }
}