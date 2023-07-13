<?php

namespace App\Components\Common\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';

    protected $fillable = ['purchase_number'];

    // function nextPurchaseNumber() {
    // return $this->purchases_number ++;
    // $currentInvoiceNumber = optional(Invoice::query()->orderByDesc('invoice_number')->limit(1)->first())->invoice_number;
    // }
}