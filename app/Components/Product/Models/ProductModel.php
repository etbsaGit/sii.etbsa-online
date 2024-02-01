<?php

namespace App\Components\Product\Models;

use App\Components\Product\Models\ProductCategory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    protected $table = 'cat_product_models';
    protected $fillable = [
        'name', 'product_category_id'
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
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
