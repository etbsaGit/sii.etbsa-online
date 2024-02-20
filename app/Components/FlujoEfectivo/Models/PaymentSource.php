<?php

namespace App\Components\FlujoEfectivo\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSource extends Model
{

    protected $table = 'payment_sources';
    protected $fillable = [
        'key',
        'title',
    ];
}
