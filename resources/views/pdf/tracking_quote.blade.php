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
                                Folio #: {{ $data->id }}<br />
                                Creada: {{ $data->created_at }}<br />
                                Vence: {{ $data->date_due }}
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
                                Vendedor: {{ $data->tracking->assigned->name }}<br />
                                Email: {{ $data->tracking->assigned->email }}

                            </td>

                            <td>
                                Cliente: {{ $data->tracking->prospect->full_name }}<br />
                                Telefono: {{ $data->tracking->prospect->phone }}
                                {{ $data->tracking->prospect->company }}<br />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>SKU</td>
                <td>Producto</td>
                <td>Cantidad</td>
                <td>Precio Unitario</td>
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


            <tr class="item">
                <td colspan="5"></td>
            </tr>
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
            <tr class="total" style="text-align: right">
                <td colspan="3">Descuento: </td>
                <td colspan="2">{{ '$ ' . number_format($data->discount, 2, '.', ',') }}
                    {{ $data->currency->name }} </td>
            </tr>
            <tr class="total" style="text-align: right">
                <td colspan="3">Total: </td>
                <td colspan="2">{{ '$ ' . number_format($data->total, 2, '.', ',') }} {{ $data->currency->name }}
                </td>
            </tr>

        </table>

        <table>
            <tr class="heading">
                <td>Observacion Vendedor</td>
                <td colspan="5"></td>
            </tr>
            <tr class="details">
                <td colspan="6" style="text-align: left">{{ $data->observation }} </td>
            </tr>
            <tr class="details">
                <td colspan="6" style="text-align: left; font-size: 8pt;">
                    Los Precios de los Productos pueden varias sin Previo Aviso <br>
                </td>
            </tr>
            <tr class="details">
                <td colspan="6" style="text-align: left; font-size: 8pt;">
                    Solo con Disponibilidad de Inventario <br>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
