<?php

namespace App\Components\Common\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRates extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exchange_rates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['value'];

    // public function scopeLastExchangeValue($query)
    // {
    //     return $query->lastest()->first()->value;
    // }
}
