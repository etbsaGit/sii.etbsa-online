<?php

namespace App\Components\Common\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\Purchase\Models\PurchaseOrder;

class CatUsoCfdi extends Model
{
    protected $table = 'cat_uso_cfdi';
    // protected $primaryKey = 'clave';
    protected $casts = [
        'clave' => 'String'
    ];



    public function purchase()
    {
        return $this->hasMany(PurchaseOrder::class, 'uso_cfdi_id', 'id');
    }
}
