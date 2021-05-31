<?php

namespace App\Components\Gps\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Gps\Models\GpsChips;
use Illuminate\Support\Arr;

class GpsChipsRepository extends BaseRepository
{
    public function __construct(GpsChips $model)
    {
        parent::__construct($model);
    }

    /**
     * list resource
     *
     * @param array $params
     * @return GpsChipsRepository[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function index($params)
    {
        return $this->get($params, ['gps'], function ($query) use ($params) {

            $query->search($params['search'] ?? '')
                ->filter($params);

            return $query;
        });
    }

    /**
     * delete a user by id
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id)
    {
        $ids = explode(',', $id);

        foreach ($ids as $id) {
            /** @var GpsChips $GpsChips */
            $GpsChips = $this->model->find($id);

            if (!$GpsChips) {
                return false;
            };

            // $GpsChips->gps()->dissociate();
            $GpsChips->delete();
        }

        return true;
    }
}
