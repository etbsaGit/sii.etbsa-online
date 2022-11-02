<?php

namespace App\Components\Product\Models;

use App\Components\Common\Models\Agency;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'product_category_id', 'product_model_id', 'name', 'description', 'agency_id', 'sku',
        'active', 'is_usado', 'is_dollar', 'price_1', 'price_2', 'price_3'
    ];

    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', "%{$search}%");
            });
        });
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
    public function model()
    {
        return $this->belongsTo(ProductModel::class, 'product_model_id');
    }
    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }
}
