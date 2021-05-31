<?php

namespace App\Components\Vehicle\Models;

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Department;
use App\Components\Common\Models\Estatus;
use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class VehicleDispersal extends Model
{
    protected $table = 'vehicle_dispersal';

    protected $guarded = ['id'];

    protected $fillable = [
        'actual_mileage',
        'created_by',
        'estatus_id',
        'fuel_lts',
        'fuel_odometer',
        'cost_fuel_lts',
        'amount',
        'last_mileage',
        'reason',
        'reason_description',
        'vehicle_id',
        'agency_id',
        'department_id',
    ];

    protected $with = [
        'estatus:id,title,key', 'solicitante:id,name',
        'agency:id,title', 'department:id,title'
    ];

    // Relationship
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
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
}
