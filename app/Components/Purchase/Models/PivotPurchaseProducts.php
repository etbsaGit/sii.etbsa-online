<?php

namespace App\Components\Purchase\Models;

use App\Components\Common\Models\CatUnitSat;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PivotPurchaseProducts extends Pivot
{
    protected $table = 'purchase_pivot_detail_products';
    protected $with = ['unit'];


    public function unit()
    {
        return $this->belongsTo(CatUnitSat::class, 'unit_id', 'id');
    }
}
