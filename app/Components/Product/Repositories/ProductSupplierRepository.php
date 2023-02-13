<?php

namespace App\Components\Product\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Product\Models\ProductSuppliers;

class ProductSupplierRepository extends BaseRepository
{
    public function __construct(ProductSuppliers $model)
    {
        parent::__construct($model);
    }

    public function list($params)
    {
        return $this->get($params, [], function ($q) use ($params) {
            $q->search($params['search'] ?? '');
            return $q;
        });
    }
}
