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
                DB::raw("
                COALESCE(cs.`nombre`, 'N/E')
                AS Sucursal"
                ),
                'cs.linea As SucursalLinea',
                'ctv.descripcion as TipoVenta',
                DB::raw("
                COALESCE(
                    CASE 
                        WHEN cc.`NOMBRE CLIENTE` <> '' AND cc.`APELLIDOS CLIENTE` <> '' 
                        THEN cc.`NOMBRE CLIENTE` || ' ' || cc.`APELLIDOS CLIENTE`
                        ELSE cc.`COMPA�IA`
                    END, 
                'N/E')
                AS Cliente"
                ),
                'cv.Nombre Vendedor AS Vendedor',
                'cv.Clave vendedor AS VendedorClave',
                'invoice_jd.NO_DOCUMENTO',
                'invoice_jd.DESCRIPCION PRODUCTO AS Producto',
                'invoice_jd.NIP',
                'invoice_jd.TOTAL',
                DB::raw("
                CASE 
                    WHEN invoice_jd.MONEDA == '' THEN 'MX'
                    ELSE invoice_jd.MONEDA
                END AS currency"
                ),
                DB::raw("
                COALESCE(
                CASE 
                    WHEN cc.`CLAVE CLIENTE` == '' THEN 'N/E'
                    ELSE cc.`CLAVE CLIENTE`
                END , 'N/E')
                AS Clave_Cliente"
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
            ->leftJoin('catClientes AS cc', 'cc.CLAVE CLIENTE', '=', 'invoice_jd.CLAVE PROVEEDOR')
            ->leftJoin('catSucursales AS cs', 'cs.id', '=', 'invoice_jd.SUCURSAL')
            ->leftJoin('catVendedores AS cv', 'cv.Clave vendedor', '=', 'invoice_jd.VENDEDOR')
            ->leftJoin('catTipoVenta AS ctv', 'ctv.clave', '=', 'invoice_jd.TIPO DE VENTA')
            ->orderBy('cs.nombre');



        // $query = InvoicesAG::query()
        //     ->select(
        //         'cs.nombre As Sucursal',
        //         'cs.linea As SucursalLinea',
        //         'cc.CLAVE CLIENTE AS Clave_Cliente',
        //         'ctv.descripcion as TipoVenta',
        //         DB::raw("
        //         CASE 
        //             WHEN cc.`NOMBRE CLIENTE` <> '' AND cc.`APELLIDOS CLIENTE` <> '' 
        //             THEN cc.`NOMBRE CLIENTE` || ' ' || cc.`APELLIDOS CLIENTE`
        //             ELSE cc.`COMPA�IA`
        //         END AS Cliente"
        //         ),
        //         'cv.Nombre Vendedor AS Vendedor',
        //         'cv.Clave vendedor AS VendedorClave',
        //         'invoice_jd.NO_DOCUMENTO',
        //         'invoice_jd.DESCRIPCION PRODUCTO AS Producto',
        //         'invoice_jd.NIP',
        //         'invoice_jd.TOTAL',
        //         DB::raw("
        //         CASE 
        //             WHEN invoice_jd.MONEDA == '' THEN 'MX'
        //             ELSE invoice_jd.MONEDA
        //         END AS currency"
        //         ),
        //         DB::raw("
        //             CASE 
        //             WHEN invoice_jd.`TIPO/CAMBIO` > 0 AND invoice_jd.`TIPO/CAMBIO` <> '' 
        //             THEN invoice_jd.TOTAL * invoice_jd.`TIPO/CAMBIO`
        //             ELSE invoice_jd.TOTAL
        //             END AS TotalMX"
        //         ),
        //         DB::raw("
        //             CASE 
        //             WHEN invoice_jd.`TIPO/CAMBIO` > 0 AND invoice_jd.`TIPO/CAMBIO` <> '' 
        //             THEN invoice_jd.`TIPO/CAMBIO`
        //             ELSE 1
        //             END AS TipoCambio"
        //         ),
        //         DB::raw('SUBSTR(`FECHA FACTURA`, -4) as year'),
        //     )
        //     ->join('catClientes as cc', 'invoice_jd.CLAVE PROVEEDOR', '=', 'cc.CLAVE CLIENTE')
        //     ->join('catSucursales as cs', 'invoice_jd.SUCURSAL', '=', 'cs.id')
        //     ->join('catVendedores as cv', 'invoice_jd.VENDEDOR', '=', 'cv.Clave vendedor')
        //     ->join('catTipoVenta as ctv', 'invoice_jd.TIPO DE VENTA', '=', 'ctv.clave')
        //     ->orderBy('cs.nombre');


        $query->when($filters['clave_cliente'] ?? null, function ($query, $clave_cliente) {
            // $query->where('invoice_jd.CLAVE CLIENTE', 'LIKE', "%{$clave_cliente}%");

            $query->where(function ($q) use ($clave_cliente) {
                if (is_array($clave_cliente)) {
                    foreach ($clave_cliente as $term) {
                        $q->orWhere('invoice_jd.CLAVE CLIENTE', 'LIKE', "%{$term}%");
                        $q->orWhere('invoice_jd.CLAVE PROVEEDOR', 'LIKE', "%{$term}%");
                    }
                } else {
                    $q->orWhere('invoice_jd.CLAVE CLIENTE', 'LIKE', "%{$clave_cliente}%");
                    $q->orWhere('invoice_jd.CLAVE PROVEEDOR', 'LIKE', "%{$clave_cliente}%");
                }
            });

        })->when($filters['clave_vendedor'] ?? null, function ($query, $clave_vendedor) {
            // $query->Where('invoice_jd.VENDEDOR', 'LIKE', "%{$clave_vendedor}%");

            $query->where(function ($q) use ($clave_vendedor) {
                if (is_array($clave_vendedor)) {
                    foreach ($clave_vendedor as $term) {
                        $q->orWhere('invoice_jd.Clave vendedor', 'LIKE', "%{$term}%");
                    }
                } else {
                    $q->orWhere('invoice_jd.Clave vendedor', 'LIKE', "%{$clave_vendedor}%");
                }
            });
        })->when($filters['sucursales'] ?? null, function ($query, $sucursales) {
            $query->whereIn('invoice_jd.SUCURSAL', $sucursales);
        })->when($filters['municipio'] ?? null, function ($query, $municipio) {
            $query->where(function ($q) use ($municipio) {
                if (is_array($municipio)) {
                    foreach ($municipio as $term) {
                        $q->orWhere('invoice_jd.CIUDAD', 'LIKE', "%{$term}%");
                    }
                } else {
                    $q->orWhere('invoice_jd.CIUDAD', 'LIKE', "%{$municipio}%");
                }
            });
        })->when($filters['estado'] ?? null, function ($query, $estado) {
            $query->where(function ($q) use ($estado) {
                if (is_array($estado)) {
                    foreach ($estado as $term) {
                        $q->orWhere('invoice_jd.ESTADO', 'LIKE', "%{$term}%");
                    }
                } else {
                    $q->orWhere('invoice_jd.ESTADO', 'LIKE', "%{$estado}%");
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
        $query->when($request->searchCustomer ?? null, function ($query, $searchCustomer) {
            $query->where(function ($query) use ($searchCustomer) {
                $query->orWhere(DB::raw("
                    COALESCE(
                        CASE 
                            WHEN cc.`NOMBRE CLIENTE` <> '' AND cc.`APELLIDOS CLIENTE` <> '' 
                            THEN cc.`NOMBRE CLIENTE` || ' ' || cc.`APELLIDOS CLIENTE`
                            ELSE cc.`COMPA�IA`
                        END, 
                    'N/E')
                    "
                    ), 'like', "%{$searchCustomer}%");
            });
        });


        if ($request['per_page'] == -1) {
            $request['per_page'] = 999999999999;
        }


        $sumatoriaTotal = $query->sum('TOTAL');
        // $sumatoriaTotalMX = $query->get()->sum('TotalMX');
        $items = $query->paginate($request['per_page'] ?? 15);
        // $sumatoriaTotal = $items->sum('TOTAL');
        // $sumatoriaTotalMX = $items->sum('TotalMX');

        $archivo = database_path('EquipDB.db3');
        $fechaModificacion = File::lastModified($archivo);
        $lastUpdated = date("Y-m-d H:i:s", $fechaModificacion);

        return $this->sendResponseOk(compact('lastUpdated', 'items', 'sumatoriaTotal'));
    }

    public function getOptions()
    {
        $options = [
            'Clientes' => DB::connection('sqlite_equip_db')->table('catClientes AS cc')->distinct()
                ->select(
                    'cc.CLAVE CLIENTE AS Clave_Cliente',
                    DB::raw("CASE 
                    WHEN cc.`NOMBRE CLIENTE` <> '' AND cc.`APELLIDOS CLIENTE` <> '' THEN cc.`NOMBRE CLIENTE` || ' ' || cc.`APELLIDOS CLIENTE` 
                    ELSE cc.`COMPA�IA` END AS Cliente")
                )->get(),
            'Vendedores' => DB::connection('sqlite_equip_db')->table('catVendedores as cv')->distinct()
                ->select('cv.Clave vendedor AS ClaveVendedor', 'cv.Nombre Vendedor AS Nombre')
                ->get(),
            'Sucursales' => DB::connection('sqlite_equip_db')->table('catSucursales as cs')->distinct()
                ->get(),
            'Linea' => DB::connection('sqlite_equip_db')->table('catSucursales as cs')->distinct()
                ->select('cs.linea')
                ->get(),
            'SerieFiscal' => DB::connection('sqlite_equip_db')->table('invoice_jd')->distinct()
                ->select('SERIE FISCAL')->whereNotNull('SERIE FISCAL')
                ->get(),
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