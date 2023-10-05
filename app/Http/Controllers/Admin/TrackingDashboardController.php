<?php

namespace App\Http\Controllers\Admin;


use App\Components\Common\Models\ExchangeRates;
use App\Components\Tracking\Models\Prospect;
use App\Components\Tracking\Repositories\TrackingRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Auth;

class TrackingDashboardController extends AdminController
{
    /**
     * @var TrackingRepository
     */
    private $trackingRepository;

    /**
     * TrackingDashboardController constructor.
     * @param TrackingRepository $trackingRepository
     */
    public function __construct(TrackingRepository $trackingRepository)
    {
        $this->trackingRepository = $trackingRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $year = (int) request()->get('year') != 0 ? (int) request()->get('year') : Carbon::now()->year;
        $data = $this->trackingRepository->dashboardData(request()->all());
        $countActivos = $data->where('estatus_id', 1)->whereNotNull('date_next_tracking')->count();
        $countPerdidas = $data->where('estatus_id', 2)->whereNotNull('date_lost_sale')->count();
        $countGanadas = $data->where('estatus_id', 3)->whereNotNull('date_won_sale')->count();
        $TotalCount = $countActivos + $countPerdidas + $countGanadas;

        // TODO: obtener Chart Categorias conteo de Activas Perdidad y Gandas

        $seguimientosPorVendedor = $data->groupBy(function ($item) {
            if (
                ($item->estatus_id == 1 && $item->date_next_tracking != null) ||
                ($item->estatus_id == 2 && $item->date_lost_sale != null) ||
                ($item->estatus_id == 3 && $item->date_won_sale != null)
            ) {
                return $item->attended_by . '_' . $item->estatus_id;
            }
        })->map(function ($groupedItems) {
            return [
                'vendedor' => $groupedItems[0]->attended->name,
                'estatus' => $groupedItems[0]->estatus->title,
                'total_comprado' => $groupedItems->sum('amount'),
                'count' => $groupedItems->count()
            ];
        })->values();
        $seguimientosPorVendedorCategoria = $data->groupBy(function ($item) {
            if (
                ($item->estatus_id == 1 && $item->date_next_tracking != null) ||
                ($item->estatus_id == 2 && $item->date_lost_sale != null) ||
                ($item->estatus_id == 3 && $item->date_won_sale != null)
            ) {
                return $item->attended_by . '_' . $item->title . '_' . $item->estatus_id;
            }
        })->map(function ($groupedItems) {
            return [
                'vendedor' => $groupedItems[0]->attended->name,
                'categoria' => $groupedItems[0]->title,
                'estatus' => $groupedItems[0]->estatus->title,
                'total_comprado' => $groupedItems->sum('amount'),
                'count' => $groupedItems->count()
            ];
        })->values();

        $seguimientosPorCategoria = $data->groupBy(function ($item) {
            if (
                ($item->estatus_id == 1 && $item->date_next_tracking != null) ||
                ($item->estatus_id == 2 && $item->date_lost_sale != null) ||
                ($item->estatus_id == 3 && $item->date_won_sale != null)
            ) {
                return $item->title . '_' . $item->estatus_id;
            }
        })->map(function ($groupedItems) {
            return [
                'categoria' => $groupedItems[0]->title,
                'estatus' => $groupedItems[0]->estatus->title,
                'total_comprado' => $groupedItems->sum('amount'),
                'count' => $groupedItems->count()
            ];
        })->values();

        // TODO: obtener Chart Vendedor conteo de Activas Perdidad y Gandas

        // TODO: obtener Chart Sucursal conteo de Activas Perdidad y Gandas
        $seguimientosPorSucursal = $data->groupBy(function ($item) {
            if (
                ($item->estatus_id == 1 && $item->date_next_tracking != null) ||
                ($item->estatus_id == 2 && $item->date_lost_sale != null) ||
                ($item->estatus_id == 3 && $item->date_won_sale != null)
            ) {
                return $item->agency_id . '_' . $item->estatus_id;
            }


        })->map(function ($groupedItems) {
            return [
                'sucursal' => $groupedItems[0]->agency->title,
                'estatus' => $groupedItems[0]->estatus->title,
                'total_comprado' => $groupedItems->sum('amount'),
                'count' => $groupedItems->count()
            ];
        })->values();

        // TODO: obtener Chart Resumen Mes del Año Actual conteo de Activas Perdidad y Gandas
        $lineChart = [];
        // // Filtra los registros con estatus_id igual a 1 (Activos)
        $activos = $data->where('estatus_id', 1);

        // Obtén el año actual
        // $year = (int) request()->get('year') != 0 ? (int) request()->get('year') : Carbon::now()->year;

        // Filtra los registros para el año actual
        $lineChart['activosDelAñoActual'] = $data->where('estatus_id', 1)
            ->whereNotNull('date_next_tracking')
            ->filter(function ($item) use ($year) {
                return Carbon::parse($item->date_next_tracking)->year === $year;
            })
            ->map(function ($item) {
                return [
                    'month' => Carbon::parse($item->date_next_tracking)->month,
                    'total' => $item->amount,
                ];
            })->groupBy('month')->map(function ($group) {
            return [
                'total' => $group->sum('total'),
                'count' => $group->count(),
            ];
        })->sortKeys()->union(array_fill_keys(range(1, 12), ['total' => 0, 'count' => 0]))->sortKeys();

        $lineChart['perdidasDelAñoActual'] = $data->where('estatus_id', 2)
            ->whereNotNull('date_lost_sale')
            ->filter(function ($item) use ($year) {
                return Carbon::parse($item->date_lost_sale)->year === $year;
            })->map(function ($item) {
            return [
                'month' => Carbon::parse($item->date_lost_sale)->month,
                'total' => $item->amount,
            ];
        })->groupBy('month')->map(function ($group) {
            return [
                'total' => $group->sum('total'),
                'count' => $group->count(),
            ];
        })->sortKeys()->union(array_fill_keys(range(1, 12), ['total' => 0, 'count' => 0]))->sortKeys();

        $lineChart['ganadasDelAñoActual'] = $data->where('estatus_id', 3)
            ->whereNotNull('date_won_sale')
            ->filter(function ($item) use ($year) {
                return Carbon::parse($item->date_won_sale)->year === $year;
            })->map(function ($item) {
            return [
                'month' => Carbon::parse($item->date_won_sale)->month,
                'total' => $item->amount,
            ];
        })->groupBy('month')->map(function ($group) {
            return [
                'total' => $group->sum('total'),
                'count' => $group->count(),
            ];
        })->sortKeys()->union(array_fill_keys(range(1, 12), ['total' => 0, 'count' => 0]))->sortKeys();

        return $this->sendResponseOk(
            compact(
                'lineChart',
                'seguimientosPorCategoria',
                'seguimientosPorSucursal',
                'seguimientosPorVendedor',
                'seguimientosPorVendedorCategoria',
                'countActivos',
                'countPerdidas',
                'countGanadas',
                'TotalCount'
            ),
            "Dashboard Stats"
        );
    }


    public function resources()
    {
        if (Auth::user()->isSuperUser()) {
            $agencies = DB::table('agencies')->get(['id', 'code', 'title']);
            $departments = DB::table('departments')->get(['id', 'title']);
            // $categories = DB::table('cat_product_category')->get(['id', 'name']);
        } else {
            $agencies = Auth::user()->seller_agency->map(function ($i, $k) {
                return ['id' => $i->id, 'code' => $i->code, 'title' => $i->title];
            });
            $departments = Auth::user()->seller_type->map(function ($i, $k) {
                return ['id' => $i->id, 'title' => $i->title];
            });
            // $categories = Auth::user()->seller_category->map(function ($i, $k) {
            //     return ['id' => $i->id, 'name' => $i->name];
            // });
        }
        $categories = DB::table('cat_product_category')->get(['id', 'name']);

        $currency = DB::table('currency')->get(['id', 'name']);
        $prospects = Prospect::with('township')->get()->map->only(['id', 'full_name', 'email', 'company', 'rfc', 'town', 'phone', 'township']);
        $exchange_value = ExchangeRates::latest()->first()->value;
        return $this->sendResponseOk(compact('agencies', 'departments', 'prospects', 'currency', 'categories', 'exchange_value'));
    }

}