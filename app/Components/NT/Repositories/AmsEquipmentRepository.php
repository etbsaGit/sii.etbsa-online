<?php

namespace App\Components\NT\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\NT\Models\AmsEquipment;

class AmsEquipmentRepository extends BaseRepository
{
    public function __construct(AmsEquipment $model)
    {
        parent::__construct($model);
    }

    public function list($params)
    {
        return $this->get($params, [], function ($q) use ($params) {
            $q->search($params['search'] ?? '');
            $q->category($params['category'] ?? '');
            return $q;
        });
    }
}
