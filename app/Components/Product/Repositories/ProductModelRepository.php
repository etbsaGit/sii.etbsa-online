<?php

namespace App\Components\Product\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Product\Models\ProductCategory;
use App\Components\Product\Models\ProductModel;

class ProductModelRepository extends BaseRepository
{
    public function __construct(ProductModel $model)
    {
        parent::__construct($model);
    }

    public function list($params)
    {
        return $this->get($params, ['category'], function ($q) use ($params) {
            $q->search($params['search'] ?? '');
            return $q;
        });
    }

    public function options()
    {
        $category = ProductCategory::all('id', 'name');

        return compact('category');
    }
}
