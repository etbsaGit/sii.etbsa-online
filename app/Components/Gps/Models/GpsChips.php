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
    ];
    /**
     * Get the gps that owns the chip.
     */
    public function gps()
    {
        return $this->belongsTo(Gps::class);
    }
}
