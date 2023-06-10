<html>

<head>
    <meta charset="utf-8" />
    <title>Equipos y Tractores del Bajio</title>
    <style>
        @page {
            margin: 0.75cm 0cm;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        body {
            margin: 3.6cm 0.25cm;
        }

        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2.5cm;

            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 0.8em;
            line-height: 1em;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
            padding-top: 2rem;
            padding-left: 1rem;
            padding-right: 1rem;

        }

        header table {
            height: inherit;
            width: 100%;
            line-height: inherit;
        }

        header table td {
            vertical-align: top;
        }

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;

            /** Extra personal styles **/
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 0.8em;
            line-height: 1em;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
            background-color: #123f0283;
            color: white;
            text-align: center;
            line-height: 1rem;
            padding-bottom: 2rem;
            padding-left: 1rem;
            padding-right: 1rem;

        }

        footer table {
            height: inherit;
            width: 100%;
            line-height: inherit;
        }

        footer table td {
            vertical-align: middle;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
    <style>
        .invoice-box {
            width: 100%;
            /* max-width: 300px; */
            /* margin: auto; */
            /* padding: 30px; */
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 0.8em;
            line-height: 1em;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            /* border: 1px solid #df1414; */
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
            /* border: 1px solid #3614df; */
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 10px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            vertical-align: middle;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
            /* border: 2px solid #eeb004; */
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
            padding-top: 1cm;
            /* border: 3px solid #d5df14; */
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 400px) {
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

            header table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            header table tr.information table td {
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

        .swiss {
            max-width: 600px;
            opacity: 0.05;
        }

        .position-ref {
            position: relative;
        }
    </style>

</head>

<body>
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Helvetica, sans-serif", "normal");
                $pdf->text(520, 25, "Pagina $PAGE_NUM de $PAGE_COUNT", $font, 10);
            ');
        }
    </script>
    <header>


        <table>
            <tr class="top">
                <td style="width: 200px">
                    <img src="img/logo.png" style="width: 200px" />
                </td>
                <td colspan="4"
                    style="text-align: center; width: 370px; font-size: 9pt; line-height: 1rem; text-transform: uppercase;">
                    <b style="font-size: 12pt;">Equipos y Tractores del Bajio</b><br />
                    Carretera Panamericana Celaya - Salamanca Km. 61<br />
                    1ra Fracc. crespo C.P. 38120 Celaya, Gto.<br />
                    Tel. 461-614-2323, 461-614-2324, 461-614-2325<br />
                    <b>R.F.C.: ETB-860812-I23</b>
                </td>

                <td style="text-align: right; width: 170px; line-height: 1.2rem;">
                    <b>ORDEN DE COMPRA</b><br />
                    # {{ $item->id }}<br />
                   {{$item->authorization_date}}<br />
                    {{ today()}}
                </td>
            </tr>
        </table>
        <div style="text-align: center; height: 50%;">
            <img class="swiss" style="margin-top: 280px;" src="img/logo-dark.png">
        </div>

    </header>
    <footer>
        <!-- <div>
            El Numero de Orden de Compra debe aparecer siempre en la Factura, Etiquetas y Notas de Envio
            correspondientes, inclusive en los embarques a terceros. La Factura debera contener la misma
            infomacion (linea por linea) que la mostrada en esta Orden de Compra, Para entregas contactar al
            Requisitor.
        </div> -->
        <table>
            <tr class="top">
                <td style="width: 100px;text-align: center;">
                    <img src="img/logo.png" style="width: 120px" />
                </td>
                <td colspan="4"
                    style="text-align: left; width: 370px; font-size: 0.7rem; line-height: 1rem; font-style: italic;color: #ddd;">
                    Se espera que el proveedor organice su negocio con ETBSA, en consonancia con el Codigo de Conducta
                    de Proveedores de ETBSA (URL), ETBSA tenda derecho de auditar el desempeño de sostenibilidad del
                    proveedor, ya sea por una situacion, ejecutada directamente por ETBSA o por un tercero.
                </td>

                <td style="text-align: center; width: 170px;">
                    <img src="img\QR_VoBo.jpg" style="width: 90px ; height: 90px;" />
                </td>
            </tr>
        </table>
    </footer>

    <main>
        <div class="invoice-box">
            <table cellpadding="0" cellspacing="0">
                <tr class="information">
                    <td colspan="8">
                        <table>
                            <tr>
                                <td colspan="4">
                                    Sucursal embarque: {{ $item->ship->title }} <br>
                                    Direccion:
                                    {{ $item->ship->address ?? 'Carretera Celaya-Salamanca Km. 61 Primera Frac. de
                                    Crespo 38120' }}<br>
                                    Comprador: {{ $item->elaborated->name }}<br>
                                    Email:{{ $item->elaborated->email }}<br>
                                </td>

                                <td colspan="4">
                                    Num. Proveedor: <b>{{ $item->supplier->code_equip }}</b><br />
                                    {{ $item->supplier->business_name }}<br />
                                    R.F.C.: <b>{{ $item->supplier->rfc }}</b><br />
                                    {{ $item->supplier->email ?? '' }}
                                </td>
                            </tr>
                            <!-- <tr>
                                <td colspan="6"></td>
                                <td colspan="2">
                                    Datos Bancarios<br />
                                    Cuenta: 21312-321312-3213-21<br />
                                    Banco: BBVA
                                </td>
                            </tr> -->
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
                                    {{ $item->payment_condition < 8 ? 'CON PREFACTURA' : $item->payment_condition . ' '
                                        . 'Dias habiles despues de recibir Factura' }}
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
                <tr class="information" style="font-size: 0.7em; line-height: 1em;text-transform: uppercase;">
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
                                    Atencion Proveedor si tu Factura NO Coincide con los Requisitos Fiscales de esta
                                    Orden
                                    compra NO sera programada para Pago<br />
                                    *Poner en copia al/los correos:sanchezblanca@etbsa.com.mx
                                </td>
                            </tr>
                            <tr style="padding-bottom: 0px;">
                                <td style="padding-bottom: 0px;">
                                    El Numero de Orden de Compra debe aparecer siempre en la Factura, Etiquetas y Notas
                                    de
                                    Envio correspondientes, inclusive en los embarques a terceros. La Factura debera
                                    contener la misma infomacion (linea por linea) que la mostrada en esta Orden de
                                    Compra,
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
                                    </span>
                                    <br />
                                    @endforeach
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

    </main>


</body>

</html>