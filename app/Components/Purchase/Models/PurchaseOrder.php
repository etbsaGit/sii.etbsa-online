<?php

namespace App\Components\Purchase\Models;

use Illuminate\Database\Eloquent\Model;

use App\Components\User\Models\User;
use App\Components\Common\Models\Estatus;
use App\Components\Common\Models\Agency;
use App\Components\Common\Models\CatFormaPago;
use App\Components\Common\Models\CatMetodoPago;
use App\Components\Common\Models\CatUnitSat;
use App\Components\Common\Models\CatUsoCfdi;
use App\Components\Common\Models\Department;
use App\Components\Common\Models\Document;
use App\Components\Common\Models\Message;
use App\Components\Core\Utilities\Helpers;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';
    protected $guarded = ['id'];
    protected $appends = ['products', 'charges'];

    // protected $with = [
    //     'estatus', 'elaborated', 'supplier:id,business_name,rfc'
    // ];

    public function estatus()
    {
        return $this->belongsTo(Estatus::class, 'estatus_id');
    }
    public function type()
    {
        return $this->belongsTo(PurchaseType::class, 'purchase_type_id');
    }

    public function elaborated()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function usocfdi()
    {
        return $this->belongsTo(CatUsoCfdi::class, 'uso_cfdi', 'clave');
    }
    public function metodopago()
    {
        return $this->belongsTo(CatMetodoPago::class, 'metodo_pago', 'clave');
    }
    public function formapago()
    {
        return $this->belongsTo(CatFormaPago::class, 'forma_pago', 'clave');
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'messageable');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function invoice()
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }
    public function ship()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function purchase_concept()
    {
        return $this->belongsTo(PurchaseConcept::class, 'purchase_concept_id');
    }

    public function chargeAgency()
    {
        return $this->belongsToMany(Agency::class, 'purchase_agency_pivot_table', 'purchase_order_id')
            ->withPivot('department_id', 'percent')
            ->as('charge');
    }

    public function chargeDepartment()
    {
        return $this->belongsToMany(Department::class, 'purchase_agency_pivot_table', 'purchase_order_id')
            ->withPivot('agency_id', 'percent')
            ->as('charge');
    }

    public function detailPurchase()
    {
        return $this->belongsToMany(PurchaseConceptProduct::class, 'purchase_pivot_detail_products', 'purchase_order_id', 'concept_product_id')
            ->withPivot('description', 'unit_id', 'qty', 'price', 'discount', 'subtotal')
            ->as('detail');
    }

    public function files()
    {
        return $this->morphMany('App\Components\File\Models\File', 'fileable');
    }

    public function setChargesAttribute($charges)
    {
        $this->attributes['charges'] = serialize($charges);
    }

    public function getChargesAttribute()
    {
        if (empty($this->attributes['charges']) || is_null($this->attributes['charges'])) {
            return [];
        }
        $charges = unserialize($this->attributes['charges']);
        return array_map(function ($item) {
            return [
                'agency' => \DB::table('agencies')->where('id', '=', $item['agency_id'])->get(['id', 'code', 'title'])->toArray()[0],
                'department' => \DB::table('departments')->where('id', '=', $item['depto_id'])->get(['id', 'title'])->toArray()[0],
                'percent' => $item['percent']
            ];
        }, $charges);
    }

    public function getProductsAttribute()
    {
        $detail = collect($this->detailPurchase);
        return $detail->map(function ($item) {
            return [
                'group' => [
                    'id' => $item->id,
                    'name' => $item->name
                ],
                'unit' =>  CatUnitSat::where('id', '=', $item->detail->unit_id)->first(),
                'description' => $item->detail->description,
                'qty' => $item->detail->qty,
                'price' => $item->detail->price,
                'discount' => $item->detail->discount,
                'subtotal' => $item->detail->subtotal,
            ];
        });
    }

    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->orWhere(function ($query) use ($search) {
                $query->orWhere('id', 'like', $search)
                    // ->orWhere('authorization_token', 'like', "%{$search}%")
                    // ->orWhereHas('sucursal', function ($query) use ($search) {
                    //     return $query->where('agencies.title', 'like', "%{$search}%");
                    // })
                    ->orWhere('charges', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($query) use ($search) {
                        return $query->where('suppliers.business_name', 'like', "%{$search}%");
                    })->orWhereHas('elaborated', function ($query) use ($search) {
                        return $query->where('users.name', 'like', "%{$search}%");
                    });
            });
        });
    }

    public function scopeFilter($query, array $filters)
    {
        $estatus = $filters['estatus'] ?? null;
        $query->when($filters['folio'] ?? null, function ($query, $folio) {
            $query->where(function ($query) use ($folio) {
                $query->orWhere('id', 'like', $folio);
            });
        })->when($filters['supplier'] ?? null, function ($query, $supplier) {
            $query->whereHas('supplier', function ($query) use ($supplier) {
                return $query->where('id', $supplier);
            });
        })->when($filters['metodo_pago'] ?? null, function ($query, $metodoPago) {
            $query->whereHas('metodopago', function ($query) use ($metodoPago) {
                return $query->where('clave', $metodoPago);
            });
        })->when($filters['uso_cfdi'] ?? null, function ($query, $usoCfdo) {
            $query->whereHas('usocfdi', function ($query) use ($usoCfdo) {
                return $query->where('clave', $usoCfdo);
            });
        })->when($filters['forma_pago'] ?? null, function ($query, $formaPago) {
            $query->whereHas('formapago', function ($query) use ($formaPago) {
                return $query->where('clave', $formaPago);
            });
        });

        $query->when($filters['estatus'] ?? null, function ($query, $estatus) {
            if ($estatus !== "todos") {
                $query->whereHas('estatus', function ($query) use ($estatus) {
                    $query->where('key', Helpers::commasToArray($estatus));
                });
            }
        }, function ($query) {
            $query->whereHas('estatus', function ($query) {
                $query->where('key', Estatus::ESTATUS_PENDIENTE);
            });
        });

        $query->when($filters['agencie'] ?? null, function ($query, $agency_id) {
            $query->whereHas('chargeAgency', function ($query) use ($agency_id) {
                return $query->whereIn('agency_id', $agency_id);
            });
        })->when($filters['department'] ?? null, function ($query, $depto_id) {
            $query->whereHas('chargeDepartment', function ($query) use ($depto_id) {
                return $query->whereIn('department_id', $depto_id);
            });
        });

        $query->when($filters['date_range'] ?? null, function ($query, $dates) use ($estatus) {
            if ($estatus == "todos" || $estatus == "pendiente")
                return $query->whereBetween('created_at', [$dates[0], $dates[1]]);
            if ($estatus == "autorizado" || $estatus == 'verificado')
                return $query->whereBetween('authorization_date', [$dates[0], $dates[1]]);
            if ($estatus == "por_facturar")
                return $query->whereBetween('updated_at', [$dates[0], $dates[1]]);
            if ($estatus == "programar_pago")
                return $query->whereHas('invoice', function ($query) use ($dates) {
                    return $query->whereNull('date_to_pay')
                        ->whereBetween('invoice_date', [$dates[0], $dates[1]]);
                });
            if ($estatus == "por_pagar")
                return $query->whereHas('invoice', function ($query) use ($dates) {
                    return $query->whereNull('payment_date')
                        ->whereBetween('date_to_pay', [$dates[0], $dates[1]]);
                });
            if ($estatus == "pagada")
                return $query->whereHas('invoice', function ($query) use ($dates) {
                    return $query->whereBetween('payment_date', [$dates[0], $dates[1]]);
                });
        });
    }

    public function scopeFilterPermission($query, User $user)
    {
        $query->when(
            (($user->inGroup('Gerente') || $user->inGroup('Compras')) &&
                $user->hasPermission('compras.admin')) ||
                $user->hasPermission('compras.all.list') ||
                $user->isSuperUser(),
            function ($query) {
                return $query;
            },
            function ($query) use ($user) {
                $query->when($user->inGroup('Compras') ?? null, function ($query) use ($user) {
                    $query->where('created_by', $user->id);
                });
            }
        );
    }
}


/**
 * serializes concept attribute on the fly before saving to database
 *
 * @param $concepts
 */
// public function setConceptsAttribute($concepts)
// {
//     $this->attributes['concepts'] = serialize($concepts);
// }

/**
 * unserializes concepts attribute before spitting out from database
 *
 * @return mixed
 */
    // public function getConceptsAttribute()
    // {
    //     if (empty($this->attributes['concepts']) || is_null($this->attributes['concepts'])) {
    //         return [];
    //     }

    //     return unserialize($this->attributes['concepts']);
    // }
