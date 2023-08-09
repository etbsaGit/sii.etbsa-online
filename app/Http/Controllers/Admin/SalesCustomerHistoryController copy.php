<?php

namespace App\Http\Controllers\Admin;

use App\Components\Core\Utilities\Helpers;
use App\Components\EquipDB\Models\CatClientes;
use App\Components\EquipDB\Models\InvoicesAG;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SalesCustomerHistoryController extends AdminController
{

    public function __construct()
    {
    }

    public function index(Request $request)
    {

        // dd($request->all(), $request['clave_cliente'], $request->clave_cliente);
        $filters = $request->all();

        $query = InvoicesAG::query()
            ->select(

                'cc.CIUDAD AS CCCiudad',
                'cc.ESTADO AS CCEstado',
                'invoice_jd.NO_DOCUMENTO',
                'invoice_jd.DESCRIPCION PRODUCTO AS Producto',
                'invoice_jd.NIP',
                'invoice_jd.TOTAL',
                DB::raw("
                COALESCE(
                    CASE
                        WHEN cs.`nombre` <> ''  THEN cs.`nombre`
                        ELSE NULL
                    END
                ,'N/E')
                AS Sucursal"
                ),
                'cs.linea As SucursalLinea',
                'ctv.descripcion as TipoVenta',
                DB::raw("
                COALESCE(
                    CASE
                        WHEN cc.`NOMBRE CLIENTE` <> '' AND cc.`APELLIDOS CLIENTE` <> ''  THEN cc.`NOMBRE CLIENTE` || ' ' || cc.`APELLIDOS CLIENTE`
                        WHEN cc.`COMPA�IA` <> ''  THEN cc.`COMPA�IA`
                        WHEN invoice_jd.`NOMBREPROVEEDOR` <> '' AND invoice_jd.`APELLIDOS PROVEEDOR` <> ''  THEN invoice_jd.`NOMBREPROVEEDOR` || ' ' || invoice_jd.`APELLIDOS PROVEEDOR`
                        WHEN invoice_jd.`NOMBRE CLIENTE` <> '' THEN invoice_jd.`NOMBRE CLIENTE`
                        ELSE NULL
                    END, 
                'N/E')
                AS Cliente"
                ),
                DB::raw("
                COALESCE(
                CASE
                    WHEN cc.`CLAVE CLIENTE` <> '' THEN cc.`CLAVE CLIENTE`
                    WHEN invoice_jd.`CLAVE PROVEEDOR` <> '' THEN invoice_jd.`CLAVE PROVEEDOR`
                    ELSE NULL
                END , 'S/C')
                AS Clave_Cliente"
                ),
                DB::raw("
                CASE
                    WHEN invoice_jd.MONEDA == '' THEN 'MX'
                    ELSE invoice_jd.MONEDA
                END AS currency"
                ),
                DB::raw("
                COALESCE(
                CASE
                    WHEN cv.`Clave vendedor` <> '' THEN cv.`Clave vendedor`
                    ELSE NULL
                END , 'N/E')
                AS VendedorClave"
                ),
                DB::raw("
                COALESCE(
                CASE
                    WHEN cv.`Nombre Vendedor` <> '' THEN cv.`Nombre Vendedor`
                    ELSE NULL
                END , 'N/E')
                AS Vendedor"
                ),
                DB::raw("
                COALESCE(
                CASE
                    WHEN invoice_jd.`CIUDAD`  <> '' THEN invoice_jd.`CIUDAD`
                    WHEN cc.`CIUDAD`  <> '' THEN cc.`CIUDAD`
                    ELSE NULL 
                END , 'N/E')
                AS Municipio"
                ),
                DB::raw("
                COALESCE(
                CASE
                    WHEN invoice_jd.`ESTADO`  <> '' THEN invoice_jd.`ESTADO`
                    WHEN cc.`ESTADO`  <> '' THEN cc.`ESTADO`
                    ELSE NULL 
                END , 'N/E')
                AS Estado"
                ),
                DB::raw("
                    CASE 
                    WHEN invoice_jd.`TIPO/CAMBIO` > 0 AND invoice_jd.`TIPO/CAMBIO` <> '' 
                    THEN invoice_jd.`TIPO/CAMBIO`
                    ELSE 1
                    END AS TipoCambio"
                ),
                DB::raw('SUBSTR(`FECHA FACTURA`, -4) as year'),
            )
            // ->leftJoin('catClientes AS cc', function ($lefJoin) {
            //     $lefJoin->on('cc.CLAVE CLIENTE', '=', 'invoice_jd.CLAVE PROVEEDOR')
            //         ->orWhere('cc.RFC', '=', 'invoice_jd.RFC COMPAÑIA')
            //         ->orWhere('cc.RFC', '=', 'invoice_jd.RFC ABN');
            // })
            ->leftJoin('catClientes AS cc', 'cc.CLAVE CLIENTE', '=', 'invoice_jd.CLAVE PROVEEDOR')
            ->leftJoin('catSucursales AS cs', 'cs.id', '=', 'invoice_jd.SUCURSAL')
            ->leftJoin('catVendedores AS cv', 'cv.Clave vendedor', '=', 'invoice_jd.VENDEDOR')
            ->leftJoin('catTipoVenta AS ctv', 'ctv.clave', '=', 'invoice_jd.TIPO DE VENTA')
            ->orderBy('year');

        // $result = InvoicesAG::query()
        //     ->selectRaw("
        //         invoice_jd.`NO_DOCUMENTO`,
        //         invoice_jd.`DESCRIPCION PRODUCTO`,
        //         invoice_jd.`NIP`,
        //         invoice_jd.`TOTAL`,
        //         cc.`NOMBRE CLIENTE`,
        //         '{\"nombre\": \"' || 
        //             COALESCE(CASE
        //                     WHEN cc.`NOMBRE CLIENTE` <> '' AND cc.`APELLIDOS CLIENTE` <> ''  THEN cc.`NOMBRE CLIENTE` || ' ' || cc.`APELLIDOS CLIENTE`
        //                     WHEN cc.`COMPA�IA` <> ''  THEN cc.`COMPA�IA`
        //                     WHEN invoice_jd.`NOMBREPROVEEDOR` <> '' AND invoice_jd.`APELLIDOS PROVEEDOR` <> ''  THEN invoice_jd.`NOMBREPROVEEDOR` || ' ' || invoice_jd.`APELLIDOS PROVEEDOR`
        //                     WHEN invoice_jd.`NOMBRE CLIENTE` <> '' THEN invoice_jd.`NOMBRE CLIENTE`
        //                     ELSE NULL
        //                 END,\"N/E\") || '\", \"ciudad\": \"' || cc.CIUDAD || '\"}'
        //         AS Cliente
        //     ")
        //     ->leftJoin('catClientes AS cc', 'cc.CLAVE CLIENTE', '=', 'invoice_jd.CLAVE PROVEEDOR')
        //     ->leftJoin('catSucursales AS cs', 'cs.id', '=', 'invoice_jd.SUCURSAL')
        //     ->leftJoin('catVendedores AS cv', 'cv.Clave vendedor', '=', 'invoice_jd.VENDEDOR')
        //     ->leftJoin('catTipoVenta AS ctv', 'ctv.clave', '=', 'invoice_jd.TIPO DE VENTA')
        //     ->orderBy('year')
        //     ->limit(100)->get();

        // dd($result->toArray());

        $query->when($filters['clave_cliente'] ?? null, function ($query, $clave_cliente) {
            $query->where(function ($q) use ($clave_cliente) {
                if (is_array($clave_cliente)) {
                    foreach ($clave_cliente as $term) {
                        $q->orWhere('invoice_jd.CLAVE CLIENTE', 'LIKE', "%{$term}%");
                        $q->orWhere('invoice_jd.CLAVE PROVEEDOR', 'LIKE', "%{$term}%");
                        $q->orWhere(DB::raw("
                        COALESCE(
                            CASE
                                WHEN cc.`NOMBRE CLIENTE` <> '' AND cc.`APELLIDOS CLIENTE` <> ''  THEN cc.`NOMBRE CLIENTE` || ' ' || cc.`APELLIDOS CLIENTE`
                                WHEN cc.`COMPA�IA` <> ''  THEN cc.`COMPA�IA`
                                WHEN invoice_jd.`NOMBREPROVEEDOR` <> '' AND invoice_jd.`APELLIDOS PROVEEDOR` <> ''  THEN invoice_jd.`NOMBREPROVEEDOR` || ' ' || invoice_jd.`APELLIDOS PROVEEDOR`
                                WHEN invoice_jd.`NOMBRE CLIENTE` <> '' THEN invoice_jd.`NOMBRE CLIENTE`
                                ELSE NULL
                            END, 'N/E')"
                        ), 'like', "%{$term}%");
                        $q->orWhere(DB::raw("
                        COALESCE(
                            CASE
                                WHEN cc.`CLAVE CLIENTE` <> '' THEN cc.`CLAVE CLIENTE`
                                WHEN invoice_jd.`CLAVE PROVEEDOR` <> '' THEN invoice_jd.`CLAVE PROVEEDOR`
                                ELSE NULL
                            END , 'S/C')"
                        ), 'like', "%{$term}%");
                    }
                } else {
                    $q->orWhere('invoice_jd.CLAVE CLIENTE', 'LIKE', "%{$clave_cliente}%");
                    $q->orWhere('invoice_jd.CLAVE PROVEEDOR', 'LIKE', "%{$clave_cliente}%");
                    $q->orWhere(DB::raw("
                    COALESCE(
                        CASE
                            WHEN cc.`NOMBRE CLIENTE` <> '' AND cc.`APELLIDOS CLIENTE` <> ''  THEN cc.`NOMBRE CLIENTE` || ' ' || cc.`APELLIDOS CLIENTE`
                            WHEN cc.`COMPA�IA` <> ''  THEN cc.`COMPA�IA`
                            WHEN invoice_jd.`NOMBREPROVEEDOR` <> '' AND invoice_jd.`APELLIDOS PROVEEDOR` <> ''  THEN invoice_jd.`NOMBREPROVEEDOR` || ' ' || invoice_jd.`APELLIDOS PROVEEDOR`
                            WHEN invoice_jd.`NOMBRE CLIENTE` <> '' THEN invoice_jd.`NOMBRE CLIENTE`
                            ELSE NULL
                        END, 'N/E')"
                    ), 'like', "%{$clave_cliente}%");
                    $q->orWhere(DB::raw("
                        COALESCE(
                            CASE
                                WHEN cc.`CLAVE CLIENTE` <> '' THEN cc.`CLAVE CLIENTE`
                                WHEN invoice_jd.`CLAVE PROVEEDOR` <> '' THEN invoice_jd.`CLAVE PROVEEDOR`
                                ELSE NULL
                            END , 'S/C')"
                        ), 'like', "%{$clave_cliente}%");
                }
            });

        })->when($filters['clave_vendedor'] ?? null, function ($query, $clave_vendedor) {
            // $query->Where('invoice_jd.VENDEDOR', 'LIKE', "%{$clave_vendedor}%");

            $query->where(function ($q) use ($clave_vendedor) {
                if (is_array($clave_vendedor)) {
                    foreach ($clave_vendedor as $term) {
                        $q->orWhere('invoice_jd.Clave vendedor', 'LIKE', "%{$term}%");
                        $q->orWhere(DB::raw("
                        COALESCE(
                        CASE
                            WHEN cv.`Nombre Vendedor` <> '' THEN cv.`Nombre Vendedor`
                            ELSE NULL
                        END , 'N/E')"
                        ), 'LIKE', "%{$term}%");
                    }
                } else {
                    $q->orWhere('invoice_jd.Clave vendedor', 'LIKE', "%{$clave_vendedor}%");
                    $q->orWhere(DB::raw("
                        COALESCE(
                        CASE
                            WHEN cv.`Nombre Vendedor` <> '' THEN cv.`Nombre Vendedor`
                            ELSE NULL
                        END , 'N/E')"
                    ), 'LIKE', "%{$clave_vendedor}%");
                }
            });
        })->when($filters['sucursales'] ?? null, function ($query, $sucursales) {
            $query->whereIn('invoice_jd.SUCURSAL', $sucursales);
        })->when($filters['municipio'] ?? null, function ($query, $municipio) {
            $query->where(function ($q) use ($municipio) {
                if (is_array($municipio)) {
                    foreach ($municipio as $term) {
                        $q->orWhere(DB::raw("
                        COALESCE(
                        CASE
                            WHEN invoice_jd.`CIUDAD`  <> '' THEN invoice_jd.`CIUDAD`
                            ELSE NULL 
                        END , 'N/E')"
                        ), 'LIKE', "%{$term}%");
                    }
                } else {
                    $q->orWhere(DB::raw("
                        COALESCE(
                        CASE
                            WHEN invoice_jd.`CIUDAD`  <> '' THEN invoice_jd.`CIUDAD`
                            ELSE NULL 
                        END , 'N/E')"
                    ), 'LIKE', "%{$municipio}%");
                }
            });
        })->when($filters['estado'] ?? null, function ($query, $estado) {
            $query->where(function ($q) use ($estado) {
                if (is_array($estado)) {
                    foreach ($estado as $term) {
                        $q->orWhere(DB::raw("
                        COALESCE(
                        CASE
                            WHEN invoice_jd.`ESTADO`  <> '' THEN invoice_jd.`ESTADO`
                            ELSE NULL 
                        END , 'N/E')"
                        ), 'LIKE', "%{$term}%");
                    }
                } else {
                    $q->orWhere(DB::raw("
                    COALESCE(
                    CASE
                        WHEN invoice_jd.`ESTADO`  <> '' THEN invoice_jd.`ESTADO`
                        ELSE NULL 
                    END , 'N/E')"
                    ), 'LIKE', "%{$estado}%");
                }
            });
        })->when($filters['lineas'] ?? null, function ($query, $lineas) {
            $query->whereIn(
                'invoice_jd.SUCURSAL',
                DB::connection('sqlite_equip_db')->table('catSucursales as cs')
                    ->select('id')->whereIn('linea', $lineas)
            );
        })->when($filters['years'] ?? null, function ($query, $years) {
            $query->whereIn(DB::raw('SUBSTR(`FECHA FACTURA`, -4)'), $years);
        })->when($filters['tipo_venta'] ?? null, function ($query, $tipo_venta) {
            $query->whereIn('TIPO DE VENTA', $tipo_venta);
        })->when($filters['currency'] ?? null, function ($query, $currency) {
            $query->whereIn(DB::raw("
            CASE 
                WHEN invoice_jd.MONEDA == '' THEN 'MX'
                ELSE invoice_jd.MONEDA
            END"
            ), $currency);
        });
        $query->when($request->search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('invoice_jd.DESCRIPCION PRODUCTO', 'like', "%{$search}%")
                    ->orWhere('invoice_jd.NIP', 'like', "%{$search}%")
                    ->orWhere('invoice_jd.NO ECO', 'like', "%{$search}%")
                    ->orWhere('invoice_jd.MODELO', 'like', "%{$search}%")
                    ->orWhere('invoice_jd.NO_DOCUMENTO', 'like', "%{$search}%")
                    ->orWhere('invoice_jd.RFC ABN', 'like', "%{$search}%")
                    ->orWhere('invoice_jd.RFC COMPAÑIA', 'like', "%{$search}%");
                // ->orWhere(DB::raw("
                // COALESCE(
                //     CASE 
                //         WHEN cc.`NOMBRE CLIENTE` <> '' AND cc.`APELLIDOS CLIENTE` <> '' 
                //         THEN cc.`NOMBRE CLIENTE` || ' ' || cc.`APELLIDOS CLIENTE`
                //         ELSE cc.`COMPA�IA`
                //     END, 
                // 'N/E')
                // "
                // ), 'like', "%{$search}%");
            });
        });
        // $query->when($request->searchCustomer ?? null, function ($query, $searchCustomer) {
        //     $query->where(function ($query) use ($searchCustomer) {
        //         $query->orWhere(DB::raw("
        //             COALESCE(
        //                 CASE 
        //                     WHEN cc.`NOMBRE CLIENTE` <> '' AND cc.`APELLIDOS CLIENTE` <> '' 
        //                     THEN cc.`NOMBRE CLIENTE` || ' ' || cc.`APELLIDOS CLIENTE`
        //                     ELSE cc.`COMPA�IA`
        //                 END, 
        //             'N/E')
        //             "
        //         ), 'like', "%{$searchCustomer}%");
        //     });
        // });


        if ($request['per_page'] == -1) {
            $request['per_page'] = 999999;
        }

        $sumatoriaTotal = $query->sum('TOTAL');
        $query->chunk(2000, function ($invoices) {
            return $invoices;
        });
        $items = $query->paginate($request['per_page'] ?? 10);

        $archivo = database_path('EquipDB.db3');
        $fechaModificacion = File::lastModified($archivo);
        $lastUpdated = date("Y-m-d H:i:s", $fechaModificacion);

        return $this->sendResponseOk(compact('lastUpdated', 'items', 'sumatoriaTotal'));
    }

    public function getOptions()
    {
        $options = [
            // 'Clientes' => DB::connection('sqlite_equip_db')->table('catClientes AS cc')->distinct()
            //     ->select(
            //         'cc.CLAVE CLIENTE AS Clave_Cliente',
            //         DB::raw("CASE 
            //         WHEN cc.`NOMBRE CLIENTE` <> '' AND cc.`APELLIDOS CLIENTE` <> '' THEN cc.`NOMBRE CLIENTE` || ' ' || cc.`APELLIDOS CLIENTE` 
            //         ELSE cc.`COMPA�IA` END AS Cliente")
            //     )->get(),
            'Clientes' => DB::connection('sqlite_equip_db')->table('invoice_jd')->distinct()
                ->select(
                    DB::raw("
                    COALESCE(
                    CASE 
                        WHEN cc.`CLAVE CLIENTE` <> '' THEN cc.`CLAVE CLIENTE`
                        WHEN invoice_jd.`CLAVE PROVEEDOR` <> '' THEN invoice_jd.`CLAVE PROVEEDOR`
                        ELSE NULL 
                    END,'S/C') AS Clave_Cliente"),
                    DB::raw("
                    COALESCE(
                    CASE 
                        WHEN cc.`NOMBRE CLIENTE` <> '' AND cc.`APELLIDOS CLIENTE` <> ''  THEN cc.`NOMBRE CLIENTE` || ' ' || cc.`APELLIDOS CLIENTE`
                        WHEN cc.`COMPA�IA` <> ''  THEN cc.`COMPA�IA`
                        WHEN invoice_jd.`NOMBREPROVEEDOR` <> '' AND invoice_jd.`APELLIDOS PROVEEDOR` <> ''  THEN invoice_jd.`NOMBREPROVEEDOR` || ' ' || invoice_jd.`APELLIDOS PROVEEDOR`
                        WHEN invoice_jd.`NOMBRE CLIENTE` <> '' THEN invoice_jd.`NOMBRE CLIENTE`
                        ELSE NULL
                    END,'S/N') AS Cliente")
                )
                ->leftJoin('catClientes AS cc', 'cc.CLAVE CLIENTE', '=', 'invoice_jd.CLAVE PROVEEDOR')
                ->get(),
            'Vendedores' => DB::connection('sqlite_equip_db')->table('catVendedores as cv')->distinct()
                ->select('cv.Clave vendedor AS ClaveVendedor', 'cv.Nombre Vendedor AS Nombre')
                ->get(),
            'Sucursales' => DB::connection('sqlite_equip_db')->table('catSucursales as cs')->distinct()
                ->get(),
            'Linea' => DB::connection('sqlite_equip_db')->table('catSucursales as cs')->distinct()
                ->select('cs.linea')
                ->get(),
            'SerieFiscal' => DB::connection('sqlite_equip_db')->table('invoice_jd')->distinct()
                ->select('SERIE FISCAL AS serie_fiscal')->whereNotNull('SERIE FISCAL')
                ->pluck('serie_fiscal'),
            'Moneda' => DB::connection('sqlite_equip_db')->table('invoice_jd')->distinct()
                ->select('MONEDA')->whereNotNull('MONEDA')
                ->get(),
            'TipoVenta' => DB::connection('sqlite_equip_db')->table('invoice_jd')->distinct()
                ->select('ctv.clave', 'ctv.descripcion')
                ->join('catTipoVenta as ctv', 'invoice_jd.TIPO DE VENTA', '=', 'ctv.clave')
                ->whereNotNull('invoice_jd.TIPO DE VENTA')
                ->get(),
            'Años' => DB::connection('sqlite_equip_db')->table('invoice_jd')->distinct()
                // ->select(DB::raw('strftime("%Y", `FECHA FACTURA`) as year'))
                ->select(DB::raw('SUBSTR(`FECHA FACTURA`, -4) as year'))
                ->pluck('year'),
            'Municipio' => DB::connection('sqlite_equip_db')->table('invoice_jd')->distinct()
                ->select('CIUDAD as name')
                ->pluck('name'),
            'Estado' => DB::connection('sqlite_equip_db')->table('invoice_jd')->distinct()
                ->select('ESTADO as name')
                ->pluck('name'),
        ];
        return $this->sendResponseOk(compact('options'));
    }
}