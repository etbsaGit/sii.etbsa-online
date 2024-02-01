<?php

namespace App\Components\Common\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\Purchase\Models\PurchaseOrder;

class CatMetodoPago extends Model
{
    protected $table = 'cat_metodo_pago';

    protected $primaryKey = 'code';


    public function purchase()
    {
        return $this->hasMany(PurchaseOrder::class, 'umetodo_pago_id', 'code');
    }
}
