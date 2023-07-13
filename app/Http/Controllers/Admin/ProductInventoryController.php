<?php

namespace App\Http\Controllers\Admin;

use App\Components\Product\Models\ProductInventory;
use App\Components\Product\Repositories\ProductInventoryRepository;
use Illuminate\Http\Request;

class ProductInventoryController extends AdminController
{

    private $productInventoryRepository;

    public function __construct(ProductInventoryRepository $productInventoryRepository)
    {
        $this->productInventoryRepository = $productInventoryRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productInventories = $this->productInventoryRepository->list(request()->all());
        $options = $this->productInventoryRepository->options();
        return $this->sendResponseOk(compact('productInventories', 'options'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $products = ProductInventory::with(
            'product:id,name,product_category_id',
            'product.category:id,name',
            'location_branch:id,title'
        )
            ->select('location', 'product_id', \DB::raw('count(*) as count'))
            ->groupBy('location', 'product_id')
            ->get();


        $result = $products->groupBy(function ($product) {
            return $product->location_branch->title;

        })->map(function ($productsByAgency) {
            return $productsByAgency->groupBy(function ($product) {
                return ['categoria' => $product->product->category->name];
            })->map(function ($productsByCategory) {
                return [
                    'products' => $productsByCategory->map(function ($product) {
                        return [
                            'name' => $product->product->name,
                            'count' => $product->count,
                        ];
                    })->toArray(),
                ];
            });
        });


        return $this->sendResponseOk(compact('result'));

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
            'product_id' => 'required',
            'location' => 'required',
            'num_serie' => 'required',
        ]);
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $productInventory = $this->productInventoryRepository->create($request->all());
        if (!$productInventory) {
            return $this->sendResponseBadRequest("Failed create.");
        }

        return $this->sendResponseCreated(compact('productInventory'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Components\Product\Models\ProductInventory  $productInventory
     * @return \Illuminate\Http\Response
     */
    public function show(ProductInventory $productInventory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Components\Product\Models\ProductInventory  $productInventory
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductInventory $productInventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Components\Product\Models\ProductInventory  $productInventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductInventory $inventory)
    {
        $validate = validator($request->all(), [
            'product_id' => 'required',
            'location' => 'required',
            'num_serie' => 'required',
        ]);
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $updated = $inventory->update($request->all());
        if (!$updated) {
            return $this->sendResponseBadRequest("Failed update.");
        }
        return $this->sendResponseUpdated([$updated]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Components\Product\Models\ProductInventory  $productInventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductInventory $inventory)
    {
        $inventory->delete();
        return $this->sendResponseDeleted();
    }



}