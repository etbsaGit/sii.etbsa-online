<?php

namespace App\Components\Common\Models;

use App\Components\Purchase\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Model;

class cClaveProdServ extends Model
{
    protected $connection = 'sqlite_cProductosUnidades';
    protected $table = 'c_ClaveProdServ';
    protected $primaryKey = 'c_ClaveProdServ';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $cast = [
        'c_ClaveProdServ' => 'string',
        'Descripción' => 'string',
        'IncluirIVAtrasladado' => 'string',
        'IncluirIEPStrasladado' => 'string',
        'Complementoquedebeincluir' => 'string',
        'FechaInicioVigencia' => 'date',
        'EstímuloFranjaFronteriza' => 'boolean',
        'Palabrassimilares' => 'string',
    ];

    public function purchaseOrder()
    {
        return $this->belongsToMany(PurchaseOrder::class, 'purchase_pivot_detail_products', 'clave_prod_sat', 'purchase_order_id', 'c_ClaveProdServ', 'id');
    }

    public function scopeSearch($query, string $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('c_ClaveProdServ', 'like', "{$search}%");
                $query->orWhere('Descripción', 'like', "%{$search}%");
                $query->orWhere('Palabrassimilares', 'like', "%{$search}%");
            });
        });
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['claveProdServ'] ?? null, function ($query, $claveProdServ) {
            $query->where(function ($query) use ($claveProdServ) {
                $query->orWhere('c_ClaveProdServ', 'like', '%' . $claveProdServ . '%');
            });
        })->when($filters['descripcion'] ?? null, function ($query, $descripcion) {
            $query->where(function ($query) use ($descripcion) {
                $query->orWhere('Descripción', 'like', '%' . $descripcion . '%');
            });
        })->when($filters['PalabraSimilares'] ?? null, function ($query, $PalabraSimilares) {
            $query->where(function ($query) use ($PalabraSimilares) {
                $query->orWhere('Palabrassimilares', 'like', '%' . $PalabraSimilares . '%');
            });
        });
    }


}