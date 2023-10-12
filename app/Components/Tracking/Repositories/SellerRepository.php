<?php

namespace App\Components\Tracking\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Core\Utilities\Helpers;
use App\Components\User\Models\User;

class SellerRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * list all users
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed[]
     */
    public function listSellers($params)
    {
        return $this->get($params, ['groups', 'seller_type', 'seller_agency','seller_category'], function ($q) use ($params) {
            $q->whereHas('groups', function ($query) {
                return $query->whereIn('groups.name', ['Vendedor']);
            });
            $q->ofName($params['name'] ?? '');
            $q->ofEmail($params['email'] ?? '');

            $q->ofSellerType(Helpers::commasToArray($params['seller_type_id'] ?? ''));
            $q->ofSellerAgency(Helpers::commasToArray($params['seller_agency_id'] ?? ''));
            return $q;
        });
    }

    public function deletePhoto($params){
        
    }
}
