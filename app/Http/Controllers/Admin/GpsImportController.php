<?php

namespace App\Http\Controllers\Admin;

use App\Components\Gps\Models\Gps;
use App\Components\Gps\Models\GpsChips;
use App\Components\Gps\Models\GpsGroup;
use App\Imports\ClientesGpsImport;
use App\Imports\GpsChipsImport;
use App\Imports\GpsImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class GpsImportController extends AdminController
{
    public function importClientesGps()
    {
        Excel::import(new ClientesGpsImport, request()->file('file_clientes_gps'));
        return $this->sendResponseOk(true, 'IMPORTACION COMPLETA');
    }

    public function importGps()
    {
        Excel::import(new GpsImport, request()->file('file_gps'));
        return $this->sendResponseOk(true, 'IMPORTACION COMPLETA');
    }

    public function importChips()
    {
        Excel::import(new GpsChipsImport, request()->file('file_gps_chips'));
        return $this->sendResponseOk(true, 'IMPORTACION COMPLETA');
    }

    public function matchingChipsInGps(Type $var = null)
    {
        $gps_import = DB::table('gps_import')->get();

        foreach ($gps_import as $gps) {
            $group = null;
            $chip = null;
            $exist_gps = null;
            $date = new Carbon($gps->creacion);
            $renew_date = $date->setYear(Carbon::now()->year);

            // $exist_gps = Gps::where('name',"{$gps->nombre}")->first();
            $exist_gps = Gps::whereHas('chip', function ($q) use ($gps) {
                return $q->where('sim', $gps->sim);
            })->first();

            // $cliente = DB::table('gps_clientes_import')->where('gps', "{$gps->nombre}")->first();
            $cliente = DB::table('gps_clientes_import')
                ->where('sim', "{$gps->sim}")
                ->where('gps', "{$gps->nombre}")
                ->first();

            if ($cliente) {
                $group = GpsGroup::where('name', "{$cliente->nombre}")->first();
                $chip = GpsChips::where('sim', "{$cliente->sim}")->first();
            }

            if ($chip && $group && !$exist_gps) {
                $new_gps = new Gps([
                    'name' => $gps->nombre,
                    'gps_group_id' => $group->id,
                    'gps_chip_id' => $chip->id,
                    'installation_date' => $gps->creacion,
                    'payment_type' => $group->department ? 'CARGO' : 'CONTADO',
                    'renew_date' => $renew_date,
                    'uploaded_by' => \Auth::user()->id,
                ]);
                $new_gps->save();
                $chip->gps()->associate($new_gps->id);
                $chip->save();
            } else if (!$cliente) {
                $record = new Gps([
                    'name' => $gps->nombre,
                    'gps_group_id' => $group->id ?? null,
                    'gps_chip_id' => $chip->id ?? null,
                    'installation_date' => $gps->creacion,
                    'renew_date' => $renew_date,
                    'uploaded_by' => \Auth::user()->id,
                ]);
                $record->save();
            }
        }

        return $this->sendResponseOk(true, 'IMPORTACION COMPLETA');

    }
}
// 4611218595
