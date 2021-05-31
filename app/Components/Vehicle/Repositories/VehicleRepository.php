<?php

namespace App\Components\Vehicle\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Vehicle\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class VehicleRepository extends BaseRepository
{
    public function __construct(Vehicle $model)
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
            $query->where(function ($query) use ($params) {
                $query->search($params['search'] ?? '')
                    ->filter($params)
                    ->filterPermission(Auth::user());
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
