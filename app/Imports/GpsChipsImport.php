<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Components\Gps\Models\GpsChips;

class GpsChipsImport implements ToModel, WithHeadingRow
{
    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $gps_chip = DB::table('gps_chips_import')->where('sim', 'like', "%{$row['linea']}%")->first();

        $date = new Carbon($row['fecha_activacion']);
        $date_due = $date->setYear(Carbon::now()->year);

        if (!$gps_chip) {
             DB::table('gps_chips_import')->insert([
                'sim' => $row['linea'],
                'cuenta' => $row['cuenta'],
                'imei' => $row['imei'],
                'costo' => $row['costo'],
                'fecha_activacion' => new Carbon($row['fecha_activacion']),
                'fecha_carga' => Carbon::now(),
            ]
            );

            return new GpsChips([
                'sim' => $row['linea'],
                'cuenta' => $row['cuenta'] ?? null,
                'imei' => $row['imei'] ?? null,
                'costo' => $row['costo'] ?? null,
                'fecha_activacion' => new Carbon($row['fecha_activacion']),
                'fecha_renovacion' => $date_due,
            ]);
        }
    }
}
