<?php

namespace App\Components\Purchase\Models;

use App\Components\Common\Models\Currency;
use App\Components\Purchase\Pivots\PurchasePivotCharge;
use App\Components\Purchase\Pivots\PurchasePivotProduct;
use App\Components\RRHH\Models\Employee;
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


use App\Components\Common\Models\cClaveProdServ;

class PurchaseOrder extends Model
{
    protected $connection = 'mysql';
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

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function purchaseType()
    {
        return $this->belongsTo(PurchaseType::class, 'purchase_type_id');
    }
    public function purchase_concept()
    {
        return $this->belongsTo(PurchaseConcept::class, 'purchase_concept_id');
    }

    public function pivotCharge()
    {
        return $this->hasMany(PurchasePivotCharge::class, 'purchase_order_id');
    }

    public function agencies()
    {
        return $this->belongsToMany(Agency::class, 'purchase_pivot_charges')
            ->using(PurchasePivotCharge::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'purchase_pivot_charges')
            ->using(PurchasePivotCharge::class);
    }
    public function pivotProduct()
    {
        return $this->hasMany(
            PurchasePivotProduct::class,
            'purchase_order_id',
        );
    }

    public function files()
    {
        return $this->morphMany('App\Components\File\Models\File', 'fileable');
    }

    public function getChargesAttribute()
    {
        return $this->pivotCharge->map(function ($charge) {
            return [
                'agency' => $charge->agency->only('id', 'title', 'code'),
                'department' => $charge->department->only('id', 'title', 'code'),
                'percent' => $charge->percent
            ];
        });
    }

    public function getProductsAttribute()
    {
        return $this->pivotProduct->map(function ($product) {
            return [
                "id" => $product->id,
                "claveProduct" => $product->claveProdServ->only('c_ClaveProdServ', 'Descripción', 'Palabrassimilares'),
                'unit' => $product->claveUnit->only(['id', 'clave', 'name']),
                'unit_d' => $product->unit_id,
                'description' => $product->description,
                'qty' => $product->qty,
                'price' => $product->price,
                'discount' => $product->discount,
                'subtotal' => $product->subtotal,

            ];
        });
    }
    public function scopeSearch($query, string $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->orWhere(function ($query) use ($search) {
                $query->orWhere('id', 'like', $search)
                    ->orWhereHas('supplier', function ($query) use ($search) {
                        return $query->where('suppliers.business_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('elaborated', function ($query) use ($search) {
                        return $query->where('users.name', 'like', "%{$search}%")
                            ->orWhereHasMorph('profiable', [Employee::class], function ($query) use ($search) {
                                $query->where('employees.name', 'like', "%{$search}%");
                            });
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
            $query->whereHas('agencies', function ($query) use ($agency_id) {
                return $query->whereIn('agency_id', $agency_id);
            });
        })->when($filters['department'] ?? null, function ($query, $depto_id) {
            $query->whereHas('departments', function ($query) use ($depto_id) {
                return $query->whereIn('department_id', $depto_id);
            });
        })->when($filters['purchase_type'] ?? null, function ($query, $purchase_type) {
            $query->whereHas('purchaseType', function ($query) use ($purchase_type) {
                return $query->where('id', $purchase_type);
            });
        })->when($filters['purchase_concept'] ?? null, function ($query, $purchase_concept) {
            $query->whereHas('purchase_concept', function ($query) use ($purchase_concept) {
                return $query->whereIn('id', $purchase_concept);
            });
        })->when($filters['ship'] ?? null, function ($query, $ship) {
            $query->whereHas('ship', function ($query) use ($ship) {
                return $query->whereIn('id', $ship);
            });
        });

        $query->when($filters['date_range'] ?? null, function ($query, $dates) use ($estatus) {
            $datesRange = [$dates[0], $dates[1] . ' 23:59:59'];
            if ($estatus == "todos" || $estatus == "pendiente") {
                if ($dates[0] == $dates[1])
                    return $query->whereDate('created_at', $dates);
                return $query->whereBetween('created_at', $datesRange);
            }
            if ($estatus == "autorizado" || $estatus == 'verificado') {
                if ($dates[0] == $dates[1])
                    return $query->whereDate('created_at', $dates);
                return $query->whereBetween('authorization_date', $datesRange);
            }
            if ($estatus == "por_facturar") {
                if ($dates[0] == $dates[1])
                    return $query->whereDate('created_at', $dates);
                return $query->whereBetween('updated_at', $datesRange);
            }
            if ($estatus == "programar_pago") {
                return $query->whereHas('invoice', function ($query) use ($dates, $datesRange) { {
                        if ($dates[0] == $dates[1])
                            return $query->whereDate('invoice_date', $dates);
                        return $query->whereNull('date_to_pay')
                            ->whereBetween('invoice_date', $datesRange);
                    }
                });

            }
            if ($estatus == "por_pagar") {
                if ($dates[0] == $dates[1])
                    return $query->whereDate('date_to_pay', $dates);
                // return $query->whereHas('invoice', function ($query) use ($dates) {
                return $query->whereNull('payment_date')
                    ->whereBetween('date_to_pay', $datesRange);
            }
            if ($estatus == "pagada") {
                return $query->whereHas('invoice', function ($query) use ($dates, $datesRange) {
                    if ($dates[0] == $dates[1])
                        return $query->whereDate('payment_date', $dates);
                    return $query->whereBetween('payment_date', $datesRange);
                });
            }
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