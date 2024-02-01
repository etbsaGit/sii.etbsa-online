<?php

namespace App\Components\Common\Models;

use Illuminate\Database\Eloquent\Model;

class CatTracking extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'currency';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];


    public function tracking()
    {
        return $this->hasMany('App\Components\Tracking\Models\TrackingProspect');
    }
}
