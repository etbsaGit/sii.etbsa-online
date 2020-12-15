<?php

namespace App\Components\Vehicle\Models;

use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicles';

    protected $guarded = ['id'];


    public function user()
    {
        return $this->belongsTo(User::class, 'responsable');
    }

    public function agency()
    {
        return $this->belongsTo('App\Components\Common\Models\Agency', 'sucursal');
    }

    public function dispersals()
    {
        return $this->hasMany(VehicleDispersal::class);
    }

    public function services()
    {
        return $this->hasMany(VehicleService::class);
    }

    // Filters Scope

    public function scopeOfMatricula($query, $name)
    {
        if ($name === null || $name === '') {
            return false;
        }

        return $query->where('matricula', 'like', "%{$name}%");
    }
}
