<?php

namespace App\Components\RRHH\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\RRHH\Models\Employee;

class EmployeeRepository extends BaseRepository
{
    public function __construct(Employee $model)
    {
        parent::__construct($model);
    }

    public function list($params)
    {
        return $this->get($params, ['agency:id,title', 'department:id,title'], function ($query) use ($params) {
            $query->where(function ($query) use ($params) {
                $query->search($params['search'] ?? '');
            });
            return $query;
        });
    }
}
