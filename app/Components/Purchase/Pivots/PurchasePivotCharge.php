<?php

namespace App\Components\Purchase\Pivots;

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Department;
use App\Components\Purchase\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PurchasePivotCharge extends Pivot
{
    protected $table = 'purchase_pivot_charges';

    protected $primaryKey = ['purchase_order_id', 'agency_id', 'department_id'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }


    public static function getPrimaryKey()
    {
        return (new self())->primaryKey;
    }
    // public static function getTable()
    // {
    //     return (new self())->table;
    // }
    public function getPrimaryKeyValues()
    {
        $primaryKeyValues = [];
        foreach ($this->primaryKey as $key) {
            $primaryKeyValues[$key] = $this->getAttribute($key);
        }
        return $primaryKeyValues;
    }
}