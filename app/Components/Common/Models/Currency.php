<?php

namespace App\Components\Common\Models;

use App\Components\Product\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
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
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
