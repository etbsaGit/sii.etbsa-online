<?php

namespace App\components\NT\Models;

use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class AmsComparative extends Model
{
    protected $table = 'ams_comparatives';
    protected $fillable = [
        'customer_fullname', 'customer_phone', 'customer_email', 'customer_city', 'customer_country', 'customer_company',
        'equipments',
        'grooves', 'tractor_value', 'tractor_potencia', 'diesel_prepare', 'diesel_work', 'diesel_price',
        'hectares', 'cycles', 'ams_value',
        'created_by', 'updated_by'
    ];
    protected $with = [
        'createdBy'
    ];


    public function setEquipmentsAttribute($equipments)
    {
        $this->attributes['equipments'] = serialize($equipments);
    }
    public function getEquipmentsAttribute()
    {
        if (empty($this->attributes['equipments']) || is_null($this->attributes['equipments'])) {
            return [];
        }
        return unserialize($this->attributes['equipments']);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('customer_fullname', 'like', "%{$search}%");
                $query->orWhere('customer_phone', 'like', "%{$search}%");
                $query->orWhere('customer_email', 'like', "%{$search}%");
                $query->orWhere('customer_company', 'like', "%{$search}%");
            });
        });
    }
}
