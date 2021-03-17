<?php

namespace App\Components\Gps\Models;

use App\Components\Core\Utilities\Helpers;
use Carbon\Carbon;
use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class Gps extends Model
{
    use GpsTrait;
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
        'invoice_date',
        'payment_type',
        'estatus',
        'installation_date',
        'renew_date',
        'cancellation_date',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

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
        return $this->belongsTo(GpsChips::class, 'gps_chip_id');
    }

    public function historical()
    {
        return $this->hasMAny(GpsHistorical::class, 'gps_id', 'id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['name'] ?? null, function ($query, $name) {
            $query->where(function ($query) use ($name) {
                $query->orWhere('name', 'like', '%' . $name . '%');
            });
        })->when($filters['sim'] ?? null, function ($query, $sim) {
            $query->whereHas('chip', function ($query) use ($sim) {
                return $query->whereIn('sim', Helpers::commasToArray($sim));
            });
        })->when($filters['customer'] ?? null, function ($query, $customer) {
            $query->whereHas('gpsGroup', function ($query) use ($customer) {
                return $query->whereIn('id', Helpers::commasToArray($customer));
            });
        })->when($filters['agency'] ?? null, function ($query, $agency) {
            $query->whereHas('gpsGroup', function ($query) use ($agency) {
                return $query->where('agency', $agency);
            });
        })->when($filters['department'] ?? null, function ($query, $deparment) {
            $query->whereHas('gpsGroup', function ($query) use ($deparment) {
                return $query->where('department', $deparment);
            });
        })->when($filters['canceled'] ?? null, function ($query) {
            return $query->whereNotNull('cancellation_date');
        }, function ($query) {
            return $query->whereNull('cancellation_date');
        })->when($filters['defeated'] ?? null, function ($query) {
            return $query->whereYear('renew_date', '<=', Carbon::now()->year);
        });
    }

    public function scopeFilterCanceled($query)
    {
        $query->whereNotNull('cancellation_date');
    }

    public function scopeFilterByDateRange($query, $rangeDates)
    {
        $query->when($rangeDates ?? null, function ($query, $dates) {
            $query->where(function ($query) use ($dates) {
                // $dates = Helpers::commasToArray($dates) ?? null;
                if (count($dates) == 2) {
                    $from = date($dates[0]);
                    $to = date($dates[1]);
                    $query->whereBetween('installation_date', [$from, $to]);
                }
            });
        });
    }
}
