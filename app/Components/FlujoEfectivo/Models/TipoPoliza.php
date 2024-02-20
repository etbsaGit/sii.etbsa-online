<?php

namespace App\Components\FlujoEfectivo\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPoliza extends Model
{
    protected $table = 'tipo_polizas';
    protected $fillable = [
        'key',
        'title',
    ];
}
