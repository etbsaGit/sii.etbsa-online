<?php

namespace App\Components\Purchase\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PurchaseAgency extends Pivot
{
    protected $table = 'purchase_agency_pivot_table';
}
