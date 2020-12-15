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
        return $this->get($params, ['vehicle', 'user', 'cargoA', 'estatus'], function ($q) use ($params) {

            $q->ofFolio($params['folio'] ?? '');
            $q->ofEstatus(Helpers::commasToArray($params['estatus_keys'] ?? ''));
            $q->ofAgency(Helpers::commasToArray($params['agencies_id'] ?? ''));

            if (Auth::user()->hasPermission('flotilla.admin')) {
                return $q;
            }

            if (Auth::user()->hasPermission('flotilla.dispersar')) {
                $q->whereHas('estatus', function ($q) {
                    return $q->whereIn('key', ['dispersal:autorizado', 'dispersal:dispersado']);
                });
                return $q;
            }

            $q->where('solicitante', Auth::user()->id);

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
