<?php

namespace App\Components\Tracking\Models;

use App\Components\Common\Models\Estatus;
use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Components\Core\Utilities\Helpers;
use Auth;
use Carbon\Carbon;

class TrackingProspect extends Model
{
    protected $table = 'tracking_prospect';
    protected $primaryKey = 'id';

    protected $fillable = [
        'title', 'reference',
        'description_topic', 'price', 'currency', 'registered_by',
        'prospect_id', 'estatus_id',
        'assigned_by', 'attended_by',
        'agency_id', 'department_id', 'date_next_tracking', 'first_contact',
        'assertiveness', 'invoice', 'tracking_condition'
    ];

    protected $with = ['registered', 'assigned', 'attended', 'prospect'];

    public function estatus()
    {
        return $this->belongsTo(Estatus::class, 'estatus_id');
    }

    public function registered()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function assigned()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function attended()
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

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['folio'] ?? null, function ($query, $folio) {
            $query->where(function ($query) use ($folio) {
                $query->orWhere('id', 'like', '%' . $folio . '%');
            });
        })->when($filters['title'] ?? null, function ($query, $title) {
            $query->where(function ($query) use ($title) {
                $query->orWhere('title', 'like', '%' . $title . '%')
                    ->orWhere('reference', 'like', '%' . $title . '%');
            });
        })->when($filters['category'] ?? null, function ($query, $category) {
            $query->where(function ($query) use ($category) {
                $query->orWhere('title', 'like', '%' . $category . '%');
            });
        })->when($filters['prospect'] ?? null, function ($query, $prospect) {
            $query->whereHas('prospect', function ($query) use ($prospect) {
                $query->whereIn('id', Helpers::commasToArray($prospect));
            });
        })->when($filters['sellers'] ?? null, function ($query, $sellers) {
            $query->whereHas('attended', function ($query) use ($sellers) {
                $query->whereIn('id', Helpers::commasToArray($sellers));
            });
        })->when($filters['agencies'] ?? null, function ($query, $agencies) {
            $query->whereHas('agency', function ($query) use ($agencies) {
                $query->whereIn('id', Helpers::commasToArray($agencies));
            });
        })->when($filters['departments'] ?? null, function ($query, $departments) {
            $query->whereHas('department', function ($query) use ($departments) {
                $query->whereIn('id', Helpers::commasToArray($departments));
            });
        })->when($filters['estatus'] ?? null, function ($query, $estatus) {
            if ($estatus !== "todos") {
                $query->whereHas('estatus', function ($query) use ($estatus) {
                    $query->where('key', Helpers::commasToArray($estatus));
                });
            }
        }, function ($query) {
            $query->whereHas('estatus', function ($query) {
                $query->where('key', Estatus::ESTATUS_ACTIVO);
            });
        });
    }

    public function scopeFilterByDateRange($query, $rangeDates)
    {
        $query->when($rangeDates ?? null, function ($query, $dates) {
            $query->where(function ($query) use ($dates) {
                $dates = Helpers::commasToArray($dates) ?? null;
                if (count($dates) == 2) {
                    $from = date($dates[0]);
                    $to = date($dates[1]);
                    $query->whereBetween('updated_at', [$from, $to]);
                }
            });
        });
    }

    public function scopeFilterForManagers($query, User $user)
    {
        $query->when(
            $user->inGroup('Gerente') ?? null,
            function ($query) use ($user) {
                $query->whereIn('agency_id', $user->seller_agency->pluck('id'))
                    ->whereIn('department_id', $user->seller_type->pluck('id'))
                    ->orWhere('assigned_by', $user->id)
                    ->orWhere('attended_by', $user->id);
            },
            function ($query) use ($user) {
                $query->when($user->inGroup('Vendedor') ?? null, function ($query) use ($user) {
                    $query->where('attended_by', $user->id);
                });
            }
        );
    }
}
