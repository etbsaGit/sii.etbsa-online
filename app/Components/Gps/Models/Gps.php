<?php

namespace App\Components\Gps\Models;

use Carbon\Carbon;
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
    public function group()
    {
        return $this->belongsTo(GpsGroup::class,'gps_group_id');
    }
}
