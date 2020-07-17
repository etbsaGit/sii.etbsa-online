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
        'gps_chip_id',
        'currency',
        'exchange_rate',
        'amount',
        'invoice',
        'payment_type',
        'installation_date',
        'renew_date',
        'cancellation_date',
        'description',
    ];

    /**
     * the group the gps belongs
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gpsGroup()
    {
        return $this->belongsTo(GpsGroup::class, 'gps_group_id');
    }

    /**
     * Get the chip record associated with the g.
     */
    public function chip()
    {
        return $this->hasOne(GpsChips::class);
    }

    public function scopeOfName($query, $name)
    {
        if ($name === null || $name === '') {
            return false;
        }

        return $query->where('name', 'LIKE', "%{$name}%");
    }

    public function scopeOfPayment($query, $v)
    {
        if ($v === null || $v === '') {
            return false;
        }

        return $query->where('payment_type', 'LIKE', "%{$v}%");
    }

    public function scopeOfMonth($query, $v)
    {
        if ($v === null || $v === '') {
            return false;
        }
        return $query->whereMonth('renew_date', $v);
    }

    public function scopeOfYear($query, $v)
    {
        if ($v === null || $v === '') {
            return false;
        }
        return $query->whereYear('renew_date', $v);
    }

    public function scopeOfGpsGroups($q, $v)
    {
        if ($v === false || $v === '' || count($v) == 0 || $v[0] == '') {
            return $q;
        }

        return $q->whereHas('gpsGroup', function ($q) use ($v) {
            return $q->whereIn('id', $v);
        });
    }
    public function scopeOfAgency($q, $v)
    {
        if ($v === null || $v === '') {
            return false;
        }
        return $q->whereHas('gpsGroup', function ($q) use ($v) {
            return $q->where('agency', $v);
        });
    }
    public function scopeOfDepartment($q, $v)
    {
        if ($v === null || $v === '') {
            return false;
        }

        return $q->whereHas('gpsGroup', function ($q) use ($v) {
            return $q->where('department', $v);
        });
    }

    public function scopeOfAssigned($q, $v)
    {
        if ($v === null || $v == false) {
            return false;
        }

        return $q->whereNotNull('gps_chip_id');
    }

    public function scopeOfDeallocated($q, $v)
    {
        if ($v === null || $v == false) {
            return false;
        }

        return $q->whereNull('gps_chip_id');
    }

    public function scopeOfExpired($q, $v)
    {
        if ($v === null || $v == false) {
            return false;
        }

        return $q->whereDate('renew_date', '<=', Carbon::now());
    }
    public function scopeOfRenewed($q, $v)
    {
        if ($v === null || $v == false) {
            return false;
        }

        return $q->whereYear('renew_date', '>=', Carbon::now()->addYear()->year);
    }
}
