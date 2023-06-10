<?php


namespace App\Components\CargosInternos\Models;

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Department;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CargosInternosSucursales extends Pivot
{
    protected $table = 'cargos_internos_sucursales';
    protected $fillable = [
        'percent',
        'cargo_interno_id',
        'sucursal_id',
        'department_id',
    ];

    public function cargoInterno()
    {
        return $this->belongsTo(CargosInternos::class, 'cargo_interno_id');
    }
    public function sucursal()
    {
        return $this->belongsTo(Agency::class, 'sucursal_id');
    }
    public function departamento()
    {
        return $this->belongsTo(Department::class, 'departamento_id');
    }
}