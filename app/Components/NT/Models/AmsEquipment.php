<?php

namespace App\Components\NT\Models;

use Illuminate\Database\Eloquent\Model;

class AmsEquipment extends Model
{
    protected $table = 'ams_equipments';
    protected $fillable = [
        'category', 'name', 'unit', 'value', 'price'
    ];

    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('category', 'like', "%{$search}%");
                $query->orWhere('name', 'like', "%{$search}%");
            });
        });
    }
    public function scopeCategory($query, String $category)
    {
        $query->when($category ?? null, function ($query, $category) {
            $query->where(function ($query) use ($category) {
                $query->orWhere('category', 'like', "{$category}%");
            });
        });
    }
}
