<?php

namespace App\Components\Purchase\Models;

use App\Components\Common\Models\CatUsoCfdi;
use Illuminate\Database\Eloquent\Model;

class PurchaseConcept extends Model
{
    protected $table = 'purchase_concepts';
    protected $guarded = ['id'];
    protected $with = ['usocfdi', 'purchaseType', 'conceptProduct'];


    public function usocfdi()
    {
        return $this->belongsToMany(CatUsoCfdi::class, 'purchase_concepts_uso_cfdi', 'purchase_concept_id', 'uso_cfdi_clave');
    }

    public function purchaseType()
    {
        return $this->belongsTo(PurchaseType::class, 'purchase_type_id');
    }

    public function conceptProduct()
    {
        return $this->hasMany(PurchaseConceptProduct::class, 'purchase_concept_id');
    }

    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', "%{$search}%");
            });
        });
    }

    // public function setProductsAttribute($products)
    // {
    //     $this->attributes['products'] = serialize($products);
    // }
    // public function getProductsAttribute()
    // {
    //     if (empty($this->attributes['products']) || is_null($this->attributes['products'])) {
    //         return [];
    //     }

    //     return unserialize($this->attributes['products']);
    // }
}
