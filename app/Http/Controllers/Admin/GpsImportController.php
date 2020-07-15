<?php

namespace App\Http\Controllers\Admin;

use App\Components\Gps\Models\Gps;
use App\Components\Gps\Models\GpsChips;
use App\Components\Gps\Models\GpsGroup;
use App\Imports\ClientesGpsImport;
use App\Imports\GpsChipsImport;
use App\Imports\GpsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

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
            $date = new Carbon($gps->creacion);
            $date2 = $date->setYear(Carbon::now()->year);

            $cliente = DB::table('gps_clientes_import')->where('gps',"{$gps->nombre}")->first();
            if ($cliente) {
                $group = GpsGroup::where('name',"{$cliente->nombre}")->first();
                $chip = GpsChips::where('sim',"{$gps->sim}")->first();
            }

            if ($chip && $group) {
                $record = new Gps([
                    'name' => $gps->nombre,
                    'gps_group_id' => $group->id,
                    'gps_chip_id' => $chip->id,
                    'installation_date' => $gps->creacion,
                    'renew_date' => $date2->addYear(),
                    'uploaded_by' => \Auth::user()->id,
                ]);
                $record->save();
                $chip->gps()->associate($record->id);
                $chip->save();
            }
        }

        return $this->sendResponseOk(true, 'IMPORTACION COMPLETA');

    }
}
// 4611218595
