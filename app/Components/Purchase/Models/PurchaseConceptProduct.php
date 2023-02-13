<?php

namespace App\Components\Purchase\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseConceptProduct extends Model
{
    protected $table = 'purchase_concept_products';
    protected $guarded = ['id'];
    protected $with = [];

    public function purchaseConcept()
    {
        return $this->belongsTo(PurchaseConcept::class, 'purchase_concept_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsToMany(PurchaseOrder::class, 'purchase_pivot_detail_products', 'concept_product_id', 'purchase_order_id');
    }

    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', "%{$search}%");
            });
        });
    }
}
