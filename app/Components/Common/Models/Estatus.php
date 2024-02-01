<?php

namespace App\Components\Common\Models;

use App\Components\CargosInternos\Models\CargosInternos;
use App\Components\Purchase\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use App\Components\Tracking\Models\TrackingProspect;

class Estatus extends Model
{

    const ESTATUS_ACTIVO = 'activo';
    const ESTATUS_FINALIZADO = 'finalizado';
    const ESTATUS_FORMALIZADO = 'formalizado';
    const ESTATUS_AUTORIZADO = 'autorizado';
    const ESTATUS_PENDIENTE = 'pendiente';
    const ESTATUS_RECIBIDO = 'recibido';
    const ESTATUS_ENVIADA = 'enviada';
    const ESTATUS_VERIFICADO = 'verificado';
    const ESTATUS_PROGRAMAR_PAGO = 'programar_pago';
    const ESTATUS_POR_PAGAR = 'por_pagar';
    const ESTATUS_PAGADA = 'pagada';
    const ESTATUS_DENEGAR = 'denegar';
    const ESTATUS_DISPERSADO = 'flotilla.dispersado';
    const ESTATUS_DESPACHADO = 'flotilla.despachado';
    const ESTATUS_EnSERVICIO = 'flotilla.enServicio';
    const ESTATUS_SERVICIO_TERMINADO = 'flotilla.servicio.terminado';
    const ESTATUS_SIN_DOCUMENTO = 'documento.none';
    const ESTATUS_DOCUMENTO_VALIDO = 'documento.valido';
    const ESTATUS_DOCUMENTO_INVALIDO = 'documento.invalido';
    const ESTATUS_DOCUMENTO_REVICION = 'documento.revicion';
    const ESTATUS_PAGADA_PORFACTURAR = 'pagada.porFacturar';
    const ESTATUS_POR_FACTURAR = 'por_facturar';


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'estatus';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'key'];

    /**
     * the rules of the Group for validation before persisting
     *
     * @var array
     */
    public static $rules = array(
        'title' => 'required|unique:estatus,title',
        'description' => 'required',
        'key' => 'required|unique:estatus,key'
    );

    public function tracking()
    {
        return $this->hasMany(TrackingProspect::class, 'id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'estatus_id', 'id');
    }

    public function cargos_internos()
    {
        return $this->hasMany(CargosInternos::class, 'estatus_id', 'id');
    }
}