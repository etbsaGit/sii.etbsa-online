<?php

namespace App\Components\Purchase\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Core\Utilities\Helpers;
use App\Components\Purchase\Models\PurchaseConcept;
use App\Components\Purchase\Models\PurchaseConceptProduct;

class PurchaseConceptProductRepository extends BaseRepository
{
    public function __construct(PurchaseConceptProduct $model)
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
        return $this->get($params, ['purchaseConcept'], function ($query) use ($params) {
            $query->where(function ($query) use ($params) {
                $query->search($params['search'] ?? '');
            });
            return $query;
        });
    }

    public function options()
    {
        $purchase_concept = PurchaseConcept::all();
        return compact('purchase_concept');
    }
}
