<?php

namespace App\Components\Product\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBrands extends Model
{
    protected $table = 'products_brands';
    protected $fillable = [
        'name'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
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
