<?php

namespace App\Components\Vehicle\Models;

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Estatus;
use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class VehicleService extends Model
{
    protected $table = 'vehicle_services';

    protected $guarded = ['id'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'solicitante');
    }

    public function estatus()
    {
        return $this->belongsTo(Estatus::class, 'estatus_id');
    }

    public function cargoA()
    {
        return $this->belongsTo(Agency::class, 'suc_cargo');
    }

    // Filters Scope

    public function scopeOfFolio($query, $name)
    {
        if ($name === null || $name === '') {
            return false;
        }

        return $query->where('title', 'like', "%{$name}%")
            ->orWhere('id', 'like', "%{$name}");
    }

    public function scopeOfEstatus($q, $v)
    {
        if ($v === false || $v === '' || count($v) == 0 || $v[0] == '') {
            return $q;
        }

        return $q->whereHas('estatus', function ($q) use ($v) {
            return $q->whereIn('key', $v);
        });
    }

    public function scopeOfAgency($q, $v)
    {
        if ($v === false || $v === '' || count($v) == 0 || $v[0] == '') {
            return $q;
        }

        return $q->whereHas('agency', function ($q) use ($v) {
            return $q->whereIn('id', $v);
        });
    }
}
