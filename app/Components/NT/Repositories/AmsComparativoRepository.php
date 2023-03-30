<?php

namespace App\Components\NT\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\NT\Models\AmsComparativo;

class AmsComparativoRepository extends BaseRepository
{
    public function __construct(AmsComparativo $model)
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
