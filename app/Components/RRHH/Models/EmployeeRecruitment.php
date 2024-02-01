<?php

namespace App\Components\RRHH\Models;

use App\Components\Common\Models\Estatus;
use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class EmployeeRecruitment extends Model
{
    protected $table = 'employee_recruitment';

    protected $guarded = ['id'];

    protected $fillable = [
        'actividades_experiencia', 'area_solcitante',
        'area_solicitante', 'areas_experiencia',
        'comision_puesto', 'competencias',
        'equipo_proporcionar', 'escolaridad_puesto',
        'especificacion_vacante', 'habilidades_puesto',
        'idiomas_puesto', 'manejo_puesto', 'motivo_vacante',
        'puesto', 'rango_edad_puesto', 'sexo_puesto',
        'sucursal_solcitante', 'sueldo_puesto',
        'tiempo_experiencia', 'tipo_vacante',
        'created_by', 'estatus_id'
    ];
    protected $with = ['estatus:id,title,key', 'solicitante:id,name'];

    public function estatus()
    {
        return $this->belongsTo(Estatus::class, 'estatus_id');
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function setCompetenciasAttribute($competencias)
    {
        $this->attributes['competencias'] = serialize($competencias);
    }
    public function getCompetenciasAttribute()
    {
        if (empty($this->attributes['competencias']) || is_null($this->attributes['competencias'])) {
            return [];
        }
        return unserialize($this->attributes['competencias']);
    }

    public function setEquipoProporcionarAttribute($equipo_proporcionar)
    {
        $this->attributes['equipo_proporcionar'] = serialize($equipo_proporcionar);
    }
    public function getEquipoProporcionarAttribute()
    {
        if (empty($this->attributes['equipo_proporcionar']) || is_null($this->attributes['equipo_proporcionar'])) {
            return [];
        }
        return unserialize($this->attributes['equipo_proporcionar']);
    }

    public function setSexoPuestoAttribute($sexo_puesto)
    {
        $this->attributes['sexo_puesto'] = serialize($sexo_puesto);
    }
    public function getSexoPuestoAttribute()
    {
        if (empty($this->attributes['sexo_puesto']) || is_null($this->attributes['sexo_puesto'])) {
            return [];
        }
        return unserialize($this->attributes['sexo_puesto']);
    }

    public function setRangoEdadPuestoAttribute($rango_edad_puesto)
    {
        $this->attributes['rango_edad_puesto'] = serialize($rango_edad_puesto);
    }
    public function getRangoEdadPuestoAttribute()
    {
        if (empty($this->attributes['rango_edad_puesto']) || is_null($this->attributes['rango_edad_puesto'])) {
            return [];
        }
        return unserialize($this->attributes['rango_edad_puesto']);
    }
}
