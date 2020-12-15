<?php

namespace App\Components\Common\Models;

use Illuminate\Database\Eloquent\Model;
use  App\Components\Tracking\Models\TrackingProspect;

class Estatus extends Model
{

    const ESTATUS_ACTIVO = 'activo';
    const ESTATUS_FINALIZADO = 'finalizado';
    const ESTATUS_FORMALIZADO = 'formalizado';
    const ESTATUS_AUTORIZADO = 'autorizado';


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
    protected $fillable = ['title','description','key'];

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
        return $this->hasMany(TrackingProspect::class,'id');
    }
}
