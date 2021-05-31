<?php

namespace App\Components\Purchase\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $guarded = ['id'];

    /**
     * serializes concept attribute on the fly before saving to database
     *
     * @param $billing_data
     */
    public function setBillingDataAttribute($billing_data)
    {
        $this->attributes['billing_data'] = serialize($billing_data);
    }

    /**
     * unserializes billing_data attribute before spitting out from database
     *
     * @return mixed
     */
    public function getBillingDataAttribute()
    {
        if (empty($this->attributes['billing_data']) || is_null($this->attributes['billing_data'])) {
            return [];
        }

        return unserialize($this->attributes['billing_data']);
    }

    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('business_name', 'like', "%{$search}%")
                    ->orWhere('rfc', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        });
    }
}
