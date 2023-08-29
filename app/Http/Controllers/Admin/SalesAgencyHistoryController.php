<?php

namespace App\Http\Controllers\Admin;

use App\Components\EquipDB\Models\FacturacionAG;
use App\Components\EquipDB\Models\VentasSucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SalesAgencyHistoryController extends AdminController
{
    public function __construct()
    {
    }
    public function index(Request $request)
    {

        // dd($request->all(), $request['clave_cliente'], $request->clave_cliente);
        $filters = $request->all();

        // $query = VentasSucursal::from('ventas_suc_depto AS fa')->select('*')->orderBy('SUCURSAL');

        $query = VentasSucursal::from('ventas_suc_depto AS V')
            ->select(
                'S.nombre AS nombre_sucursal',
                'S.id AS id_sucursal',
                'S.linea AS linea_sucursal',
                'D.Departamento',
                'V.CONCEPTO',
                DB::raw('SUM(V.VENTA_MES_ACTUAL) AS ACUMULADO_VENTA_MES')
            )
            ->join('cat_depto AS D', 'V.ID_DEPARTAMENTO', '=', 'D.ID')
            ->join('catSucursales AS S', 'V.SUCURSAL', '=', 'S.id')
            ->groupBy('S.linea', 'S.id', 'D.Departamento', 'V.CONCEPTO')
            ->orderBy('id_sucursal');
        // ->get();
        // $queryGroup = DB::connection('sqlite_equip_db')->table('catSucursales')->from('catSucursales AS cs')
        // $condition = $cs->linea === 'Agricola';
        $queryIterator = VentasSucursal::from('catSucursales AS cs')
            ->select(
                'cs.linea AS LineaDeSucursal',
                'vsd.CONCEPTO',
                'cd.Departamento AS DescripcionDepartamento',
                DB::raw('SUM(vsd.VENTA_MES_ACTUAL) AS AcumuladoVenta'),
                DB::raw('SUM(vsd.ACUMULADO_ACTUAL) AS AcumuladoActual')
            )
            ->join('ventas_suc_depto AS vsd', 'cs.id', '=', 'vsd.SUCURSAL')
            ->join('cat_depto AS cd', 'vsd.ID_DEPARTAMENTO', '=', 'cd.ID')
            ->groupBy(
                'cs.linea',
                'vsd.CONCEPTO',
                'cd.Departamento'
            )
            ->havingRaw('cs.linea = "Agricola" AND vsd.ID_DEPARTAMENTO IN (0,1,2,3,4,5,32)')
            ->orHavingRaw('cs.linea = "Construccion" AND vsd.ID_DEPARTAMENTO IN (51,52,54,55)')
            ->orHavingRaw('cs.linea = "Renta" AND vsd.ID_DEPARTAMENTO IN (6,56)')
            ->orderBy('cd.Departamento')
            ->orderBy('vsd.CONCEPTO')
            ->orderBy('cs.linea');


        // id?Depatamentos AG
        // 0,1,2,3,5,32 , 6*,4*
        // id?Depatamentos IND
        // 51,52,55,56, 54*

        if ($request['per_page'] == -1) {
            $request['per_page'] = 999999;
        }

        $query->when($request->search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('linea_sucursal', 'LIKE', "%{$search}%")
                    ->orWhere('nombre_sucursal', 'LIKE', "%{$search}%")
                    ->orWhere('D.Departamento', 'LIKE', "%{$search}%")
                    ->orWhere('V.CONCEPTO', 'LIKE', "%{$search}%");
            });
        });
        $queryIterator->when($request->search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('cs.linea', 'LIKE', "%{$search}%")
                    ->orWhere('cs.nombre', 'LIKE', "%{$search}%")
                    ->orWhere('cd.Departamento', 'LIKE', "%{$search}%")
                    ->orWhere('vsd.CONCEPTO', 'LIKE', "%{$search}%");
            });
        });

        // if ($request->has('clave_cliente') || $request->has('clave_vendedor') || $request->has('sucursales')) {
        //     $results = $query->get();
        //     $allYears = $results->pluck('year')->unique()->sort()->values();
        // }
        $sumatoriaTotal = $query->get()->sum('ACUMULADO_VENTA_MES');
        $chart = $query->get();
        // $query->chunk(2000, function ($item) {
        //     return $item;
        // });
        $queryGroup = $queryIterator->get();
        $items = $query->paginate($request['per_page'] ?? 10);

        $archivo = database_path('EquipDB.db3');
        $fechaModificacion = File::lastModified($archivo);
        $lastUpdated = date("Y-m-d H:i:s", $fechaModificacion);

        return $this->sendResponseOk(
            compact(
                'lastUpdated',
                'items',
                'sumatoriaTotal',
                'chart',
                'queryGroup'
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