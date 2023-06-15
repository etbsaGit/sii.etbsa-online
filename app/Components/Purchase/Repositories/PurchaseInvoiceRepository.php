<?php

namespace App\Components\Purchase\Repositories;

use App\Components\Core\BaseRepository;
use App\Components\Purchase\Models\Invoice;
use App\Components\Purchase\Models\PurchaseOrder;
use Auth;
use Illuminate\Database\Eloquent\Builder;

class PurchaseInvoiceRepository extends BaseRepository
{
    public function __construct(Invoice $model)
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
            'invoiceable:id,created_by,updated_by,estatus_id,supplier_id,observation,note,purchase_concept_id,payment_condition',
            'invoiceable.supplier:id,business_name,rfc',
            'invoiceable.pivotCharge',
            'invoiceable.estatus:id,title,key',
            'invoiceable.elaborated.profiable:id,name,last_name,agency_id',
            'invoiceable.elaborated.profiable.agency:id,title',
            'invoiceable.purchase_concept:id,name',
        ], function ($query) use ($params) {
            $query->whereHasMorph(
                'invoiceable',
                [PurchaseOrder::class],
                function (Builder $query) use ($params) {
                    $query->filter($params);
                    // search($params['search'] ?? '')
                }
            )
                ->search($params['search'] ?? '');

            return $query;
        });
    }
}
