<table>
  <thead>
    <tr>
      <th>ID_SUC</th>
      <th>SUCURSAL</th>
      <th>NO_ECO</th>
      <th>NO_DOCUMENTO</th>
      <th>TIPO_DOCUMENTO</th>
      <th>NO_DOC_INV</th>
      <th>MODULO</th>
      <th>SERIE_FISCAL</th>
      <th>FOLIO_FISCAL</th>
      <th>TIPO_DE_VENTA</th>
      <th>MES</th>
      <th>AÑO</th>
      <th>MARCA</th>
      <th>MODELO</th>
      <th>DESCRIPCION_PRODUCTO</th>
      <th>NIP</th>
      <th>CLAVE_CLIENTE</th>
      <th>NOMBRE_CLIENTE</th>
      <th>VENDEDOR</th>
      <th>EMAIL</th>
      <th>CALLE</th>
      <th>CIUDAD</th>
      <th>ESTADO</th>
      <th>CP</th>
      <th>RFC_COMPANIA</th>
      <th>PAGO_EFECTIVO</th>
      <th>PAGADO_TARJETA_CREDITO</th>
      <th>PAGADO_CHEQUE</th>
      <th>IMPUESTO_REG</th>
      <th>MONEDA</th>
      <th>TIPO_CAMBIO</th>
      <th>VALOR_FORANEO</th>
      <th>METODO_PAGO</th>
      <th>PRECIO_VENTA</th>
      <th>IMPUESTO_INVENTARIO</th>
      <th>SUBTOTAL</th>
      <th>IMPUESTO</th>
      <th>TOTAL</th>
      <th>TOTAL_COSTO</th>
      <th>MARGEN</th>
      <th>PORCENTAJE_MARGEN</th>
    </tr>
  </thead>
  <tbody>
    @foreach($history_sale as $item)
    <tr>
      <td>{{ $item->ID_SUC }}</td>
      <td>{{ $item->SUCURSAL }}</td>
      <td>{{ $item->NO_ECO }}</td>
      <td>{{ $item->NO_DOCUMENTO }}</td>
      <td>{{ $item->TIPO_DOCUMENTO }}</td>
      <td>{{ $item->NO_DOC_INV }}</td>
      <td>{{ $item->MODULO }}</td>
      <td>{{ $item->SERIE_FISCAL }}</td>
      <td>{{ $item->FOLIO_FISCAL }}</td>
      <td>{{ $item->TIPO_DE_VENTA }}</td>
      <td>{{ $item->MES }}</td>
      <td>{{ $item->ANIO }}</td>
      <td>{{ $item->MARCA }}</td>
      <td>{{ $item->MODELO }}</td>
      <td>{{ $item->DESCRIPCION_PRODUCTO }}</td>
      <td>{{ $item->NIP }}</td>
      <td>{{ $item->CLAVE_CLIENTE }}</td>
      <td>{{ $item->NOMBRE_CLIENTE }}</td>
      <td>{{ $item->VENDEDOR }}</td>
      <td>{{ $item->EMAIL }}</td>
      <td>{{ $item->CALLE }}</td>
      <td>{{ $item->CIUDAD }}</td>
      <td>{{ $item->ESTADO }}</td>
      <td>{{ $item->CP }}</td>
      <td>{{ $item->RFC_COMPANIA }}</td>
      <td>{{ $item->PAGO_EFECTIVO }}</td>
      <td>{{ $item->PAGADO_TARJETA_CREDITO }}</td>
      <td>{{ $item->PAGADO_CHEQUE }}</td>
      <td>{{ $item->IMPUESTO_REG }}</td>
      <td>{{ $item->MONEDA ?? "MXN" }}</td>
      <td>{{ $item->TIPO_CAMBIO }}</td>
      <td>{{ $item->VALOR_FORANEO }}</td>
      <td>{{ $item->METODO_PAGO }}</td>
      <td>{{ $item->PRECIO_VENTA }}</td>
      <td>{{ $item->IMPUESTO_INVENTARIO }}</td>
      <td>{{ $item->SUBTOTAL }}</td>
      <td>{{ $item->IMPUESTO }}</td>
      <td>{{ $item->TOTAL }}</td>
      <td>{{ $item->TOTAL_COSTO }}</td>
      <td>{{ $item->MARGEN }}</td>
      <td>{{ $item->PORCENTAJE_MARGEN }}</td>
    </tr>
    @endforeach
  </tbody>
</table>