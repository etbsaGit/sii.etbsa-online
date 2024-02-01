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
        return $this->get($params, ['gpsGroup', 'chip', 'historical'], function ($q) use ($params) {

            $typeQuery = ($params['typeQuery'] ?? false);
            $month = ($params['month'] ?? false);

            $assigned = ($params['assigned'] ?? null);
            $deallocated = ($params['deallocated'] ?? null);
            $expired = ($params['expired'] ?? null);
            $canceled = ($params['canceled'] ?? null);

            if ($typeQuery != 'cancelados' && $month) {
                $q->ofTotalGps($month);
                if ($typeQuery == 'nuevos') {
                    $q->OfNewGps();
                }

                if ($typeQuery == 'renovados') {
                    $q->OfReNewGps();
                }

                if ($typeQuery == 'por_renovar') {
                    $q->OfToReNewGps();
                }
            } else if ($typeQuery == 'cancelados' && $month) {
                $q->ofCanceledGps($month);
            }

            // $q->ofName($params['name'] ?? '');
            // $q->ofGpsGroups(Helpers::commasToArray($params['group_id'] ?? ''));
            // $q->ofGpsChips(Helpers::commasToArray($params['chips_id'] ?? ''));
            // $q->ofAgency($params['agency'] ?? '');
            // $q->ofDepartment($params['department'] ?? '');
            // $q->ofPayment($params['payment_type'] ?? '');
            // $q->ofEstatus($params['estatus'] ?? '');


            // if (!($assigned && $deallocated)) {
            //     $q->ofExpired($expired);
            // }


            // $canceled ? $q->ofCancelled($canceled) : $q->whereNull('cancellation_date');

            return $q;
        });
    }

    public function getStatsGps(Int $month, Int $year)
    {
        $Total = $this->model->whereMonth('installation_date', $month)->count();
        $Nuevos = $this->model->where(function ($query) use ($month, $year) {
            $query->whereMonth('installation_date', $month)
                ->whereYear('installation_date', $year);
        })->count();
        $Renovados = $this->model->where(function ($query) use ($month, $year) {
            $query->whereMonth('installation_date', $month)
                ->whereYear('installation_date', $year);
        })->count();
        // $Total = $this->model::all();
        // ->ofTotalGps($params['month'])
        // ->whereMonth('installation_date', $params['month'])
        // ->count();


        // $Nuevos = $this->get($params, [], function ($q) use ($params) {
        //     // $q->ofTotalGps($params['month'])
        //     $q->whereMonth('installation_date', $params['month'])
        //         ->OfNewGps()->count();
        // });

        // $Renovados = $this->get($params, [], function ($q) use ($params) {
        //     // $q->ofTotalGps($params['month'])
        //     $q->whereMonth('installation_date', $params['month'])
        //         ->OfReNewGps()->count();
        // });

        // $PorRenovar = $this->get($params, [], function ($q) use ($params) {
        //     // $q->ofTotalGps($params['month'])
        //     $q->whereMonth('installation_date', $params['month'])
        //         ->OfToReNewGps()->count();
        // });

        // $Cancelados = $this->get($params, [], function ($q) use ($params) {
        //     $q->ofCanceledGps($params['month'])->count();
        // });

        // $Porcentaje = $Renovados / ($Total - $Nuevos);

        // $stats = [
        //     'Total' => $Total,
        //     // 'Nuevos' => $Nuevos,
        //     // 'Renovados' => $Renovados,
        //     // 'PorRenovar' => $PorRenovar,
        //     // 'Cancelados' => $Cancelados,
        //     // 'Porcentaje' => $Porcentaje,
        // ];

        return ['Total' => $Total, 'Nuevos' => $Nuevos];
    }

    public function keepHistorical(int $id)
    {
        $ids = explode(',', $id);

        foreach ($ids as $id) {
            /** @var Gps $Gps */
            $gps = $this->model->find($id);

            if (!$gps) {
                return false;
            };

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
