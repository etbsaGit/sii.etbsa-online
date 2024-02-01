<?php

namespace App\Components\Vehicle\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Vehicle\Models\VehicleTicketCard;

class VehicleTicketCardRepository extends BaseRepository
{
    public function __construct(VehicleTicketCard $model)
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
        return $this->get($params, ['vehicle'], function ($query) use ($params) {
            $query->where(function ($query) use ($params) {
                $query->search($params['search'] ?? '');
            });
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
