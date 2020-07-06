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
}
