<?php

namespace App\Components\Common\Models;

use App\Components\Purchase\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Model;

class CatFormaPago extends Model
{
    protected $table = 'cat_forma_pago';

    protected $primaryKey = 'code';


    public function purchase()
    {
        return $this->hasMany(PurchaseOrder::class, 'forma_pago_id', 'code');
    }
}
