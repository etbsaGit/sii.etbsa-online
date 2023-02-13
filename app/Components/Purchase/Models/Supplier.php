<?php

namespace App\Components\Purchase\Models;

use App\Components\Common\Models\Township;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $guarded = ['id'];

    protected $with = [
        'township'
    ];


    public function township()
    {
        return $this->belongsTo(Township::class, 'township_id');
    }

    public function products()
    {
        return $this->hasMany(SupplierProduct::class, "supplier_product_id", "id");
    }

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

    public function setContactsAttribute($contacts)
    {
        $this->attributes['contacts'] = serialize($contacts);
    }

    /**
     * unserializes contacts attribute before spitting out from database
     *
     * @return mixed
     */
    public function getContactsAttribute()
    {
        if (empty($this->attributes['contacts']) || is_null($this->attributes['contacts'])) {
            return [];
        }

        return unserialize($this->attributes['contacts']);
    }
    public function setAddressesAttribute($address)
    {
        $this->attributes['addresses'] = serialize($address);
    }

    /**
     * unserializes address attribute before spitting out from database
     *
     * @return mixed
     */
    public function getAddressesAttribute()
    {
        if (empty($this->attributes['addresses']) || is_null($this->attributes['addresses'])) {
            return [];
        }

        return unserialize($this->attributes['addresses']);
    }
    public function setGiroAttribute($giro)
    {
        $this->attributes['giro'] = serialize($giro);
    }

    /**
     * unserializes Giro attribute before spitting out from database
     *
     * @return mixed
     */
    public function getGiroAttribute()
    {
        if (empty($this->attributes['giro']) || is_null($this->attributes['giro'])) {
            return [];
        }

        return unserialize($this->attributes['giro']);
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
