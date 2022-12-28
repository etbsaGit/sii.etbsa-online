<?php

namespace App\Components\Product\Repositories;

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Currency;
use App\Components\Core\BaseRepository;
use App\Components\Product\Models\Product;
use App\Components\Product\Models\ProductCategory;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function list($params)
    {
        return $this->get($params, ['category:id,name', 'model:id,name', 'agency:id,title', 'currency'], function ($q) use ($params) {
            $q->search($params['search'] ?? '');
            return $q;
        });
    }

    public function options()
    {
        $category = ProductCategory::with('models')->get();
        $agency = Agency::all('id', 'title');
        $currency = Currency::all('id', 'name');

        return compact('category', 'agency', 'currency');
    }
}
