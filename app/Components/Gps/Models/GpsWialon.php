<?php

namespace App\Components\Gps\Models;

use Illuminate\Database\Eloquent\Model;

class GpsWialon extends Model
{
    protected $table = 'gps_wialon_import';

    protected $fillable = [
        'nombre',
        'sim',
        'uiid',
    ];
}
