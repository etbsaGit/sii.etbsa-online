<?php

namespace App\Components\Tracking\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingHistoricalProspect extends Model
{
    protected $table = 'tracking_historical';
    protected $primaryKey = 'id';

    protected $fillable = ['message','tracking_id'];

    public function tracking()
    {
        return $this->belongsTo(TrackingProspect::class);
    }
}
