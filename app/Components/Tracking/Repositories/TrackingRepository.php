<?php

namespace App\Components\Tracking\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Core\Utilities\Helpers;
use App\Components\Tracking\Models\TrackingProspect;
use Auth;
use Illuminate\Support\Facades\Request;

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
            ['estatus', 'prospect', 'agency', 'department'],
            function ($query) use ($params) {
                $query->where(function ($query) use ($params) {
                    $query->filter($params)
                        ->filterByDateRange($params['dates'] ?? null);
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
