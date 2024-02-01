<?php

namespace App\Components\Purchase\Models;

use App\Components\Common\Models\CatUnitSat;
use Illuminate\Database\Eloquent\Model;

class SupplierProduct extends Model
{
    protected $table = 'supplier_products';
    protected $guarded = ['id'];

    protected $with = [];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function unitSat()
    {
        return $this->belongsTo(CatUnitSat::class, 'cat_unit_sat_id');
    }

    public function purchaseDetail()
    {
        return $this->belongsToMany(PurchaseOrder::class, 'purchase_detail', 'supplier_product_id');
    }

    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        });
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when(
            $filters['supplier_id'] ?? null,
            function ($query, $supplier_id) {
                $query->whereHas('supplier', function ($query) use ($supplier_id) {
                    return $query->where('id', $supplier_id);
                });
            }
        );
    }
}
