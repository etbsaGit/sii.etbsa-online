<?php

namespace App\Components\Gps\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Gps\Models\Gps;
use App\Components\Core\Utilities\Helpers;

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
        return $this->get($params, ['gpsGroup','chip'], function ($q) use ($params) {

            $assigned = ($params['assigned'] ?? null);
            $deallocated = ($params['deallocated'] ?? null);
            $expired = ($params['expired'] ?? null);
            $renewed = ($params['renewed'] ?? null);
            
            $q->ofGpsGroups(Helpers::commasToArray($params['group_id'] ?? ''));
            $q->ofName($params['name'] ?? '');
            $q->ofMonth($params['month'] ?? '');
            $q->ofYear($params['year'] ?? '');
            $q->ofMonthInstallation($params['month_installation'] ?? '');
            $q->ofYearInstallation($params['year_installation'] ?? '');
            $q->ofAgency($params['agency'] ?? '');
            $q->ofDepartment($params['department'] ?? '');
            $q->ofPayment($params['payment_type'] ?? '');

            if (!($assigned && $deallocated)) {
                $q->ofAssigned($assigned);
                $q->ofDeallocated($deallocated);
                $q->ofExpired($expired);
                $q->ofRenewed($renewed);
            }

            return $q;
        });
    }

    public function renewGps($request)
    {
        # code...
    }

    public function delete(int $id)
    {
        $ids = explode(',',$id);

        foreach ($ids as $id)
        {
            /** @var Gps $Gps */
            $Gps = $this->model->find($id);

            if(!$Gps)
            {
                return false;
            };

            $Gps->gpsGroup()->dissociate();
            $Gps->delete();
        }

        return true;
    }
}
