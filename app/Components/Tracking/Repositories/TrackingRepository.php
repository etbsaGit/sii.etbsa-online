<?php

namespace App\Components\Tracking\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Tracking\Models\TrackingProspect;
use Auth;

class TrackingRepository extends BaseRepository
{
    public function __construct(TrackingProspect $model)
    {
        parent::__construct($model);
    }

    /**
     * list all users
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function listTracking($params)
    {
        return $this->get(
            $params,
            [
                'estatus', 'prospect:id,full_name,company',
                'agency:id,title',
                'department:id,title',
                'attended.profiable:id,name,last_name,agency_id',
                'attended.profiable.agency:id,title',
                'estatus:id,title,key',
                'moneda:id,name',
            ],
            function ($query) use ($params) {
                $query->where(function ($query) use ($params) {
                    $query->filter($params)
                        ->filterByDateRange($params['dates'] ?? null, $params['estatus'] ?? null);
                })->when(Auth::user()->isSuperUser(), function ($query) {
                    return $query;
                }, function ($query) {
                    $query->where(function ($query) {
                        $query->filterForManagers(Auth::user());
                    });
                });
                return $query;
            }
        );
    }
    public function diaryTracking()
    {
        return $this->get(
            ['paginate' => 'no'],
            [],
            function ($query) {
                $query->whereHas('historical')
                    ->where(function ($query) {
                        $query->whereIn('estatus_id', [1]);
                    })
                    ->when(Auth::user()->isSuperUser(), function ($query) {
                        return $query;
                    }, function ($query) {
                        $query->where(function ($query) {
                            $query->filterForManagers(Auth::user());
                        });
                    });
                return $query;
            }
        );
    }
}
