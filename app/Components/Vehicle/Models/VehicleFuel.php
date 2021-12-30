<?php

namespace App\Components\Vehicle\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleFuel extends Model
{
    protected $table = 'vehicle_fuels';

    // protected $guarded = ['id'];

    protected $fillable = ['name', 'cost_lt'];
}
