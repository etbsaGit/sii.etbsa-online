<?php

namespace App\Http\Controllers\Admin;

use App\Components\Purchase\Models\PurchaseConceptProduct;
use App\Components\Purchase\Repositories\PurchaseConceptProductRepository;
use Illuminate\Http\Request;

class PurchaseConceptProductController extends AdminController
{
    private $purchaseConceptProductRepository;

    public function __construct(PurchaseConceptProductRepository $purchaseConceptProductRepository)
    {
        $this->purchaseConceptProductRepository = $purchaseConceptProductRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->purchaseConceptProductRepository->list(request()->all());
        $data->setCollection(
            $data->getCollection()->transform(function ($model) {
                return [
                    'id' => $model->id,
                    'name' => $model->name,
                    'purchase_concept' => $model->purchaseConcept->name,
                    'purchase_concept_type' => $model->purchaseConcept->purchaseType->name,
                ];
            })
        )->toArray();
        $options = $this->purchaseConceptProductRepository->options();
        return $this->sendResponseOk(compact('data', 'options'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $options = $this->purchaseConceptProductRepository->options();
        return $this->sendResponseOk(compact('options'));
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
        $productModel = $this->purchaseConceptProductRepository->create($request->all());
        if (!$productModel) {
            return $this->sendResponseBadRequest("Failed create.");
        }
        return $this->sendResponseCreated($productModel);
    }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($id)
    // {
    //     //
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit($id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PurchaseConceptProduct $purchase_concept_product)
    {
        $validate = validator($request->all(), []);
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        // dd($purchase_concept_product->id,$request->all());
        $updated = $purchase_concept_product->update($request->all());
        if (!$updated) {
            return $this->sendResponseBadRequest("Failed update.");
        }
        return $this->sendResponseUpdated($updated);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}