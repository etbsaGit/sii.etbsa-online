<?php

namespace App\Components\Gps\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Gps\Models\GpsGroup;
use Illuminate\Support\Arr;

class GpsGroupRepository extends BaseRepository
{
    public function __construct(GpsGroup $model)
    {
        parent::__construct($model);
    }

    /**
     * list resource
     *
     * @param array $params
     * @return GpsGroupRepository[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function index($params)
    {
        return $this->get($params, ['gps'], function ($q) use ($params) {
            $name = Arr::get($params, 'name', null);

            if ($name) {
                $q = $q->where('name', 'like', "%{$name}%");
            }

            return $q;
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
        $ids = explode(',',$id);

        foreach ($ids as $id)
        {
            /** @var GpsGroup $GpsGroup */
            $GpsGroup = $this->model->find($id);

            if(!$GpsGroup)
            {
                return false;
            };

            $GpsGroup->gps()->detach();
            $GpsGroup->delete();
        }

        return true;
    }
}
