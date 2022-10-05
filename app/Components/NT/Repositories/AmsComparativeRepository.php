<?php

namespace App\Components\NT\Repositories;

use App\Components\Core\BaseRepository;
use App\components\NT\Models\AmsComparative;

class AmsComparativeRepository extends BaseRepository
{
    public function __construct(AmsComparative $model)
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
