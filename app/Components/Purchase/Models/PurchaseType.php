<?php

namespace App\Components\Purchase\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseType extends Model
{
    protected $table = 'purchase_types';
    protected $guarded = ['id'];
    protected $with = [];


    public function PurchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'purchase_type_id', 'id');
    }

    public function purchaseConcept()
    {
        return $this->hasMany(PurchaseConcept::class, 'purchase_type_id', 'id');
    }
}
