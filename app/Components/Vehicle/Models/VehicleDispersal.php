<?php

namespace App\Components\Vehicle\Models;

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Department;
use App\Components\Common\Models\Estatus;
use App\Components\Core\Utilities\Helpers;
use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class VehicleDispersal extends Model
{
    protected $table = 'vehicle_dispersal';

    // protected $guarded = ['id'];

    protected $fillable = [
        'vehicle_id',
        'drum_dispersion',
        'fuel_odometer',
        'mileage_last',
        'mileage_actual',
        'reason_dispersal',
        'reason_data',
        'reason_note',
        'fuel_id',
        'fuel_liters',
        'fuel_cost_liter',
        'tickets_info',
        'dispatched_amount',
        'ticket_card',
        'date_to_disperse',
        'agency_id',
        'department_id',
        'estatus_id',
        'created_by',
        'updated_by',
    ];

    protected $with = [
        'estatus:id,title,key', 'agency:id,title', 'department:id,title'
    ];

    // Relationship
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function ticketcard()
    {
        return $this->belongsTo(VehicleTicketCard::class, 'ticket_card');
    }

    public function estatus()
    {
        return $this->belongsTo(Estatus::class, 'estatus_id');
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function fuel()
    {
        return $this->belongsTo(VehicleFuel::class, 'fuel_id');
    }

    // Attributes
    public function setTicketsInfoAttribute($ticket)
    {
        $this->attributes['tickets_info'] = serialize($ticket);
    }
    public function getTicketsInfoAttribute()
    {
        if (empty($this->attributes['tickets_info']) || is_null($this->attributes['tickets_info'])) {
            return [];
        }
        return unserialize($this->attributes['tickets_info']);
    }
    public function setReasonDataAttribute($reason_data)
    {
        $this->attributes['reason_data'] = serialize($reason_data);
    }
    public function getReasonDataAttribute()
    {
        if (empty($this->attributes['reason_data']) || is_null($this->attributes['reason_data'])) {
            return [];
        }
        return unserialize($this->attributes['reason_data']);
    }

    // Scopes

    // Search
    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('id', 'like', $search)
                    ->orWhereHas('sucursal', function ($query) use ($search) {
                        return $query->where('agencies.title', 'like', "%{$search}%");
                    })->orWhereHas('responsable', function ($query) use ($search) {
                        return $query->where('users.name', 'like', "%{$search}%");
                    });
            });
        });
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['reason_dispersal'] ?? null, function ($query, $reason_dispersal) {
            return $query->where('reason_dispersal', $reason_dispersal);
        })->when($filters['responsable'] ?? null, function ($query, $responsable) {
            $query->whereHas('responsable', function ($query) use ($responsable) {
                return $query->where('id', $responsable);
            });
        })->when($filters['agencie'] ?? null, function ($query, $agencie) {
            $query->whereHas('agency', function ($query) use ($agencie) {
                return $query->where('id', $agencie);
            });
        })->when($filters['dates'] ?? null, function ($query, $dates) {
            return $query->whereBetween('date_to_disperse', [$dates[0], $dates[1]]);
        })->when($filters['estatus'] ?? null, function ($query, $estatus) {
            if ($estatus !== "todos") {
                $query->whereHas('estatus', function ($query) use ($estatus) {
                    $query->where('key', Helpers::commasToArray($estatus));
                });
            }
        }, function ($query) {
            $query->whereHas('estatus', function ($query) {
                $query->where('key', Estatus::ESTATUS_PENDIENTE);
            });
        });
    }

    public function scopeFilterPermission($query, User $user)
    {
        $query->when(
            ($user->hasPermission('flotilla.admin') ||
                $user->hasPermission('flotilla.all.list') ||
                $user->isSuperUser()),
            function ($query) {
                return $query;
            },
            function ($query) use ($user) {
                $query->where('created_by', $user->id);
            }
        );
    }
}
