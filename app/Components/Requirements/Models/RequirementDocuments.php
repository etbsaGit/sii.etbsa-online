<?php

namespace App\Components\Requirements\Models;

use Illuminate\Database\Eloquent\Model;

class RequirementDocuments extends Model
{
    protected $table = 'requirement_documents';
    protected $primaryKey = 'id';
    protected $with = [];

    protected $fillable = ['name', 'key', 'description', 'types'];
    protected $appends = [];

    public function scopeSupplirsRequirements($query)
    {
        // TODO: Scope Para obtener solo los requsitos de Documentacion de Proveedores
        return $query;
    }
}