<?php

namespace App\Components\Product\Models;

use App\Components\Common\Models\Agency;
use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    //product_inventories
    protected $table = 'product_inventories';

    protected $fillable = [
        'product_id',
        'location',
        'assigned_branch',
        'num_serie',
        'num_serie_motor',
        'num_economico',
        'costo_jd',
        'costo_etbsa',
        'invoice',
        'invoice_date',
        'model',
        'arrival_date',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function location_branch()
    {
        return $this->belongsTo(Agency::class, 'location');
    }
    public function assigned_branch()
    {
        return $this->belongsTo(Agency::class, 'assigned_branch');
    }
}