<?php

namespace App\Components\Requirements\Models;

use App\Components\Purchase\Models\Supplier;
use Illuminate\Database\Eloquent\Model;

class RequirementDocuments extends Model
{
    protected $table = 'requirement_documents';
    protected $primaryKey = 'id';
    protected $with = [];

    protected $fillable = ['name', 'key', 'description', 'types'];
    protected $appends = [];


    public function setTypesAttribute($types)
    {
        $this->attributes['types'] = serialize($types);
    }

    /**
     * unserializes types attribute before spitting out from database
     *
     * @return mixed
     */
    public function getTypesAttribute()
    {
        if (empty($this->attributes['types']) || is_null($this->attributes['types'])) {
            return [];
        }

        return unserialize($this->attributes['types']);
    }

    /**
     * Get all of the supplier that are assigned this requirement.
     */
    public function supplier()
    {
        return $this->morphedByMany(Supplier::class, 'requireable', 'requireables');
    }

    public function scopeSupplirsRequirements($query)
    {
        // TODO: Scope Para obtener solo los requsitos de Documentacion de Proveedores
        return $query;
    }
}