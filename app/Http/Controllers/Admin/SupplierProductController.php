<?php

namespace App\Http\Controllers\Admin;

use App\Components\Purchase\Repositories\SupplierProductRepository;
use Illuminate\Http\Request;

class SupplierProductController extends AdminController
{
    /**
     * @var SupplierRepository
     */
    private $supplierProductRepository;

    /**
     * UserController constructor.
     * @param SupplierRepository $supplierProductRepository
     */
    public function __construct(SupplierProductRepository $supplierProductRepository)
    {
        $this->supplierProductRepository = $supplierProductRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->supplierProductRepository->list(request()->all());
        return $this->sendResponseOk($data, "list suppliers Products ok.");
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
            'sku' => 'unique:supplier_products,sku',
            'description' => 'required',
            'supplier_id' => 'required',
        ]);
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $supplier_product = $this->supplierProductRepository->create($request->all());
        if (!$supplier_product) {
            return $this->sendResponseBadRequest("Failed to create.");
        }
        return $this->sendResponseCreated($supplier_product);
    }
}
