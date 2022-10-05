<?php

namespace App\Components\Purchase\Models;

use App\Components\Common\Models\CatUsoCfdi;
use Illuminate\Database\Eloquent\Model;

class PurchaseConcept extends Model
{
    protected $table = 'purchase_concepts';
    protected $guarded = ['id'];
    protected $with = ['usocfdi'];


    public function usocfdi()
    {
        return $this->belongsToMany(CatUsoCfdi::class, 'purchase_concepts_uso_cfdi', 'purchase_concept_id', 'uso_cfdi_clave');
    }

    public function setProductsAttribute($products)
    {
        $this->attributes['products'] = serialize($products);
    }
    public function getProductsAttribute()
    {
        if (empty($this->attributes['products']) || is_null($this->attributes['products'])) {
            return [];
        }

        return unserialize($this->attributes['products']);
    }
}
