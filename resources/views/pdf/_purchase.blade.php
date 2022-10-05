<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Quote</title>

    <style>
        .invoice-box {
            max-width: 900px;
            margin: auto;
            padding: 30px;
            border: 0px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 12px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
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
            font-size: 45px;
            line-height: 45px;
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
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="{{ public_path() . '/img/etbsa-logo-agricola.png' }}"
                                    style="width: 100%; max-width: 125px; height: 60px;" />
                            </td>

                            <td>
                                Orden de Compra #: {{ str_pad($data->id, 5, '0', STR_PAD_LEFT) }}<br />
                                Creada: January 1, 2015<br />
                                Expira: February 1, 2015
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                R.F.C.: ETB-860812-I23 <br />
                                Equipos y Tractores del Bajio SA de CV<br />
                                Carretera Panamericana Celaya - Salamanca Km. 61<br />
                                1ra Fracc. crespo C.P. 38120 Celaya, Gto.
                            </td>

                            <td>
                                Acme Corp.<br />
                                John Doe<br />
                                john@example.com
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Datos de Facturacion</td>

                <td>Check #</td>
            </tr>

            <tr class="details">
                <td>Metodo de Pago</td>
                <td>{{ $data->metodo_pago }} </td>
            </tr>
            <tr class="details">
                <td>Forma de Pago</td>
                <td>{{ $data->forma_pago }} </td>
            </tr>
            <tr class="details">
                <td>USO CFDI</td>
                <td>{{ $data->uso_cfdi }} </td>
            </tr>


            <tr class="heading">
                <td>Item</td>

                <td>Total</td>
            </tr>

            @foreach ($data->concepts as $concept)
                <tr class="item">
                    <td>{{ $concept['name'] }}</td>
                    <td>
                        {{ '$ ' . number_format($concept['quantity'] * $concept['price'] - $concept['discount'], 2, '.', ',') }}
                    </td>
                </tr>
            @endforeach


            <tr class="total">
                <td></td>

                <td>Total: $385.00</td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                Promotor:<br />
                                Equipos y Tractores del Bajio SA de CV<br />
                                Carretera Panamericana Celaya - Salamanca Km. 61<br />
                                1ra Fracc. crespo C.P. 38120 Celaya, Gto.
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
