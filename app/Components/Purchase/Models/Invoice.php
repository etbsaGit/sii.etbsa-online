<?php

namespace App\Components\Purchase\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $guarded = ['id'];

    // protected $fillable = [
    //     'folio', 'folio_fiscal', 'invoice_date', 'amount', 'date_to_payment', 'payment_date'
    // ];

    public function invoiceable()
    {
        return $this->morphTo();
    }

    public function complementos()
    {
        return $this->belongsToMany(InvoiceComplemento::class, 'invoice_complemento_pivot_table', 'invoice_id');
    }


    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->orWhere(function ($query) use ($search) {
                $query->orWhere('folio', 'like', $search)
                    ->orWhere('folio_fiscal', 'like', "%{$search}%");
            });
        });
    }
}
