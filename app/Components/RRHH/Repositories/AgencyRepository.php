<?php

namespace App\Components\RRHH\Repositories;

use App\Components\Common\Models\Agency;
use App\Components\Core\BaseRepository;

class AgencyRepository extends BaseRepository
{
    public function __construct(Agency $model)
    {
        parent::__construct($model);
    }

    public function list($params)
    {
        return $this->get($params, [], function ($query) use ($params) {
            // $query->where(function ($query) use ($params) {
            //     $query->search($params['search'] ?? '');
            //     $query->filter($params);
            // });
            return $query;
        });
    }
}