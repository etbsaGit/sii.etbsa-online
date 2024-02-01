<?php

namespace App\Components\Product\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'cat_product_category';
    protected $fillable = [
        'name',
        'parent'
    ];

    // protected $with = ['child', 'parent'];
    protected $appends = ['breadcrumbs_category'];

    public function models()
    {
        return $this->hasMany(ProductModel::class, 'product_category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'product_category_id');
    }
    public function parentCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'parent');
    }

    public function childCategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function allParentCategories()
    {
        return $this->parentCategory()->with('allParentCategories');
    }

    public function scopeSearch($query, string $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', "%{$search}%");
            });
        });
    }

    public function getBreadcrumbsCategoryAttribute()
    {
        $categories = [];
        $parent = $this->parentCategory;

        while ($parent) {
            $categories[] = [
                'id' => $parent->id,
                'text' => $parent->name,
            ];
            $parent = $parent->parentCategory;
        }


        array_unshift($categories, [
            'id' => $this->id,
            'text' => $this->name,
        ]);

        $this->unsetRelation('parentCategory');

        return array_reverse($categories);
    }


}