<?php

namespace App\Components\Vehicle\Models;

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Department;
use App\Components\Common\Models\Estatus;
use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class VehicleService extends Model
{
    protected $table = 'vehicle_services';

    protected $guarded = ['id'];

    protected $fillable = [
        'mileage',
        'invoice',
        'cost',
        'reason',
        'reason_description',
        'created_by',
        'estatus_id',
        'vehicle_id',
        'agency_id',
        'department_id',
    ];

    protected $with = [
        'estatus:id,title,key', 'user:id,name',
        'agency:id,title', 'department:id,title'
    ];


    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function estatus()
    {
        return $this->belongsTo(Estatus::class, 'estatus_id');
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
