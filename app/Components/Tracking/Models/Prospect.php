<?php

namespace App\Components\Tracking\Models;

use App\Components\Common\Models\Estate;
use App\Components\Customers\Models\Customers;
use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Components\Common\Models\Township;

class Prospect extends Model
{
    protected $table = 'prospect';
    protected $primaryKey = 'id';
    protected $with = ['township'];

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'address',
        'town',
        'is_moral',
        'rfc',
        'company',
        'customer_id',
        'estate_id',
        'township_id',
        'segmentacion',
        'rating',
        'capacidad_tech',
        'registered_by',
    ];
    protected $appends = ['tracking_count'];

    /**
     * the owner of the prospect
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tracking()
    {
        return $this->hasMany(TrackingProspect::class, 'prospect_id');
    }

    public function estate()
    {
        return $this->belongsTo(Estate::class, 'estate_id');
    }

    public function township()
    {
        return $this->belongsTo(Township::class, 'township_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    public function getTrackingCountAttribute()
    {
        return $this->tracking()->whereHas('estatus', function ($q) {
            return $q->where('key', 'activo');
        })->count();
    }

    public function scopeSearch($query, string $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        });
    }
}
