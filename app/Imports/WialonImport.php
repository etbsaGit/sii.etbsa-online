<?php

namespace App\Imports;

use App\Components\Gps\Models\Gps;
use App\Components\Gps\Models\GpsWialon;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WialonImport implements ToModel, WithHeadingRow
{
    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $gps_wialon = GpsWialon::where('nombre', 'like', "%{$row['nombre']}%")->first();
        /**
         * obtener la fecha de vencimiento de este registro
         */
        $date_create = new Carbon($row['creado']);
        $date_due = $date_create->setYear(Carbon::now()->year);

        if (!$gps_wialon) {
            $gps = new Gps([
                'name' => $row['nombre'],
                'sim' => $row['telefono'],
                'activation_date' => new Carbon($row['creado']),
                'due_date' => $date_due,
                'uploaded_by' => \Auth::user()->id,
            ]);

            $gps->save();

            return new GpsWialon([
                'nombre' => $row['nombre'],
                'sim' => $row['telefono'],
                'uiid' => $row['uid'],
                'grupo' => $row['grupos'],
            ]);
        }

    }
}
