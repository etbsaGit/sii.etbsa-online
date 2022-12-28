<?php

namespace App\Components\Purchase\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceComplemento extends Model
{
    protected $table = 'invoices_complemento';
    protected $guarded = ['id'];

    // protected $fillable = [
    //     'folio', 'folio_fiscal', 'invoice_date', 'amount', 'date_to_payment', 'payment_date'
    // ];

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_complemento_pivot_table', 'invoice_complemento_id');
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
