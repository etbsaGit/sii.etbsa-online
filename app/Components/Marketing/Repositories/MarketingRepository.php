<?php

namespace App\Components\Marketing\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Core\Utilities\Helpers;
use App\Components\Marketing\Models\Marketing;

class MarketingRepository extends BaseRepository
{
    public function __construct(Marketing $model)
    {
        parent::__construct($model);
    }

    public function index($params)
    {
        return $this->get($params, [], function ($q) use ($params) {

            $q->ofProductName($params['product_name'] ?? '');
            $q->ofCustomerName($params['customer_name'] ?? '');
            $q->ofYears(Helpers::commasToArray($params['years'] ?? ''));
            $q->ofMonths(Helpers::commasToArray($params['months'] ?? ''));
            $q->ofAgencies(Helpers::commasToArray($params['agencies'] ?? ''));
            $q->ofTypeSale(Helpers::commasToArray($params['type_of_sale'] ?? ''));
            $q->ofSeller(Helpers::commasToArray($params['seller'] ?? ''));
            $q->ofBrands(Helpers::commasToArray($params['brands'] ?? ''));

            $q->ofCurrencyMXN(($params['isMXN'] ?? null) && !($params['isUSD'] ?? null));
            $q->ofCurrencyUSD(($params['isUSD'] ?? null) && !($params['isMXN'] ?? null));

            return $q;
        });
    }
}
