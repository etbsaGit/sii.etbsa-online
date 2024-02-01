<?php

namespace App\Components\Gps\Models;

use Illuminate\Database\Eloquent\Model;

class GpsSupplier extends Model
{
    protected $table = 'gps_supplies_import';

    protected $fillable = [
        'sim',
        'imei',
        'fecha',
    ];
}
