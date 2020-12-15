<?php

namespace App\Components\Vehicle\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Vehicle\Models\Vehicle;
use Illuminate\Support\Arr;
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
    function list($params)
    {
        return $this->get($params, ['user', 'agency'], function ($q) use ($params) {
            $q->ofMatricula($params['matricula'] ?? '');

            if (Auth::user()->hasPermission('flotilla.admin')) {
                return $q;
            }

            $q->where('responsable', Auth::user()->id);

            return $q;
        });
    }

    public function delete(int $id)
    {
        $ids = explode(',', $id);

        foreach ($ids as $id) {
            $Vehicle = $this->model->find($id);

            if (!$Vehicle) {
                return false;
            };

            $Vehicle->delete();
        }

        return true;
    }
}
