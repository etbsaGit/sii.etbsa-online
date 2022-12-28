<?php

namespace App\Components\Product\Models;

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Currency;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'product_category_id', 'product_model_id', 'name', 'description', 'agency_id', 'sku',
        'active', 'is_usado', 'is_dollar', 'currency_id', 'price_1', 'price_2', 'price_3'
    ];

    const PRODUCT_QTY = 1;
    const PRODUCT_DISCOUNT = 0;
    const PRODUCT_INCOME = 0;
    protected $appends = ['type', 'qty', 'discount'];

    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
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
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }


    public function getTypeAttribute()
    {
        return Product::class;
    }
    public function getQtyAttribute()
    {
        return $this::PRODUCT_QTY;
    }
    public function getDiscountAttribute()
    {
        return $this::PRODUCT_DISCOUNT;
    }
    public function getIncomeAttribute()
    {
        $cost = $this->price_2;
        $list = $this->price_1;
        $icome = ($list - $cost) / $list;
        return number_format($icome, 2);
    }
}
