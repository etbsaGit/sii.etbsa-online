<?php

namespace App\Components\Marketing\Models;

use Illuminate\Database\Eloquent\Model;

class Marketing extends Model
{
    protected $table = "marketing_import";

    protected $fillable = [
        "ID_SUC",
        "SUCURSAL",
        "NO_ECO",
        "NO_DOCUMENTO",
        "TIPO_DOCUMENTO",
        "NO_DOC_INV",
        "MODULO",
        "SERIE_FISCAL",
        "FOLIO_FISCAL",
        "TIPO_DE_VENTA",
        "FECHA_REQ",
        "FECHA_FACTURA",
        "MES",
        "ANIO",
        "MARCA",
        "MODELO",
        "DESCRIPCION_PRODUCTO",
        "NIP",
        "CLAVE_CLIENTE",
        "NOMBRE_CLIENTE",
        "VENDEDOR",
        "EMAIL",
        "CALLE",
        "CIUDAD",
        "ESTADO",
        "CP",
        "RFC_COMPANIA",
        "PAGO_EFECTIVO",
        "PAGADO_TARJETA_CREDITO",
        "PAGADO_CHEQUE",
        "IMPUESTO_REG",
        "MONEDA",
        "TIPO_CAMBIO",
        "VALOR_FORANEO",
        "METODO_PAGO",
        "PRECIO_VENTA",
        "IMPUESTO_INVENTARIO",
        "SUBTOTAL",
        "IMPUESTO",
        "TOTAL",
        "TOTAL_COSTO",
        "MARGEN",
        "PORCENTAJE_MARGEN",
    ];

    public function scopeOfProductName($query, $v)
    {
        if ($v === null || $v === '') {
            return false;
        }
        return $query->where('DESCRIPCION_PRODUCTO', 'like', "%{$v}%");
    }
    public function scopeOfCustomerName($query, $v)
    {
        if ($v === null || $v === '') {
            return false;
        }
        return $query->where('NOMBRE_CLIENTE', 'like', "%{$v}%");
    }

    public function scopeOfMonths($q, $v)
    {
        if ($v === false || $v === '' || count($v) == 0 || $v[0] == '') {
            return $q;
        }
        return $q->whereIn('MES', $v);
    }

    public function scopeOfYears($q, $v)
    {
        if ($v === false || $v === '' || count($v) == 0 || $v[0] == '') {
            return $q;
        }
        return $q->whereIn('ANIO', $v);
    }
    public function scopeOfAgencies($q, $v)
    {
        if ($v === false || $v === '' || count($v) == 0 || $v[0] == '') {
            return $q;
        }
        return $q->whereIn('SUCURSAL', $v);
    }
    public function scopeOfTypeSale($q, $v)
    {
        if ($v === false || $v === '' || count($v) == 0 || $v[0] == '') {
            return $q;
        }
        return $q->whereIn('TIPO_DE_VENTA', $v);
    }
    public function scopeOfSeller($q, $v)
    {
        if ($v === false || $v === '' || count($v) == 0 || $v[0] == '') {
            return $q;
        }
        return $q->whereIn('VENDEDOR', $v);
    }
    public function scopeOfBrands($q, $v)
    {
        if ($v === false || $v === '' || count($v) == 0 || $v[0] == '') {
            return $q;
        }
        return $q->whereIn('MARCA', $v);
    }

    public function scopeOfCurrencyMXN($q, $v)
    {
        if ($v === null || $v === false) {
            return false;
        }
        return $q->whereNull('MONEDA');
    }
    public function scopeOfCurrencyUSD($q, $v)
    {
        if ($v === null || $v === false) {
            return false;
        }
        return $q->whereNotNull('MONEDA');
    }
}
