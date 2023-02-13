<?php

namespace App\Http\Controllers\Admin;

use App\Components\Product\Models\ProductSuppliers;
use App\Components\Product\Repositories\ProductSupplierRepository;
use Illuminate\Http\Request;

class ProductSuppliersController extends AdminController
{
    private $productSupplierRepository;

    public function __construct(ProductSupplierRepository $productSupplierRepository)
    {
        $this->productSupplierRepository = $productSupplierRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->productSupplierRepository->list(request()->all());
        return $this->sendResponseOk($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $productCategory = $this->productSupplierRepository->create($request->all());
        if (!$productCategory) {
            return $this->sendResponseBadRequest("Failed create.");
        }
        return $this->sendResponseCreated($productCategory);
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
    public function update(Request $request, ProductSuppliers $supplier)
    {
        $validate = validator($request->all(), []);
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $updated = $supplier->update($request->all());
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
