<?php

namespace App\Components\Purchase\Pivots;

use App\Components\Common\Models\CatUnitSat;
use Illuminate\Database\Eloquent\Relations\Pivot;

use App\Components\Common\Models\cClaveProdServ;
use App\Components\Purchase\Models\PurchaseOrder;

class PurchasePivotProduct extends Pivot
{
    protected $connection = 'mysql';
    protected $table = 'purchase_pivot_detail_products';
    protected $guarded = ['id'];
    public $timestamps = false;


    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    public function claveProdServ()
    {
        return $this->belongsTo(cClaveProdServ::class, 'c_ClaveProdServ', 'c_ClaveProdServ');
    }
    public function claveUnit()
    {
        return $this->belongsTo(CatUnitSat::class, 'unit_id', 'id');
    }

}