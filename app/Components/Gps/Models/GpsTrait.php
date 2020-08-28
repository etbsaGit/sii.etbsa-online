<?php

namespace App\Components\Gps\Models;

use Illuminate\Support\Arr;

trait GpsTrait
{

    public function scopeOfTotalGps($query, $month)
    {
        return $query->whereMonth('installation_date', $month);
    }

    public function scopeOfNewGps($query)
    {
        return $query
            ->whereYear('installation_date', date('Y'))
            ->whereNull('cancellation_date');
    }

    public function scopeOfReNewGps($query)
    {
        return $query
            ->whereYear('installation_date', '<', date('Y'))
            ->whereYear('renew_date', date('Y', strtotime('1 year')))
            ->whereNull('cancellation_date');
    }
    public function scopeOfToReNewGps($query)
    {
        return $query
            ->whereYear('installation_date', '<', date('Y'))
            ->whereYear('renew_date', date('Y'))
            ->whereNull('cancellation_date');
    }
    public function scopeOfCanceledGps($query, $month)
    {
        return $query
            ->whereNotNull('cancellation_date')
            ->whereMonth('cancellation_date', $month)
            ->whereYear('cancellation_date', date('Y'));
            // ->orWhereMonth('installation_date', $month);
    }

}
