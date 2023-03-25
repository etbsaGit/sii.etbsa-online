<?php

namespace App\Components\Tracking\Models;

use App\Components\Common\Models\Currency;
use App\Components\Product\Models\Product;
use Illuminate\Database\Eloquent\Model;

class TrackingQuote extends Model
{
    protected $table = 'tracking_quotes';
    protected $primaryKey = 'id';
    protected $with = [];

    protected $fillable = [
        'date_due',
        'tracking_id',
        'currency_id',
        'exchange_value',
        'subtotal',
        'tax',
        'discount',
        'total',
        'payment_condition',
        'observation',
    ];
    protected $appends = ['label_payment'];


    public function tracking()
    {
        return $this->belongsTo(TrackingProspect::class, 'tracking_id');
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }


    public function products()
    {
        return $this->belongsToMany(Product::class, 'quote_products_pivot', 'quote_id', 'product_id')
            ->withPivot('price_unit', 'quantity', 'currency', 'subtotal')
            ->as('quotation');
    }

    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('id', 'like', "%{$search}%");
            });
        });
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when(
            $filters['trackingId'] ?? null,
            function ($query, $trackingId) {
                $query->where(function ($query) use ($trackingId) {
                    $query->orWhere('tracking_id', '=', $trackingId);
                });
            }
        );
    }

    public function getLabelPaymentAttribute()
    {
        $label = [
            "por_definir" => "Por Definir",
            "precio_lista" => "P. Lista",
            "contado" => "Contado",
            "jdf_2y" => "JDF 2 años",
            "jdf_5y" => "JDF 5 años",
            "precio_expo" => "Expo",
            "por_volumen" => "Precio Volumen",
            "renta_1" => "Arrendamiento",
            "renta_2" => "Arrendamiento 2 meses",
            "renta_3" => "Arrendamiento +3 meses",
        ];
        if ($this->payment_condition) {
            # code...
            return $label[$this->payment_condition];
        } else {
            return $label["por_definir"];
        }
    }
}
