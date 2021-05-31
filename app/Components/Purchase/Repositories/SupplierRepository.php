<?php

namespace App\Components\Purchase\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Core\Utilities\Helpers;
use App\Components\Purchase\Models\Supplier;

class SupplierRepository extends BaseRepository
{
    public function __construct(Supplier $model)
    {
        parent::__construct($model);
    }

    /**
     * list all users
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function list($params)
    {
        return $this->get($params, [], function ($query) use ($params) {
            $query->where(function ($query) use ($params) {
                $query->search($params['search'] ?? '');
            });
            return $query;
        });
    }
}
