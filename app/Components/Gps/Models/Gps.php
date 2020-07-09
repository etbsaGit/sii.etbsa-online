<?php

namespace App\Components\Gps\Models;

use Illuminate\Database\Eloquent\Model;

class Gps extends Model
{
    protected $table = 'gps';

    protected $fillable = [
        'name',
        'uploaded_by',
        'gps_group_id',
        'sim',
        'imei',
        'cost',
        'amount',
        'activation_date',
        'due_date',
    ];

    /**
     * the group the gps belongs
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gpsGroup()
    {
        return $this->belongsTo(GpsGroup::class, 'gps_group_id');
    }

    /**
     * Get the chip record associated with the g.
     */
    public function chip()
    {
        return $this->hasOne(GpsChips::class, 'gps_chip_id');
    }

    public function scopeOfName($query, $name)
    {
        if ($name === null || $name === '') {
            return false;
        }

        return $query->where('name', 'LIKE', "%{$name}%");
    }

    public function scopeOfMonth($query, $v)
    {
        if ($v === null || $v === '') {
            return false;
        }
        return $query->whereMonth('due_date', $v);
    }

    public function scopeOfYear($query, $v)
    {
        if ($v === null || $v === '') {
            return false;
        }
        return $query->whereYear('due_date', $v);
    }

    public function scopeOfGpsGroups($q, $v)
    {
        if ($v === false || $v === '' || count($v) == 0 || $v[0] == '') {
            return $q;
        }

        return $q->whereHas('gpsGroup', function ($q) use ($v) {
            return $q->whereIn('id', $v);
        });
    }
}
