<?php

namespace App\Components\Gps\Models;

use Illuminate\Database\Eloquent\Model;

class GpsChips extends Model
{
    protected $table = 'gps_chips';

    protected $fillable = [
        'sim',
        'cuenta',
        'imei',
        'costo',
        'fecha_activacion',
        'fecha_renovacion',
        'fecha_cancelacion',
        'descripcion',
    ];
    /**
     * Get the gps that owns the chip.
     */
    public function gps()
    {
        return $this->belongsTo(Gps::class, 'gps_id');
    }

    public function scopeOfSim($query, $sim)
    {
        if ($sim === null || $sim === '') {
            return false;
        }

        return $query->where('sim', 'like', "%{$sim}%");
    }
    public function scopeOfImei($query, $sim)
    {
        if ($sim === null || $sim === '') {
            return false;
        }

        return $query->where('imei', 'like', "%{$sim}%");
    }

    public function scopeOfMonth($query, $v)
    {
        if ($v === null || $v === '') {
            return false;
        }
        return $query->whereMonth('fecha_renovacion', $v);
    }

    public function scopeOfYear($query, $v)
    {
        if ($v === null || $v === '') {
            return false;
        }
        return $query->whereYear('fecha_renovacion', $v);
    }

    public function scopeOfAssigned($q, $v)
    {
        if ($v === null || $v == false) {
            return false;
        }
        
        return $q->whereNotNull('gps_id');
    }

    public function scopeOfDeallocated($q, $v)
    {
        if ($v === null || $v == false) {
            return false;
        }

        return $q->whereNull('gps_id');
    }
}
