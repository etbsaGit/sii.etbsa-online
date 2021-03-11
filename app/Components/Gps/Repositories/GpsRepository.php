<?php

namespace App\Components\Gps\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Core\Utilities\Helpers;
use App\Components\Gps\Models\Gps;

class GpsRepository extends BaseRepository
{
    public function __construct(Gps $model)
    {
        parent::__construct($model);
    }

    /**
     * list resource
     *
     * @param array $params
     * @return GpsRepository[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function index($params)
    {
        return $this->get($params, ['gpsGroup', 'chip'], function ($query) use ($params) {
            $query->where(function ($query) use ($params) {
                $query->filter($params)
                    ->filterByDateRange($params['dates'] ?? null);
            });

            return $query;
        });
    }

    public function getStatsGps(Int $month, Int $year)
    {
        $Total = $this->model->whereMonth('installation_date', $month)->count();

        $Nuevos = $this->model->where(function ($query) use ($month, $year) {
            $query->whereMonth('installation_date', $month)
                ->whereYear('installation_date', $year)
                ->whereYear('renew_date', '>', $year)
                ->whereNull('cancellation_date');
        })->count();

        $Renovados = $this->model->where(function ($query) use ($month, $year) {
            $query->whereMonth('renew_date', $month)
                ->whereYear('renew_date', '>', $year)
                ->whereNull('cancellation_date');
        })->count();

        $PorRenovar = $this->model->where(function ($query) use ($month, $year) {
            $query->whereMonth('renew_date', $month)
                ->whereYear('renew_date', $year)
                ->whereNull('cancellation_date');
        })->count();

        $Cancelados = $this->model->where(function ($query) use ($month, $year) {
            $query->whereMonth('installation_date', $month)
                ->whereYear('cancellation_date', $year)
                ->whereNull('renew_date');
        })->count();

        $Porcentaje = $Renovados / ($Total - $Nuevos);

        return [
            'Total' => $Total,
            'Nuevos' => $Nuevos,
            'Renovados' => $Renovados,
            'PorRenovar' => $PorRenovar,
            'Cancelados' => $Cancelados,
            'Porcentaje' => $Porcentaje,
            'Month' => $month,
        ];
    }

    public function keepHistorical(Gps $gps)
    {
        if ($gps) {
            $historical = $gps->replicate()->fill([
                'uploaded_by' => auth()->user()->id,
            ])->toArray();

            $gps->historical()->create($historical);
        }
    }

    public function cancelGps(int $id)
    {
        $ids = explode(',', $id);

        foreach ($ids as $id) {
            /** @var Gps $Gps */
            $gps = $this->model->find($id);

            if (!$gps) {
                return false;
            }

            // $gps->gpsGroup()->dissociate();
            $gps->chip()->dissociate();
            $gps->save();
        }
    }

    public function delete(int $id)
    {
        $ids = explode(',', $id);

        foreach ($ids as $id) {
            /** @var Gps $Gps */
            $Gps = $this->model->find($id);

            if (!$Gps) {
                return false;
            };

            $Gps->gpsGroup()->dissociate();
            $Gps->delete();
        }

        return true;
    }
}
