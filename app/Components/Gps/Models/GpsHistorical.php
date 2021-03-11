<?php

namespace App\Components\Gps\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\User\Models\User;

class GpsHistorical extends Model
{
    protected $table = 'gps_historical';

    protected $with = ['user', 'gpsGroup'];

    protected $fillable = [
        'name',
        'uploaded_by',
        'gps_group_id',
        'gps_chip_id',
        'invoice',
        'invoice_date',
        'amount',
        'currency',
        'exchange_rate',
        'payment_type',
        'installation_date',
        'renew_date',
        'cancellation_date',
        'description',
    ];

    public function gps()
    {
        return $this->belongsTo(Gps::class, 'gps_id');
    }

    public function gpsGroup()
    {
        return $this->belongsTo(GpsGroup::class, 'gps_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
