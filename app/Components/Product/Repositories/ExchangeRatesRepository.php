<?php

namespace App\Components\Product\Repositories;

use App\Components\Common\Models\ExchangeRates;
use App\Components\Core\BaseRepository;

class ExchangeRatesRepository extends BaseRepository
{
    public function __construct(ExchangeRates $model)
    {
        parent::__construct($model);
    }

    public function list($params)
    {
        return $this->get($params, [], function ($q) use ($params) {
            // $q->search($params['search'] ?? '');
            return $q;
        });
    }
}
