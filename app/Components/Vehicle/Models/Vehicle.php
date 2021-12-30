<?php

namespace App\Components\Vehicle\Models;

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Estatus;
use App\Components\Core\Utilities\Helpers;
use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicles';

    // protected $guarded = ['id'];

    protected $fillable = [
        'agency_id', 'brand', 'fuel', 'fuel_id', 'fuel_odometer',
        'mileage_last', 'mileage_actual', 'performance_fuel', 'matricula', 'liters_max_fuel', 'liters_per_week',
        'mileage_last_service', 'mileage_range_service', 'model', 'serie',
        'ticket_card', 'user_id', 'year', 'bidon_fuel'
    ];

    protected $with = [
        'responsable',
        'sucursal:id,title',
        'combustible:id,name,cost_lt',
        'ticket'
    ];


    /**
     * Get the chip record associated with the g.
     */
    public function ticket()
    {
        return $this->belongsTo(VehicleTicketCard::class, 'ticket_card');
    }

    public function sucursal()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function combustible()
    {
        return $this->belongsTo(VehicleFuel::class, 'fuel_id');
    }

    public function dispersals()
    {
        return $this->hasMany(VehicleDispersal::class, 'vehicle_id');
    }

    public function services()
    {
        return $this->hasMany(VehicleService::class, 'vehicle_id');
    }

    public function fuel()
    {
        return $this->belongsTo(VehicleFuel::class, 'fuel_id');
    }

    // Attributes
    protected $appends = ['can_dispersal', 'can_service'];

    public function canDispersal()
    {
        $estatus = $this->dispersals()->latest()->first()->estatus->key ?? null;
        if (is_null($estatus)) {
            return true;
        }
        return $estatus == Estatus::ESTATUS_DESPACHADO;
    }
    public function canServices()
    {
        $estatus = $this->services()->latest()->first()->estatus->key ?? null;
        if (is_null($estatus)) {
            return true;
        }
        return $estatus == Estatus::ESTATUS_DISPERSADO;
    }

    public function getCanDispersalAttribute()
    {
        return $this->canDispersal();
    }

    public function getCanServiceAttribute()
    {
        return $this->canServices();
    }

    // Search
    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('id', 'like', $search)
                    ->orWhere('matricula', 'like', "%{$search}%")
                    ->orWhere('serie', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
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

        $query->when($filters['matricula'] ?? null, function ($query, $matricula) {
            $query->where(function ($query) use ($matricula) {
                $query->orWhere('matricula', 'like', "%{$matricula}%");
            });
        })->when($filters['serie'] ?? null, function ($query, $serie) {
            $query->where(function ($query) use ($serie) {
                $query->orWhere('serie', 'like', "%{$serie}%");
            });
        })->when($filters['ticket_card'] ?? null, function ($query, $ticket_card) {
            $query->where(function ($query) use ($ticket_card) {
                $query->orWhere('ticket_card', 'like', "%{$ticket_card}%");
            });
        })->when($filters['responsable'] ?? null, function ($query, $responsable) {
            $query->whereHas('responsable', function ($query) use ($responsable) {
                return $query->where('id', $responsable);
            });
        })->when($filters['agencie'] ?? null, function ($query, $agencie) {
            $query->whereHas('sucursal', function ($query) use ($agencie) {
                return $query->where('id', $agencie);
            });
        });
    }

    public function scopeFilterPermission($query, User $user)
    {
        $query->when(
            ($user->inGroup('Gerente') && $user->hasPermission('flotilla.admin'))
                || $user->hasPermission('flotilla.all.list')
                || $user->isSuperUser(),
            function ($query) {
                return $query;
            },
            function ($query) use ($user) {
                $query->where('user_id', $user->id);
                // $query->when($user->inGroup('Compras') ?? null, function ($query) use ($user) {
                //     $query->where('user_id', $user->id);
                // });
            }
        );
    }
}
