<?php

namespace App\Components\Product\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'cat_product_category';
    protected $fillable = [
        'name'
    ];

    public function models()
    {
        return $this->hasMany(ProductModel::class, 'product_category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'product_category_id');
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
