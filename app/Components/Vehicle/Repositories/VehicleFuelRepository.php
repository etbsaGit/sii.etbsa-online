<?php

namespace App\Components\Vehicle\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Vehicle\Models\VehicleFuel;

class VehicleFuelRepository extends BaseRepository
{
    public function __construct(VehicleFuel $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function list($params)
    {
        return $this->get($params, [], function ($query) use ($params) {
            return $query;
        });
    }

    // public function delete(int $id)
    // {
    //     $ids = explode(',', $id);

    //     foreach ($ids as $id) {
    //         $Vehicle = $this->model->find($id);

    //         if (!$Vehicle) {
    //             return false;
    //         };

    //         $Vehicle->delete();
    //     }

    //     return true;
    // }
}
