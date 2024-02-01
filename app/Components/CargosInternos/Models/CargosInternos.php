<?php


namespace App\Components\CargosInternos\Models;

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Department;
use App\Components\Common\Models\Estatus;
use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class CargosInternos extends Model
{
    protected $table = 'cargos_internos';
    protected $fillable = [
        'title',
        'description',
        'num_economico',
        'num_cliente',
        'nip',
        'concepto_cargo',
        'ot',
        'mano_obra',
        'refacciones',
        'kilometraje',
        'foraneo',
        'amount',
        'estatus_id',
        'created_by',
        'autorized_by',
        'applied_at',
        'schedule_date',
        'authorization_date',
    ];

    public function estatus()
    {
        return $this->belongsTo(Estatus::class, 'estatus_id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function autorizedBy()
    {
        return $this->belongsTo(User::class, 'autorized_by');
    }

    public function sucursales()
    {
        return $this->belongsToMany(
            Agency::class,
            'cargos_internos_sucursales',
            'cargo_interno_id',
            'sucursal_id'
        )
            ->using(CargosInternosSucursales::class)
            ->withPivot('percent', 'cargo_interno_id', 'sucursal_id', 'department_id')
            ->as('sucursales')->withTimestamps();
    }
    public function departaments()
    {
        return $this->belongsToMany(
            Department::class,
            'cargos_internos_sucursales',
            'cargo_interno_id',
            'department_id'
        )->using(CargosInternosSucursales::class)
            ->withPivot('percent', 'cargo_interno_id', 'sucursal_id', 'department_id')
            ->as('departamentos')->withTimestamps();
    }

    // public function cargosSucursal()
    // {
    //     return $this->hasMany(CargosInternosSucursales::class, 'cargo_interno_id');
    // }

    public function scopeSearch($query, string $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('id', 'like', "%{$search}%");
                $query->orWhere('ot', 'like', "%{$search}%");
            });
        });
    }
}