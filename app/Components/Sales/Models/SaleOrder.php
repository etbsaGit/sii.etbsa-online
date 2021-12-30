<?php

namespace App\Components\Sales\Models;

use Illuminate\Database\Eloquent\Model;

class SaleOrder extends Model
{
    protected $table = 'sale_orders';
    protected $primaryKey = 'id';
    protected $with = [];

    protected $fillable = [];
    protected $appends = [];
}
