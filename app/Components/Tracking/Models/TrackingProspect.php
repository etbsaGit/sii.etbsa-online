<?php

namespace App\Components\Tracking\Models;

use App\Components\Common\Models\Estatus;
use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class TrackingProspect extends Model
{
    protected $table = 'tracking_prospect';
    protected $primaryKey = 'id';

    protected $fillable = ['title',
        'description_topic', 'price', 'registered_by',
        'prospect_id', 'estatus_id',
        'assigned_by', 'attended_by',
        'agency_id', 'department_id', 'date_next_tracking', 'first_contact',
    ];

    public function estatus()
    {
        return $this->belongsTo(Estatus::class, 'estatus_id');
    }

    public function registered_by()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function assigned_by()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function attended_by()
    {
        return $this->belongsTo(User::class, 'attended_by');
    }

    public function prospect()
    {
        return $this->belongsTo(Prospect::class, 'prospect_id');
    }

    public function agency()
    {
        return $this->belongsTo('App\Components\Common\Models\Agency', 'agency_id');
    }
    public function department()
    {
        return $this->belongsTo('App\Components\Common\Models\Department', 'department_id');
    }

    public function historical()
    {
        return $this->hasMany(TrackingHistoricalProspect::class, 'tracking_id', 'id');
    }

    public function scopeOfTitle($query, $name)
    {
        if ($name === null || $name === '') {
            return false;
        }

        return $query->where('title', 'like', "%{$name}%")
            ->orWhere('id', 'like', "%{$name}");
    }

    public function scopeOfProspect($q, $v)
    {
        if ($v === false || $v === '' || count($v) == 0 || $v[0] == '') {
            return $q;
        }

        return $q->whereHas('prospect', function ($q) use ($v) {
            return $q->whereIn('id', $v);
        });
    }
    public function scopeOfAgency($q, $v)
    {
        if ($v === false || $v === '' || count($v) == 0 || $v[0] == '') {
            return $q;
        }

        return $q->whereHas('agency', function ($q) use ($v) {
            return $q->whereIn('id', $v);
        });
    }
    public function scopeOfDepartment($q, $v)
    {
        if ($v === false || $v === '' || count($v) == 0 || $v[0] == '') {
            return $q;
        }

        return $q->whereHas('department', function ($q) use ($v) {
            return $q->whereIn('id', $v);
        });
    }
    public function scopeOfEstatus($q, $v)
    {
        if ($v === false || $v === '' || count($v) == 0 || $v[0] == '') {
            return $q;
        }

        return $q->whereHas('estatus', function ($q) use ($v) {
            return $q->whereIn('key', $v);
        });
    }

}
