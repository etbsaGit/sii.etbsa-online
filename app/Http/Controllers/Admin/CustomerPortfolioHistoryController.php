<?php

namespace App\Http\Controllers\Admin;

use App\Components\EquipDB\Models\CarteraClientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CustomerPortfolioHistoryController extends AdminController
{
    public function __construct()
    {
    }
    public function index(Request $request)
    {

        $filters = $request->all();
        $query = CarteraClientes::from('cartera_clientes AS C')
            ->select(
                'C.Sucursal',
                'C.Linea',
                DB::raw('substr(C.v_cliente, 1, instr(v_cliente, " - ") - 1) AS Clave_Cliente'),
                DB::raw('substr(C.v_cliente, instr(v_cliente, " - ") + 3) AS Cliente'),
                'C.v_nombre_modulo2',
                'C.v_total',
                'C.v_total_vencido',
                'C.v_total_neto',
                'C.v_mas90',
                'C.v_mas60',
                'C.v_mas30',
                'C.v_mas15',
                'C.v_mas1',
                'C.v_por_vencer',
            )
            ->orderBy('Clave_Cliente');

        $query->when($filters['clave_cliente'] ?? null, function ($query, $clave_cliente) {
            $query->where(function ($q) use ($clave_cliente) {
                if (is_array($clave_cliente)) {
                    foreach ($clave_cliente as $term) {
                        $q->orWhere(DB::raw('substr(C.v_cliente, 1, instr(v_cliente, " - ") - 1)'), 'LIKE', "%{$term}%");
                        $q->orWhere(DB::raw('substr(C.v_cliente, instr(v_cliente, " - ") + 3)'), 'LIKE', "%{$term}%");
                    }
                }
            });
        })->when($filters['sucursales'] ?? null, function ($query, $sucursales) {
            $query->where(function ($q) use ($sucursales) {
                if (is_array($sucursales)) {
                    foreach ($sucursales as $term) {
                        $q->orWhere('Sucursal', 'LIKE', "%{$term}%");
                    }
                }
            });
        })->when($filters['lineas'] ?? null, function ($query, $lineas) {
            $query->where(function ($q) use ($lineas) {
                if (is_array($lineas)) {
                    foreach ($lineas as $term) {
                        $q->orWhere('Linea', 'LIKE', "%{$term}%");
                    }
                }
            });
        })->when($filters['modulos'] ?? null, function ($query, $modulos) {
            $query->where(function ($q) use ($modulos) {
                if (is_array($modulos)) {
                    foreach ($modulos as $term) {
                        $q->orWhere('v_nombre_modulo2', 'LIKE', "%{$term}%");
                    }
                }
            });
        });

        $query->when($request->search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere(DB::raw('substr(C.v_cliente, 1, instr(v_cliente, " - ") - 1)'), 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw('substr(C.v_cliente, instr(v_cliente, " - ") + 3)'), 'LIKE', "%{$search}%")
                    ->orWhere('Sucursal', 'LIKE', "%{$search}%");
            });
        });

        if ($request['per_page'] == -1) {
            $request['per_page'] = 999999;
        }

        $sumatoriaTotal = $query->get()->sum('v_total_neto');

        $carteraPorCliente = $query->get()->groupBy('v_cliente')->map(function ($groupedItems) {
            return [
                'cliente' => $groupedItems[0]->v_cliente,
                'total_v_mas90' => $groupedItems->sum('v_mas90'),
                'total_v_mas60' => $groupedItems->sum('v_mas60'),
                'total_v_mas30' => $groupedItems->sum('v_mas30'),
                'total_v_mas15' => $groupedItems->sum('v_mas15'),
                'total_v_mas1' => $groupedItems->sum('v_mas1'),
                'total_v_por_vencer' => $groupedItems->sum('v_por_vencer'),
                'total_v_total' => $groupedItems->sum('v_total'),
                'total_v_total_vencido' => $groupedItems->sum('v_total_vencido'),
                'total_v_total_neto' => $groupedItems->sum('v_total_neto'),
                'count' => $groupedItems->count()
            ];
        })->values();
        $carteraPorModulo = $query->get()->groupBy('v_nombre_modulo2')->map(function ($groupedItems) {
            return [
                'modulo' => $groupedItems[0]->v_nombre_modulo2,
                'total_v_mas90' => $groupedItems->sum('v_mas90'),
                'total_v_mas60' => $groupedItems->sum('v_mas60'),
                'total_v_mas30' => $groupedItems->sum('v_mas30'),
                'total_v_mas15' => $groupedItems->sum('v_mas15'),
                'total_v_mas1' => $groupedItems->sum('v_mas1'),
                'total_v_por_vencer' => $groupedItems->sum('v_por_vencer'),
                'total_v_total' => $groupedItems->sum('v_total'),
                'total_v_total_vencido' => $groupedItems->sum('v_total_vencido'),
                'total_v_total_neto' => $groupedItems->sum('v_total_neto'),
                'count' => $groupedItems->count()
            ];
        })->values();
        $carteraPorSucursal = $query->get()->groupBy('Sucursal')->map(function ($groupedItems) {
            return [
                'sucursal' => $groupedItems[0]->Sucursal,
                'total_v_mas90' => $groupedItems->sum('v_mas90'),
                'total_v_mas60' => $groupedItems->sum('v_mas60'),
                'total_v_mas30' => $groupedItems->sum('v_mas30'),
                'total_v_mas15' => $groupedItems->sum('v_mas15'),
                'total_v_mas1' => $groupedItems->sum('v_mas1'),
                'total_v_por_vencer' => $groupedItems->sum('v_por_vencer'),
                'total_v_total' => $groupedItems->sum('v_total'),
                'total_v_total_vencido' => $groupedItems->sum('v_total_vencido'),
                'total_v_total_neto' => $groupedItems->sum('v_total_neto'),
                'count' => $groupedItems->count()
            ];
        })->values();


        $chart = $query->get();

        $items = $query->paginate($request['per_page'] ?? 10);

        $archivo = database_path('EquipDB.db3');
        $fechaModificacion = File::lastModified($archivo);
        $lastUpdated = date("Y-m-d H:i:s", $fechaModificacion);

        return $this->sendResponseOk(
            compact(
                'items',
                'lastUpdated',
                'sumatoriaTotal',
                'carteraPorCliente',
                'carteraPorModulo',
                'carteraPorSucursal',
            )
        );
    }

    public function getOptions()
    {
        $options = [
            'Clientes' => CarteraClientes::distinct()
                ->select(
                    DB::raw('substr(v_cliente, 1, instr(v_cliente, " - ") - 1) AS Clave_Cliente'),
                    DB::raw('substr(v_cliente, instr(v_cliente, " - ") + 3) AS Cliente')
                )
                ->get(),
            'Sucursales' => CarteraClientes::distinct()
                ->select('Sucursal as name')
                ->pluck('name'),
            'Modulo' => CarteraClientes::distinct()
                ->select('v_nombre_modulo2 as modulo')
                ->pluck('modulo'),
            'Linea' => CarteraClientes::distinct()
                ->select('Linea as linea')
                ->pluck('linea'),
        ];
        return $this->sendResponseOk(compact('options'));
    }
}