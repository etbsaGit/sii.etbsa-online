<?php

namespace App\Components\Purchase\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Core\Utilities\Helpers;
use App\Components\Purchase\Models\PurchaseOrder;
use Auth;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderRepository extends BaseRepository
{
    public function __construct(PurchaseOrder $model)
    {
        parent::__construct($model);
    }

    /**
     * list all users
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function list($params)
    {
        return $this->get($params, [
            'supplier:id,business_name,rfc',
            'elaborated.profiable:id,name,last_name,agency_id',
            'elaborated.profiable.agency:id,title',
            'estatus:id,title,key',
            'purchase_concept:id,name',
            'purchaseType:id,name',
            'files'

        ], function ($query) use ($params) {
            $query->where(function ($query) use ($params) {
                $query->search($params['search'] ?? '')
                    ->filter($params)
                    ->filterPermission(Auth::user());
            });
            return $query;
        });
    }

    // public function generateAutorizationToken($purchaseOrder)
    // {
    //     $token = 'AAG-';
    //     $sucursal = explode(' ', $purchaseOrder->sucursal->title);
    //     foreach ($sucursal as $suc) {
    //         $token .= substr($suc, 0, 3);
    //     }
    //     $token .=
    //         '-' . date('y') . '/' .  str_pad($purchaseOrder->id, 4, '0', STR_PAD_LEFT);

    //     return $token;
    // }
}