<?php

namespace App\Components\Gps\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class GpsChips extends Model
{
    protected $table = 'gps_chips';
    protected $primaryKey = 'sim';

    protected $fillable = [
        'sim',
        'cuenta',
        'imei',
        'costo',
        'fecha_activacion',
        'fecha_cancelacion',
        'descripcion',
    ];
    /**
     * Get the gps that owns the chip.
     */
    public function gps()
    {
        return $this->hasOne(Gps::class, 'gps_chip_id', 'sim');
    }

    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('sim', 'like', "%{$search}%")
                    ->orWhere('imei', 'like', "%{$search}%");
            });
        });
    }
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['sim'] ?? null, function ($query, $sim) {
            $query->where(function ($query) use ($sim) {
                $query->orWhere('sim', 'like', "%{$sim}%");
            });
        })->when($filters['month'] ?? null, function ($query, $month) {
            $query->where(function ($query) use ($month) {
                $query->orWhereMonth('fecha_activacion', $month)
                    ->orWhereMonth('fecha_cancelacion', $month);
            });
        })->when($filters['year'] ?? null, function ($query, $year) {
            $query->where(function ($query) use ($year) {
                $query->orWhereYear('fecha_activacion', $year)
                    ->orWhereYear('fecha_cancelacion', $year);
            });
        });

        // ->when($filters['assigned'] ?? null, function ($query) {
        //     // return $query->has('gps');
        // });
        // });
        // ->when($filters['assigned'], function ($query) {
        //     $query->where(function ($query) {
        //         $query->has('gps');
        //     });
        // });
        // ->when($filters['deallocated'] ?? null, function ($query, $deallocated) {
        //     if ($deallocated)
        //         $query->doesntHave('gps');
        // });
        // ->when(!!!$filters['canceled'], function ($query) {
        //     $query->whereNull('fecha_cancelacion');
        // }, function ($query) {
        //     $query->whereNotNull('fecha_cancelacion');
        // });
    }
}
