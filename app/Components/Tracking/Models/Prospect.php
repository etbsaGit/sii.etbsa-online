<?php

namespace App\Components\Tracking\Models;

use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use  App\Components\Common\Models\Township;

class Prospect extends Model
{
    protected $table = 'prospect';
    protected $primaryKey = 'id';
    protected $with = ['township'];

    protected $fillable = ['full_name', 'phone', 'registered_by', 'email', 'rfc', 'town','township_id'];
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
     * the gps on this group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tracking()
    {
        return $this->hasMany(TrackingProspect::class, 'prospect_id');
    }
    public function township()
    {
        return $this->belongsTo(Township::class, 'township_id');
    }

    public function getTrackingCountAttribute()
    {
        return $this->tracking()->whereHas('estatus', function ($q) {
            return $q->where('key', 'activo');
        })->count();
    }

    public function scopeOfName($query, $name)
    {
        if ($name === null || $name === '') {
            return false;
        }

        return $query->where('full_name', 'LIKE', "%{$name}%")
            ->orWhere('phone', 'LIKE', "%{$name}%");
    }
}
