<?php

namespace App\Components\RRHH\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\RRHH\Models\EmployeeRecruitment;

class EmployeeRecruitmentRepository extends BaseRepository
{
    public function __construct(EmployeeRecruitment $model)
    {
        parent::__construct($model);
    }

    public function list($params)
    {
        return $this->get($params, [], function ($q) use ($params) {
            return $q;
        });
    }
}
