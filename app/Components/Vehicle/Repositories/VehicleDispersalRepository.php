<?php

namespace App\Components\Vehicle\Repositories;

use Illuminate\Support\Arr;

use App\Components\Core\BaseRepository;
use App\Components\Core\Utilities\Helpers;
use App\Components\Vehicle\Models\VehicleDispersal;
use Illuminate\Support\Facades\Auth;

class VehicleDispersalRepository extends BaseRepository
{
    public function __construct(VehicleDispersal $model)
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
        return $this->get($params, [
            'vehicle', 'agency:id,title', 'department:id,title'
        ], function ($q) use ($params) {

            return $q;
        });
    }

    public function delete(int $id)
    {
        $ids = explode(',', $id);

        foreach ($ids as $id) {
            $VehicleDispersal = $this->model->find($id);

            if (!$VehicleDispersal) {
                return false;
            };

            $VehicleDispersal->delete();
        }

        return true;
    }
}
