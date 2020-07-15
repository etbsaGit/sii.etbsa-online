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
            ->where('gps', 'like', "%{$row['nombre_gps']}%")
            ->first();
        $cliente_nombre = DB::table('gps_clientes_import')
            ->where('nombre', 'like', "%{$row['cliente']}%")
            ->first();
        if (!$cliente_import) {
            DB::table('gps_clientes_import')->insert([
                'nombre' => $row['cliente'],
                'razon_social' => $row['razon_social'],
                'rfc' => $row['rfc'],
                'gps' => $row['nombre_gps'],
                'sucursal' => $row['sucursal'],
                'departamento' => $row['departamento'],
            ]
            );
        }
        if (!$cliente_nombre) {
            return new GpsGroup([
                'name' => $row['cliente'],
                'agency' => $row['sucursal'] ?? null,
                'department' => $row['departamento'] ?? null,
            ]);
        }
    }
}
