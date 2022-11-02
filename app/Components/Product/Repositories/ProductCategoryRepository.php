<?php

namespace App\Components\Product\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Product\Models\ProductCategory;

class ProductCategoryRepository extends BaseRepository
{
    public function __construct(ProductCategory $model)
    {
        parent::__construct($model);
    }

    public function list($params)
    {
        return $this->get($params, ['models:id,name,product_category_id'], function ($q) use ($params) {
            $q->search($params['search'] ?? '');
            return $q;
        });
    }
}
