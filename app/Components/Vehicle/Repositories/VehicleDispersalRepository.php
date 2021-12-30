<?php

namespace App\Components\Vehicle\Repositories;

use App\Components\Common\Models\Estatus;
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
        return $this->get($params, [
            'vehicle', 'agency:id,title', 'department:id,title', 'solicitante', 'fuel:id,name'
        ], function ($query) use ($params) {
            $query->where(function ($query) use ($params) {
                $query->search($params['search'] ?? '')
                    ->filter($params)
                    ->filterPermission(Auth::user());
            });
            return $query;
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


    public function dispersalAurotize(int $id, $payload = [])
    {
        $ids = explode(',', $id);
        foreach ($ids as $id) {
            $VehicleDispersal = $this->model->find($id);
            if (!$VehicleDispersal) {
                return false;
            };
            $estatus = Estatus::where('key', Estatus::ESTATUS_AUTORIZADO)->first();
            $VehicleDispersal->estatus()->associate($estatus);
            $VehicleDispersal->update($payload);
            $VehicleDispersal->save();
        }

        return true;
    }
    public function dispersalDenied(int $id, $payload = [])
    {
        $ids = explode(',', $id);
        foreach ($ids as $id) {
            $VehicleDispersal = $this->model->find($id);
            if (!$VehicleDispersal) {
                return false;
            };
            $estatus = Estatus::where('key', Estatus::ESTATUS_DENGAR)->first();
            $VehicleDispersal->estatus()->associate($estatus);
            $VehicleDispersal->update($payload);
            $VehicleDispersal->save();
        }

        return true;
    }
    public function dispersalDisperse(int $id)
    {
        $ids = explode(',', $id);
        foreach ($ids as $id) {
            $VehicleDispersal = $this->model->find($id);
            if (!$VehicleDispersal) {
                return false;
            };
            $estatus = Estatus::where('key', Estatus::ESTATUS_DISPERSADO)->first();
            $VehicleDispersal->estatus()->associate($estatus);
            // $VehicleDispersal->save();
            $VehicleDispersal->vehicle->update([
                'mileage_last' => $VehicleDispersal->mileage_actual
            ]);
            $VehicleDispersal->ticketcard->update([
                'account_balance' => $VehicleDispersal->ticketcard->account_balance + ($VehicleDispersal->fuel_liters * $VehicleDispersal->fuel_cost_liter)
            ]);
            $VehicleDispersal->save();
        }

        return true;
    }

    public function dispersalDispatched(int $id, $payload = [])
    {
        $ids = explode(',', $id);
        foreach ($ids as $id) {
            $VehicleDispersal = $this->model->find($id);
            if (!$VehicleDispersal) {
                return false;
            };
            $estatus = Estatus::where('key', Estatus::ESTATUS_DESPACHADO)->first();
            $VehicleDispersal->estatus()->associate($estatus);

            $tickets = $VehicleDispersal->tickets_info;
            $tickets[] = $payload['ticket'];
            $VehicleDispersal->tickets_info = $tickets;
            $VehicleDispersal->dispatched_amount = array_reduce($tickets, function ($sum, $item) {
                $sum += ($item['cost_per_liter'] * $item['liters']);
                return $sum;
            });
            // Reducir Saldo a Tarjeta
            $amount = $payload['ticket']['cost_per_liter'] * $payload['ticket']['liters'];

            $VehicleDispersal->ticketcard->update([
                'account_balance' => $VehicleDispersal->ticketcard->account_balance - $amount
            ]);

            // calcular Rendimiento y actualizar Odometro Vehiculo si no es un Bidon
            if (!$VehicleDispersal->drum_dispersion) {
                $performance = ($payload['ticket']['mileage_actual'] - $payload['ticket']['mileage_last']) / $payload['ticket']['liters'];
                $mileage_last = $VehicleDispersal->vehicle->actual;
                $VehicleDispersal->vehicle->update([
                    'fuel_odometer' => $payload['fuel_odometer'],
                    'mileage_actual' => $payload['ticket']['mileage_actual'],
                    'mileage_last' => $mileage_last,
                    'performance_fuel' => $performance,
                ]);
                $VehicleDispersal->update([
                    'fuel_odometer' => $payload['fuel_odometer'],
                    'mileage_actual' => $payload['ticket']['mileage_actual'],
                ]);
            }
            $VehicleDispersal->save();
        }
        return true;
    }
}
