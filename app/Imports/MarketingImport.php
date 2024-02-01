<?php

namespace App\Imports;

use App\Components\Marketing\Models\Marketing;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MarketingImport implements ToModel, WithHeadingRow
{
    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // DB::table('marketing_import')->insert($row);
        $format = "m/d/Y H:i";
        return new Marketing([
            'ID_SUC' => $row['id_suc'],
            'SUCURSAL' => $row['sucursal'],
            'NO_ECO' => $row['no_eco'],
            'NO_DOCUMENTO' => $row['no_documento'],
            'TIPO_DOCUMENTO' => $row['tipo_documento'],
            'NO_DOC_INV' => $row['no_doc_inv'],
            'MODULO' => $row['modulo'],
            'SERIE_FISCAL' => $row['serie_fiscal'],
            'FOLIO_FISCAL' => $row['folio_fiscal'],
            'TIPO_DE_VENTA' => $row['tipo_de_venta'],
            // 'FECHA_REQ' => Carbon::createFromFormat($format,$row['fecha_req']),
            // 'FECHA_FACTURA' => Carbon::createFromFormat($format,$row['fecha_factura']),
            'MES' => $row['mes'],
            'ANIO' => $row['anio'],
            'MARCA' => $row['marca'],
            'MODELO' => $row['modelo'],
            'DESCRIPCION_PRODUCTO' => $row['descripcion_producto'],
            'NIP' => $row['nip'],
            'CLAVE_CLIENTE' => $row['clave_cliente'],
            'NOMBRE_CLIENTE' => $row['nombre_cliente'],
            'VENDEDOR' => $row['vendedor'],
            'EMAIL' => $row['email'],
            'CALLE' => $row['calle'],
            'CIUDAD' => $row['ciudad'],
            'ESTADO' => $row['estado'],
            'CP' => $row['cp'],
            'RFC_COMPANIA' => $row['rfc_compania'],
            'PAGO_EFECTIVO' => $row['pago_efectivo'],
            'PAGADO_TARJETA_CREDITO' => $row['pagado_tarjeta_credito'],
            'PAGADO_CHEQUE' => $row['pagado_cheque'],
            'IMPUESTO_REG' => $row['impuesto_reg'],
            'MONEDA' => $row['moneda'],
            'TIPO_CAMBIO' => $row['tipo_cambio'],
            'VALOR_FORANEO' => $row['valor_foraneo'],
            'METODO_PAGO' => $row['metodo_pago'],
            'PRECIO_VENTA' => $row['precio_venta'],
            'IMPUESTO_INVENTARIO' => $row['impuesto_inventario'],
            'SUBTOTAL' => $row['subtotal'],
            'IMPUESTO' => $row['impuesto'],
            'TOTAL' => $row['total'],
            'TOTAL_COSTO' => $row['total_costo'],
            'MARGEN' => $row['margen'],
            'PORCENTAJE_MARGEN' => $row['porcentaje_margen'],
        ]
        );
        // $data = Marketing::create($row);
        // return $data;
    }
}
