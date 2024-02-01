<?php

namespace App\Components\Common\Models;

use App\Components\Purchase\Models\PivotPurchaseProducts;
use Illuminate\Database\Eloquent\Model;
use App\Components\Purchase\Models\SupplierProduct;

class CatUnitSat extends Model
{
    protected $table = 'cat_unit_sat';
    protected $casts = [
        'clave' => 'String'
    ];



    public function supplierProducts()
    {
        return $this->hasMany(SupplierProduct::class, 'cat_unit_sat_id', 'id');
    }
    public function conceptProduct()
    {
        return $this->hasOne(PivotPurchaseProducts::class, 'unit_id', 'id');
    }
}
