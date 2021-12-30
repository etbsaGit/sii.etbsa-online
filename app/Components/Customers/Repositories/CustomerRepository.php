<?php

namespace App\Components\Customers\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Customers\Models\Customers;

class CustomerRepository extends BaseRepository
{
    public function __construct(Customers $model)
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
