<?php

namespace App\Components\Tracking\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Core\Utilities\Helpers;
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
        return $this->get($params, ['estatus', 'attended_by', 'prospect', 'agency', 'department'], function ($q) use ($params) {

            $q->ofTitle($params['title'] ?? '');
            $q->ofEstatus(Helpers::commasToArray($params['estatus_keys'] ?? ''));
            $q->ofAgency(Helpers::commasToArray($params['agencies_id'] ?? ''));
            $q->ofDepartment(Helpers::commasToArray($params['departments_id'] ?? ''));
            $q->ofProspect(Helpers::commasToArray($params['prospects_id'] ?? ''));

            if (Auth::user()->isSuperUser()) {
                return $q;
            }

            $agency_type = Auth::user()->seller_agency->map(function ($i, $k) {
                return $i->id;
            })->toArray();

            $depto_type = Auth::user()->seller_type->map(function ($i, $k) {
                return $i->id;
            })->toArray();

            if (Auth::user()->inGroup('Gerente')) {
                $seller_type = Auth::user()->seller_type;
                return $q
                    ->whereIn('agency_id', $agency_type)
                    ->whereIn('department_id', $depto_type)
                    ->orWhere('assigned_by', Auth::user()->id)
                    ->orWhere('attended_by', Auth::user()->id);
            }

            if (Auth::user()->inGroup('Vendedor')) {
                $q->where('attended_by', Auth::user()->id);
            }

            return $q;

        });
    }

}
