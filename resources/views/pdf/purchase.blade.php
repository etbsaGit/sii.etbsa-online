<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Favicon -->
    {{-- <link rel="icon" href="./images/favicon.png" type="image/x-icon" /> --}}
    <!-- Invoice styling -->
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {
            font-weight: 300;
            margin-top: 10px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            max-width: 100%;
            margin: auto;
            padding: 20px;
            border: 0px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 10pt;
            line-height: normal;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: rgb(27, 27, 27);
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 16px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 24px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 18px;
            font-size: 8pt;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            white-space: nowrap;
            text-align: left;
        }

        .invoice-box table tr.details td {
            padding-bottom: 8px;
            font-size: 10pt;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
            font-size: 10pt
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
    </style>
    {{-- Header Footer styiling --}}
    <style type="text/css">
        body {
            font-family: sans-serif;

        }

        @page {
            margin: 30px 10px;
        }

        header {
            position: fixed;
            left: 0px;
            top: -120px;
            right: 0px;
            height: 40vh;
            /* background-color: #ddd; */
            text-align: center;
            justify-content: center;
        }

        header h1 {
            /* margin: 5px 0; */
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            font-size: 18pt;
            color: #000;
        }

        header .title {
            font-size: 18px;
            font-weight: 300;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #333;
        }

        header .subtitle {
            font-size: 18px;
            font-weight: 300;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: rgb(19, 18, 18);
        }

        header h2 {
            /* margin: 0 0 10px 0; */
            font-weight: 200;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        footer {
            position: fixed;
            left: 0px;
            bottom: -50px;
            right: 0px;
            height: 40px;
            border-bottom: 2px solid #ddd;
        }

        footer .page:after {
            content: counter(page);
        }

        footer table {
            width: 100%;
        }

        footer p {
            text-align: right;
        }

        footer .izq {
            text-align: left;
        }

        .currSign:before {
            content: '$';
        }

        .swiss {
            max-width: 600px;
            opacity: 0.07;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        .position-ref {
            position: relative;
        }
    </style>
</head>

<body>

    <header>
        {{-- <div class="title">EQUIPOS Y TRACTORES DEL BAJIO , S.A. DE C.V</div>
        <div>Carretera Panamericana Celaya - Salamanca Km. 61</div>
        <div>1ra Fracc. crespo C.P. 38120 Celaya, Gto.</div>
        <div>Tel. 461-614-23-23, 461-614-23-24, 461-614-23-25</div>
        <div class="subtitle">R.F.C.: ETB-860812-I23</div> --}}
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md align-center" style="margin-top: 400px">
                    <img class="swiss" src="{{ url('img/etbsa-logo-corporativo-dark.jpeg') }}">
                    {{-- <img class="swiss" src="{{ url('img/etbsa-logo-construccion.png') }}"> --}}
                </div>
            </div>
        </div>
    </header>
    <footer>
        <table>
            <tr>
                <td>
                    <p class="izq">
                        EQUIPOS Y TRACTORES DEL BAJIO SA DE CV
                    </p>
                </td>
                <td>
                    <p class="page">
                        Página
                    </p>
                </td>
            </tr>
        </table>
    </footer>
    <div class="invoice-box" style="text-transform:uppercase;">
        <table cellpadding="0" cellspacing="0">
            {{-- <tr class="top"> --}}
            <tr>
                <td colspan="6">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="{{ public_path() . '/img/etbsa-logo-corporativo-dark.jpeg' }}"
                                    style="width: 100%; max-width: 189px; height: 100px;" />
                            </td>
                            {{-- <td style="max-width: 100px">
                                Sucursal ETBSA : {{ $data->sucursal->title }}<br />
                                Domicilio: {{ $data->sucursal->address }}<br />
                            </td> --}}
                            <td>
                                ORDEN DE COMPRA: # {{ str_pad($data->id, 5, '0', STR_PAD_LEFT) }}<br />
                                Fecha: {{ $data->authorization_date }}<br />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="6">
                    <table>
                        <tr>
                            <td colspan="6">
                                Equipos y Tractores del Bajio SA de CV<br />
                                Carr. Panamericana Celaya - Salamanca Km. 61<br />
                                1ra Fracc. crespo C.P. 38120 Celaya, Gto.<br />
                                R.F.C.: ETB-860812-I23
                            </td>

                            <td colspan="6">
                                Proveedor:<br />
                                {{ $data->supplier->business_name }}<br />
                                {{ $data->supplier->rfc }}<br />
                                {{ $data->supplier->email }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Datos Factura</td>
                <td colspan="5"></td>
            </tr>

            <tr class="details">
                <td>Metodo de Pago</td>
                <td colspan="4">({{ $data->metodopago->description }})</td>
                <td style="text-align: center">{{ $data->metodopago->clave }} </td>
            </tr>
            <tr class="details">
                <td>USO DE CFDI</td>
                <td colspan="4">({{ $data->usocfdi->description }})</td>
                <td style="text-align: center">{{ $data->usocfdi->clave }} </td>
            </tr>
            <tr class="details">
                <td>Forma de Pago</td>
                <td colspan="4">({{ $data->formapago->description }})</td>
                <td style="text-align: center">{{ $data->formapago->clave }} </td>
            </tr>

            <tr class="heading">
                {{-- <td>cargo</td> --}}
                <td>Producto</td>
                <td>Cantidad</td>
                <td>unidad</td>
                <td>precio unit.</td>
                <td>Descuento</td>
                <td>importe</td>

            </tr>
            @foreach ($data->concepts as $concept)
                <tr class="item" style="font-size: 8pt;">
                    {{-- <td>{{ ($concept['department'] ?? '') . ' ' . ($concept['user'] ?? '') }} --}}
                    {{-- </td> --}}
                    <td style="width: 300px; ">
                        <div>
                            <b>{{ $concept['name'] }}</b>
                        </div>
                        <div>
                            {{ $concept['description'] }}
                        </div>
                    </td>
                    <td style="text-align: left;">
                        {{ $concept['quantity'] }}
                    </td>
                    <td style="text-align: left;">
                        {{ $concept['unit'] }}
                    </td>
                    <td style="text-align: right">
                        {{ '$ ' . number_format($concept['price'], 2, '.', ',') }}
                    </td>
                    <td style="text-align: right">
                        {{ '$ ' . number_format($concept['discount'] ?? 0, 2, '.', ',') . '' }}
                    </td>
                    <td style="text-align: right">
                        {{ '$ ' . number_format($concept['quantity'] * $concept['price'] - $concept['discount'], 2, '.', ',') }}
                    </td>
                </tr>
            @endforeach


            <tr class="item">
                <td colspan="6"></td>
            </tr>
            <tr class="total" style="text-align: right">
                <td colspan="4">Subtotal: </td>
                <td colspan="2" style="text-align: right">{{ '$ ' . number_format($data->subtotal, 2, '.', ',') }}
                </td>
            </tr>
            <tr class="total" style="text-align: right">
                <td colspan="4">Descuento: </td>
                <td colspan="2">{{ '$ ' . number_format($data->discount, 2, '.', ',') }}</td>
            </tr>
            <tr class="total" style="text-align: right">
                <td colspan="4">IVA: </td>
                <td colspan="2">{{ '$ ' . number_format($data->tax, 2, '.', ',') }}</td>
            </tr>
            <tr class="total" style="text-align: right">
                <td colspan="4">Total: </td>
                <td colspan="2">{{ '$ ' . number_format($data->total, 2, '.', ',') }}</td>
            </tr>
        </table>
        <table>
            {{-- <tr class="heading">
                <td>Observacion</td>
                <td colspan="5"></td>
            </tr>
            <tr class="details">
                <td colspan="6" style="text-align: left">{{ $data->observation }} </td>
            </tr> --}}
            <tr class="heading">
                <td>Nota</td>
                <td colspan="5"></td>
            </tr>
            <tr class="details">
                <td colspan="6" style="text-align: left">{{ $data->note }} </td>
            </tr>
            <tr class="details">
                <td colspan="6" style="text-align: left; font-size: 8pt;">
                    Atencion Proveedor si tu Factura NO Coincide con los Requisitos Fiscales>de esta Orden compra <br>
                    NO sera programada para Pago<br><br>
                    *Poner en copia al/los correos:<br>
                    @foreach ($data->chargeAgency as $agency)
                        {{ $agency->email }}<br>
                    @endforeach
                </td>
            </tr>
        </table>
    </div>
    {{-- <p style="page-break-before: always;">
        Podemos romper la página en cualquier momento...</p>
    </p>
    <p>
        Praesent pharetra enim sit amet...
    </p> --}}
</body>

</html>
