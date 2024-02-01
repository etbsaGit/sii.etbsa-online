<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>A simple, clean, and responsive HTML invoice template</title>
    <style>
        .invoice-box {
            max-width: 100vh;
            margin: auto;
            padding: 0px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 0.8em;
            line-height: 1em;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #626262;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        /* .invoice-box table tr.top table td {
            padding-bottom: 8px;
        } */

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 24px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 5px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        /* .invoice-box table tr.details td {
            padding-bottom: 10px;
        } */

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="8">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="img/logo.png" style="width: 100%; max-width: 180px" />
                            </td>

                            <td>
                                Orden de Compra #: 1{{ str_pad($item->id, 7, '0', STR_PAD_LEFT) }}<br />
                                Creacion: January 1, 2015<br />
                                Vencimiento: February 1, 2015
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="8">
                    <table>
                        <tr>
                            <td colspan="4">
                                Equipos y Tractores del Bajio<br />
                                Carr. Panamericana Celaya - Salamanca Km. 61<br />
                                1ra Fracc. crespo C.P. 38120 Celaya, Gto.<br />
                                R.F.C.: <b>ETB-860812-I23</b>
                            </td>

                            <td colspan="4">
                                Num. Proveedor: <b>{{ $item->supplier->code_equip }}</b><br />
                                {{ $item->supplier->business_name }}<br />
                                R.F.C.: <b>{{ $item->supplier->rfc }}</b><br />
                                {{ $item->supplier->email ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                Sucursal embarque: {{ $item->ship->title }} <br>
                                Direccion: Lorem ipsum dolor sit. Lorem, ipsum.<br>
                                Comprador: {{ $item->elaborated->name }}<br>
                                Email:{{ $item->elaborated->email }}<br>

                            </td>

                            <td colspan="2">
                                Datos Bancarios<br />
                                Cuenta: 21312-321312-3213-21<br />
                                Banco: BBVA
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr class="heading">
                            <td>Uso CFDI:</td>
                            <td>G03</td>
                        </tr>
                        <tr class="heading">
                            <td>Metodo de Pago:</td>
                            <td>PPD</td>
                        </tr>

                    </table>
                </td>
                <td colspan="4">
                    <table>
                        <tr class="heading">
                            <td>Forma de Pago:</td>
                            <td>99</td>
                        </tr>
                        <tr class="heading">
                            <td>
                                Condicion de Pago:
                                ({{ $item->payment_condition < 8 ? 'CONTADO' : 'CREDITO' }}) </td>
                            <td style="font-size: 0.8em;">
                                {{ $item->payment_condition < 8 ? 'CON PREFACTURA' : $item->payment_condition . ' ' .
                                    'Dias habiles despues de recibir Factura' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>



            <tr class="heading">
                <td colspan="1" style="text-align: center;">Cant.</td>
                <td colspan="1" style="text-align: left;">UM</td>
                <td colspan="4" style="text-align: left;">Clv Prod. / Descripcion</td>
                <td colspan="1" style="text-align: right;">Precio</td>
                <td colspan="1" style="text-align: right;">Subtotal</td>
            </tr>
            @foreach ($item->products as $product)
            <tr class="item">
                <td colspan="1" style="text-align: center;">{{ $product->qty }}</td>
                <td colspan="1" style="text-align: left;">
                    {{ $product->unit->clave . '-' . $product->unit->name }}</td>
                <td colspan="4">
                    <div style="text-align: start">
                        <b>{{ $product->claveProduct->c_ClaveProdServ }}</b>
                        ({{ $product->claveProduct->Descripción }})
                    </div>
                    <div style="text-align: justify;">{{ $product->description }}</div>
                </td>
                <td colspan="1" style="text-align: right;">
                    {{ '$ ' . number_format($product->price, 2, '.', ',') }}</td>
                <td colspan="1" style="text-align: right;">
                    {{ '$ ' . number_format($product->subtotal, 2, '.', ',') }}</td>
            </tr>
            @endforeach
            <tr class="total" style="text-align: end;">
                <td colspan="6"></td>
                <td colspan="1">Subtotal:</td>
                <td colspan="1" style="text-align: right;">
                    {{ '$ ' . number_format($item->amounts->subtotal, 2, '.', ',') }}</td>
            </tr>
            <tr class="total" style="text-align: end;">
                <td colspan="6"></td>
                <td colspan="1">IVA 16%:</td>
                <td colspan="1" style="text-align: right;">
                    {{ '$ ' . number_format($item->amounts->tax, 2, '.', ',') }}</td>
            </tr>
            @if ($item->amounts->tax_isr > 0)
            <tr class="total" style="text-align: end;">
                <td colspan="6"></td>
                <td colspan="1">ISR:</td>
                <td colspan="1" style="text-align: right;">
                    {{ '$ ' . number_format($item->amounts->tax_isr, 2, '.', ',') }}</td>
            </tr>
            @endif
            @if ($item->amounts->tax_iva_retenido > 0)
            <tr class="total" style="text-align: end;">
                <td colspan="6"></td>
                <td colspan="1">IVA Retenido:</td>
                <td colspan="1" style="text-align: right;">
                    {{ '$ ' . number_format($item->amounts->tax_iva_retenido, 2, '.', ',') }}</td>
            </tr>
            @endif
            @if ($item->amounts->tax_retencion_cedular > 0)
            <tr class="total" style="text-align: end;">
                <td colspan="6"></td>
                <td colspan="1">Cedular GTO 2%:</td>
                <td colspan="1" style="text-align: right;">
                    {{ '$ ' . number_format($item->amounts->tax_retencion_cedular, 2, '.', ',') }}</td>
            </tr>
            @endif
            @if ($item->amounts->tax_retencion_125 > 0)
            <tr class="total" style="text-align: end;">
                <td colspan="6"></td>
                <td colspan="1">Retencion 1.25%:</td>
                <td colspan="1" style="text-align: right;">
                    {{ '$ ' . number_format($item->amounts->tax_retencion_125, 2, '.', ',') }}</td>
            </tr>
            @endif
            @if ($item->amounts->tax_flete > 0)
            <tr class="total" style="text-align: end;">
                <td colspan="6"></td>
                <td colspan="1">Flete:</td>
                <td colspan="1" style="text-align: right;">
                    {{ '$ ' . number_format($item->amounts->tax_flete, 2, '.', ',') }}</td>
            </tr>
            @endif
            @if ($item->amounts->discount > 0)
            <tr class="total" style="text-align: end;">
                <td colspan="6"></td>
                <td colspan="1">Descuento:</td>
                <td colspan="1" style="text-align: right;">
                    {{ '$ ' . number_format($item->amounts->discount, 2, '.', ',') }}</td>
            </tr>
            @endif


            <tr class="total" style="text-align: end;">
                <td colspan="6"></td>
                <td colspan="1">Total:</td>
                <td colspan="1" style="text-align: right;">
                    {{ '$ ' . number_format($item->amounts->total, 2, '.', ',') }}</td>
            </tr>
            <tr class="information" style="font-size: 0.7em; line-height: 1em;">
                <td colspan="8">
                    <table>
                        @if ($item->note)
                        <tr style="padding-bottom: 0px;">
                            <td style="padding-bottom: 0px;">
                                NOTA para el Proveedor:<br />
                                {{ $item->note }}
                            </td>
                        </tr>
                        @endif
                        <tr style="padding-bottom: 0px;">
                            <td style="padding-bottom: 0px;">
                                Atencion Proveedor si tu Factura NO Coincide con los Requisitos Fiscales de esta Orden
                                compra NO sera programada para Pago<br />
                                *Poner en copia al/los correos:sanchezblanca@etbsa.com.mx
                            </td>
                        </tr>
                        <tr style="padding-bottom: 0px;">
                            <td style="padding-bottom: 0px;">
                                El Numero de Orden de Compra debe aparecer siempre en la Factura, Etiquetas y Notas de
                                Envio correspondientes, inclusive en los embarques a terceros. La Factura debera
                                contener la misma infomacion(linea por linea) que la mostrada en esta Orden de Compra,
                                Para entregas contactar al Requisitor.
                            </td>
                        </tr>
                        <tr style="padding-bottom: 0px;">
                            <td style="padding-bottom: 0px;">
                                Nota Contabilidad (Cargos):<br>
                                @foreach ($item->charges as $charge)
                                <span style="font-size:0.8em; line-height: 0.5em;">
                                    {{ $charge->agency->title . ',' . $charge->department->title . ',' .
                                    $charge->percent . '%' }}
                                </span><br />
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>