<?php

namespace App\Components\Vehicle\Repositories;

use Illuminate\Support\Arr;

use App\Components\Core\BaseRepository;
use App\Components\Core\Utilities\Helpers;
use App\Components\Vehicle\Models\VehicleService;
use Illuminate\Support\Facades\Auth;

class VehicleServicesRepository extends BaseRepository
{
    public function __construct(VehicleService $model)
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
        return $this->get($params, ['user:id,name', 'vehicle:id,matricula', 'estatus'], function ($q) use ($params) {
            return $q;
        });
    }

    public function delete(int $id)
    {
        $ids = explode(',', $id);

        foreach ($ids as $id) {
            $VehicleServices = $this->model->find($id);

            if (!$VehicleServices) {
                return false;
            };

            $VehicleServices->delete();
        }

        return true;
    }
}
