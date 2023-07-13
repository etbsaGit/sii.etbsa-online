<?php

namespace App\Components\Product\Repositories;

use App\Components\Common\Models\Agency;
use App\Components\Core\BaseRepository;
use App\Components\Product\Models\Product;
use App\Components\Product\Models\ProductInventory;

class ProductInventoryRepository extends BaseRepository
{
    public function __construct(ProductInventory $model)
    {
        parent::__construct($model);
    }

    public function list($params)
    {
        return $this->get($params, [
            'product:id,name,sku,product_category_id,product_model_id',
            'product.model:id,name',
            'product.category:id,name',
            'location_branch:id,title',
            'assigned_branch:id,title'
        ], function ($q) use ($params) {
            // $q->search($params['search'] ?? '');
            return $q;
        });
    }

    public function options()
    {
        // $category = ProductCategory::all('id', 'name');
        $products = Product::with('category:id,name')->get(['id', 'name', 'sku', 'product_category_id']);
        $agencies = Agency::all('id', 'title');

        return compact('products', 'agencies');
    }
}