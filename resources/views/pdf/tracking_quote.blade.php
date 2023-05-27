<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cotizacion</title>

    <style>
        .invoice-box {
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 10pt;
            font-family: 'Raleway', 'sans-serif';
            color: #555;
            line-height: normal;
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

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 10pt;
            line-height: 10px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

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
                <td colspan="5">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="{{ public_path() . '/img/etbsa-logo-corporativo-dark.jpeg' }}"
                                    style="width: 100%; max-width: 250px; max-height: 150px;" />
                            </td>

                            <td>
                                Cotizacion: # {{ str_pad($data->id, 5, '0', STR_PAD_LEFT) }}<br />
                                LEAD: # {{ str_pad($data->tracking->id, 5, '0', STR_PAD_LEFT) }}<br />
                                Creacion: {{ $data->created_at }}<br />
                                Vencimiento: {{ $data->date_due }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="5">
                    <table>
                        <tr>
                            <td>
                                Equipos y Tractores del Bajio SA de CV<br />
                                Carr. Panamericana Celaya-Salamanca Km 61.<br />
                                1a. Fracc. de Crespo. Celaya, Gto. C.P. 38120<br />
                                (461) 614.23.23/24/25<br />
                                www.etbsa.com.mx<br />

                            </td>

                            <td>
                                Atencion a: {{ $data->tracking->prospect->full_name }}<br />
                                Telefono: {{ $data->tracking->prospect->phone }}<br />
                                {{ $data->tracking->prospect->company }} <br><br>
                                Vendedor: {{ $data->tracking->attended->name }}<br />
                                Email: {{ $data->tracking->attended->email }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td colspan="6" style="text-transform: uppercase;">
                    Condicion de Pago: {{ $data->label_payment }}
                </td>
            </tr>
            <tr class="heading">
                <td>SKU</td>
                <td>Producto</td>
                <td>Cant.</td>
                <td>Precio U.</td>
                <td>Subtotal</td>
            </tr>

            @foreach ($data->products as $product)
                <tr class="item">
                    <td>
                        {{ $product->sku }}<br />
                    </td>
                    <td style="text-align: justify">
                        {{ $product->name }}<br />
                        <span style="font-size: 8pt;">{{ $product->description }}</span>
                    </td>
                    <td style="text-align: center">{{ $product->quotation->quantity }}</td>
                    <td>${{ number_format($product->quotation->price_unit, 2, '.', ',') }} {{ $data->currency->name }}
                    </td>
                    <td>${{ number_format($product->quotation->subtotal, 2, '.', ',') }} {{ $data->currency->name }}
                    </td>
                </tr>
            @endforeach

            <tr class="total" style="text-align: right">
                <td colspan="3">Subtotal: </td>
                <td colspan="2" style="text-align: right">{{ '$ ' . number_format($data->subtotal, 2, '.', ',') }}
                    {{ $data->currency->name }}
                </td>
            </tr>
            <tr class="total" style="text-align: right">
                <td colspan="3">IVA: </td>
                <td colspan="2">{{ number_format($data->tax, 2, '.', ',') * 100 }}%</td>
            </tr>
            @if ($data->currency->name == 'USD')
                <tr class="total" style="text-align: right">
                    <td colspan="3">T.C.: </td>
                    <td colspan="2">{{ '$ ' . number_format($data->exchange_value, 2, '.', ',') }}
                        {{ $data->currency->name }} </td>
                </tr>
            @endif
            @if ($data->discount > 0)
                <tr class="total" style="text-align: right">
                    <td colspan="3">Descuento: </td>
                    <td colspan="2">{{ '$ ' . number_format($data->discount, 2, '.', ',') }}
                        {{ $data->currency->name }} </td>
                </tr>
            @endif
            <tr class="total" style="text-align: right">
                <td colspan="3">Total {{ $data->currency->name }}: </td>
                <td colspan="2" style="font-size: 12pt">{{ '$ ' . number_format($data->total, 2, '.', ',') }}
                    {{ $data->currency->name }}
                </td>
            </tr>
            {{-- @if ($data->currency->name == 'USD')
                <tr class="total" style="text-align: right">
                    <td colspan="3">Total M.N.: </td>
                    <td colspan="2" style="font-size: 12pt">
                        {{ '$ ' . number_format($data->total * $data->exchange_value, 2, '.', ',') }}
                        MXN
                    </td>
                </tr>
            @endif --}}

        </table>

        <table>

            <tr class="heading">
                <td>Nota del Vendedor</td>
                <td colspan="5"></td>
            </tr>
            <tr class="details">
                <td colspan="5" style="text-align: left">{{ $data->observation }} </td>
            </tr>
            {{-- <tr class="details">
                <td colspan="5" style="text-align: left; font-size: 10pt;">
                    *Los precios de los productos pueden varias sin previo aviso <br>
                    *Solo con Disponibilidad en Inventario <br>
                    *La presente cotización tiene una vigencia de 30 días a partir de la fecha de expedición de la
                    misma<br><br>
                    Sin otro en particular de momento, quedo a sus órdenes para cualquier duda o aclaración.
                </td>
            </tr> --}}
            <tr class="details">
                <td colspan="5"
                    style="text-align: left; font-size: 7pt; font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif; font-style: italic;text-align: justify">
                    Costos de Envío / Flete se definen de acuerdo a la zona <b>**NO INCLUIDO EN EL PRECIO
                        PUBLICADO**</b>,
                    El costo del producto puede variar sin previo aviso, favor de verificar con nosotros antes de
                    apartar.
                    <b>**ETBSA Nunca le Solicitara que deposite dinero sin antes haber visto la Maquinaria**</b>,
                    ETBSA realiza todas las operaciones y tramites de venta de Maquinaria, dentro de sus instalaciones
                    oficiales, las ubicaciones las puede consultar en la pagina oficial
                    <a href="https://www.etbsa.com.mx/"> www.etbsa.com.mx </a> <br>
                    <b>
                        **La presente cotización tiene una vigencia de 30 días a partir de la fecha de expedición de la
                        misma**
                    </b>
                </td>
            </tr>

            <tr class="details">
                <td colspan="5">
                    <div style="text-align: center; font-size: 10pt;">
                        <br>
                        <br>
                        __________________________________________________<br>
                        NOMBRE Y FIRMA DEL GERENTE QUE AUTORIZA
                    </div>
                </td>
            </tr>

        </table>
    </div>
</body>

</html>
