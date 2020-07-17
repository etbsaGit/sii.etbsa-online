<?php

namespace App\Imports;

use App\Components\Gps\Models\GpsGroup;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientesGpsImport implements ToModel, WithHeadingRow
{
    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $cliente_import = DB::table('gps_clientes_import')
            ->where('sim', "%{$row['sim']}%")
            ->first();

        $cliente = GpsGroup::where('name', "{$row['cliente']}")->first();

        if (!$cliente_import) {
            DB::table('gps_clientes_import')->insert([
                'nombre' => $row['cliente'],
                'razon_social' => $row['razon_social'],
                'rfc' => $row['rfc'],
                'gps' => $row['nombre_gps'],
                'sim' => $row['sim'] ?? null,
                'sucursal' => $row['sucursal'] ?? null,
                'departamento' => $row['departamento'] ?? null,
            ]
            );
        }
        if (!$cliente) {
            return new GpsGroup([
                'name' => $row['cliente'],
                'razon_social' => $row['razon_social'],
                'rfc' => $row['rfc'],
                'agency' => $row['sucursal'] ?? null,
                'department' => $row['departamento'] ?? null,
            ]);
        }
    }
}
