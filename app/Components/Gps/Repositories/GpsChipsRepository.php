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
        return $this->get($params, ['gps'], function ($q) use ($params) {
            // $sim = Arr::get($params, 'sim', null);
            $assigned = ($params['assigned'] ?? null);
            $deallocated = ($params['deallocated'] ?? null);

            // if ($sim) {
            //     $q = $q->where('sim', 'like', "%{$sim}%");
            // }

            $q->ofSim($params['sim'] ?? '');
            $q->ofImei($params['imei'] ?? '');
            $q->ofMonth($params['month'] ?? '');
            $q->ofYear($params['year'] ?? '');

            if (!($assigned && $deallocated)) {
                $q->ofAssigned($assigned);
                $q->ofDeallocated($deallocated);
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
        $ids = explode(',', $id);

        foreach ($ids as $id) {
            /** @var GpsChips $GpsChips */
            $GpsChips = $this->model->find($id);

            if (!$GpsChips) {
                return false;
            };

            $GpsChips->gps()->detach();
            $GpsChips->delete();
        }

        return true;
    }
}
