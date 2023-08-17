<?php

namespace App\Http\Controllers\Admin;

use App\Components\Core\Utilities\Helpers;
use App\Components\EquipDB\Models\CatClientes;
use App\Components\EquipDB\Models\FacturacionAG;
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

        $query = FacturacionAG::from('facturacion_utf8 AS fa')->select(
            'CVE_CLIENTE as clave_cliente',
            'fa.SUCURSAL AS sucursal',
            'fa.LINEA AS linea',
            'NIP',
            'DESCRIPCION PRODUCTO AS producto',
            'NOMBRE CLIENTE AS cliente',
            'PRECIO VENTA AS precio_venta',
            'FECHA FACTURA AS invoice_date',
            'ctv.clave AS tipo_venta',
            'ctv.descripcion AS tipo_venta_nombre',
            // 'cv.clave_vendodor AS clave_vendedor',
            DB::raw("
            COALESCE(
            CASE
                WHEN cv.clave_vendodor <> '' THEN cv.clave_vendodor
                ELSE fa.CVE_VENDEDOR
            END , 'N/E')
            AS clave_vendedor"
            ),
            DB::raw("
            COALESCE(
            CASE
                WHEN cv.nombre_vendor <> '' THEN cv.nombre_vendor
                ELSE fa.`NOMBRE VENDEDOR`
            END , 'N/E')
            AS nombre_vendedor"
            ),
            // 'cv.nombre_vendor AS nombre_vendedor',
            DB::raw("fa.CALLE ||','|| fa.`COLONIA /COMUNIDAD`||','|| fa.CP ||','||fa.`CIUDAD / MUNICIPIO`||','|| fa.ESTADO ||','|| fa.PAIS AS addresses"),
            DB::raw("fa.`Telefono 1` ||','|| fa.`Telefono 2`||','|| fa.`Telefono 3` AS phones"),
            DB::raw('SUBSTR(`FECHA FACTURA`, -4) as year')
        )
            ->leftJoin('vendedores_utf8 AS cv', 'cv.clave_vendodor', '=', 'fa.CVE_VENDEDOR')
            ->leftJoin('catTipoVenta AS ctv', 'ctv.clave', '=', 'fa.TIPO DE VENTA')
            ->orderByRaw("SUBSTR(`FECHA FACTURA`, -4)");
        // ->orderBy('invoice_date');
        // ->orderByRaw("strftime('%Y', fa.`FECHA FACTURA`)");

        $query->when($filters['clave_cliente'] ?? null, function ($query, $clave_cliente) {
            $query->where(function ($q) use ($clave_cliente) {
                if (is_array($clave_cliente)) {
                    foreach ($clave_cliente as $term) {
                        $q->orWhere('fa.CVE_CLIENTE', 'LIKE', "%{$term}%");
                        $q->orWhere('fa.NOMBRE CLIENTE', 'LIKE', "%{$term}%");
                    }
                }
            });
        })->when($filters['clave_vendedor'] ?? null, function ($query, $clave_vendedor) {
            $query->where(function ($q) use ($clave_vendedor) {
                if (is_array($clave_vendedor)) {
                    foreach ($clave_vendedor as $term) {
                        $q->orWhere('cv.clave_vendodor', 'LIKE', "%{$term}%");
                        $q->orWhere('cv.nombre_vendor', 'LIKE', "%{$term}%");
                        
                    }
                }
            });
        })->when($filters['sucursales'] ?? null, function ($query, $sucursales) {
            $query->whereIn('fa.SUCURSAL', $sucursales);
        });

        $query->when($request->search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('fa.DESCRIPCION PRODUCTO', 'like', "%{$search}%")
                    ->orWhere('fa.NIP', 'like', "%{$search}%")
                    ->orWhere('cv.MODELO', 'LIKE', "%{$search}%");
            });
        });

        if ($request['per_page'] == -1) {
            $request['per_page'] = 999999;
        }

        $sumatoriaTotal = $query->sum('PRECIO VENTA');
        $query->chunk(2000, function ($invoices) {
            return $invoices;
        });
        $results = $query->get();
        $items = $query->paginate($request['per_page'] ?? 10);

        $ventasPorCliente = [];
        $barGrafica = [];
        $ultimasInvoicePorCliente = [];
        if ($request->has('clave_cliente')) {

            // Grafica de Pie y Tabla 
            $ventasPorCliente = $results->groupBy(function ($item) {
                return $item->tipo_venta . '_' . $item->clave_cliente;
                // return $item->tipo_venta . '_' . $item->cliente;
                // return $item->tipo_venta;
            })->map(function ($groupedItems) {
                return [
                    'clave_cliente' => $groupedItems[0]->clave_cliente,
                    'cliente' => $groupedItems[0]->cliente,
                    'tipo' => $groupedItems[0]->tipo_venta,
                    'tipo_venta' => $groupedItems[0]->tipo_venta_nombre,
                    'total_comprado' => $groupedItems->sum('precio_venta')
                ];
            })->values();

            // Grafica de Barras
            $allYears = $results->pluck('year')->unique()->sort()->values();
            $barGrafica = $results->groupBy('clave_cliente')->map(function ($ventasPorCliente, $cliente) use ($allYears) {
                $acumuladoPorYear = $ventasPorCliente->groupBy('year')->map(function ($ventasPorYear) {
                    return $ventasPorYear->sum('precio_venta');
                });

                // Rellenar con 0 los años sin ventas
                $acumuladoPorYear = $acumuladoPorYear->union($allYears->flip()->map(function () {
                    return 0;
                }))->sortKeys()->values();


                return [
                    'cliente' => $cliente,
                    'years' => $allYears,
                    'acumulado' => $acumuladoPorYear,
                ];
            })->values();

            // Tabla Ultima Venta
            $ultimasInvoicePorCliente = $results->mapToGroups(function ($item) {
                return [$item->clave_cliente => $item];
            })->map(function ($invoiceDates) {
                return $invoiceDates->last();
            });
        }

        $ventasPorVendedor = [];
        $barVendedorGrafica = [];
        $ultimasInvoiceVendedor = [];
        if ($request->has('clave_vendedor')) {
            $ventasPorVendedor = $results->groupBy(function ($item) {
                return $item->tipo_venta . '_' . $item->clave_vendedor;
                // return $item->clave_vendedor;
                // return $item->tipo_venta . '_' . $item->cliente;
                // return $item->tipo_venta;
            })->map(function ($groupedItems) {
                return [
                    'clave_vendedor' => $groupedItems[0]->clave_vendedor,
                    'vendedor' => $groupedItems[0]->nombre_vendedor,
                    'tipo' => $groupedItems[0]->tipo_venta,
                    'tipo_venta' => $groupedItems[0]->tipo_venta_nombre,
                    'total_comprado' => $groupedItems->sum('precio_venta')
                ];
            })->values();

            $allYears = $results->pluck('year')->unique()->sort()->values();
            $barVendedorGrafica = $results->groupBy('clave_vendedor')->map(function ($ventasPorCliente, $cliente) use ($allYears) {
                $acumuladoPorYear = $ventasPorCliente->groupBy('year')->map(function ($ventasPorYear) {
                    return $ventasPorYear->sum('precio_venta');
                });

                // Rellenar con 0 los años sin ventas
                $acumuladoPorYear = $acumuladoPorYear->union($allYears->flip()->map(function () {
                    return 0;
                }))->sortKeys()->values();


                return [
                    'cliente' => $cliente,
                    'years' => $allYears,
                    'acumulado' => $acumuladoPorYear,
                ];
            })->values();

            $ultimasInvoiceVendedor = $results->mapToGroups(function ($item) {
                return [$item->clave_vendedor => $item];
            })->map(function ($invoiceDates) {
                return $invoiceDates->last();
            });
        }


        $archivo = database_path('EquipDB.db3');
        $fechaModificacion = File::lastModified($archivo);
        $lastUpdated = date("Y-m-d H:i:s", $fechaModificacion);

        return $this->sendResponseOk(
            compact(
                'lastUpdated',
                'items',
                'sumatoriaTotal',
                'ventasPorCliente',
                'barGrafica',
                'ultimasInvoicePorCliente',
                'ventasPorVendedor',
                'barVendedorGrafica',
                'ultimasInvoiceVendedor',
            )
        );
    }

    public function getOptions()
    {
        $options = [
            'Clientes' => FacturacionAG::distinct()
                ->select(
                    'CVE_CLIENTE AS Clave_Cliente',
                    'NOMBRE CLIENTE AS Cliente'
                )
                // ->leftJoin('catClientes AS cc', 'cc.CLAVE CLIENTE', '=', 'invoice_jd.CLAVE PROVEEDOR')
                ->get(),
            'Vendedores' => FacturacionAG::distinct()
                ->select(
                    'cv.clave_vendodor AS ClaveVendedor',
                    'cv.nombre_vendor AS Nombre'
                )
                ->leftJoin('vendedores_utf8 AS cv', 'cv.clave_vendodor', '=', 'facturacion_utf8.CVE_VENDEDOR')
                ->whereNotNull('cv.clave_vendodor')
                ->get(),
            // 'Vendedores' => DB::connection('sqlite_equip_db')->table('catVendedores as cv')->distinct()
            //     ->select('cv.Clave vendedor AS ClaveVendedor', 'cv.Nombre Vendedor AS Nombre')
            //     ->get(),
            'Sucursales' => DB::connection('sqlite_equip_db')->table('facturacion_utf8')->distinct()
                ->select('SUCURSAL as name')
                ->pluck('name'),
            // DB::connection('sqlite_equip_db')->table('catSucursales as cs')->distinct()
            //     ->get(),
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