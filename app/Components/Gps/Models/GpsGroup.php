<?php

namespace App\Components\Gps\Models;

use Illuminate\Database\Eloquent\Model;

class GpsGroup extends Model
{
    protected $table = 'gps_groups';

    protected $fillable = [
        'name',
        'agency',
        'department',
        'razon_social',
        'rfc',
        'description',
    ];

    protected $appends = ['gps_count'];

    /**
     * get the file count attribute
     *
     * @return mixed
     */
    public function getGpsCountAttribute()
    {
        return $this->gps()->count();
    }
    /**
     * the gps on this group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gps()
    {
        return $this->hasMany(Gps::class, 'gps_group_id');
    }

    public function scopeOfName($query, $name)
    {
        if ($name === null || $name === '') {
            return false;
        }

        return $query->where('name', 'LIKE', "%{$name}%");
    }

    public function scopeOfAgency($q, $v)
    {
        if ($v === null || $v === '') {
            return false;
        }
        return $q->where('agency', $v);

    }
    public function scopeOfDepartment($q, $v)
    {
        if ($v === null || $v === '') {
            return false;
        }
        return $q->where('department', $v);

    }
}
