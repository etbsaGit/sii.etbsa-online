<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            font-size: 10pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr td {
            padding: 0;
        }

        table tr td:last-child {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .right {
            text-align: right;
        }

        .large {
            font-size: 1.75em;
        }

        .total {
            font-weight: bold;
            color: #fb7578;
        }

        .logo-container {
            margin: 20px 0 10px 0;
        }

        .invoice-info-container {
            font-size: 0.875em;
        }

        .invoice-info-container td {
            padding: 4px 0;
        }

        .client-name {
            font-size: 1.5em;
            vertical-align: top;
        }

        .line-items-container {
            margin: 35px 0;
            font-size: 0.875em;
        }

        .line-items-container th {
            text-align: left;
            color: #999;
            border-bottom: 2px solid #ddd;
            padding: 10px 0 15px 0;
            font-size: 0.75em;
            text-transform: uppercase;
        }

        .line-items-container th:last-child {
            text-align: right;
        }

        .line-items-container td {
            padding: 15px 0;
        }

        .line-items-container tbody tr:first-child td {
            padding-top: 5px;
        }

        .line-items-container.has-bottom-border tbody tr:last-child td {
            padding-bottom: 5px;
            border-bottom: 2px solid #ddd;
        }

        .line-items-container.has-bottom-border {
            margin-bottom: 0;
        }

        .line-items-container th.heading-quantity {
            width: 50px;
        }

        .line-items-container th.heading-price {
            text-align: right;
            width: 100px;
        }

        .line-items-container th.heading-subtotal {
            width: 100px;
        }

        .payment-info {
            width: 38%;
            font-size: 0.75em;
            line-height: 1.5;
        }

        .footer {
            margin-top: 30px;
        }

        .footer-info {
            float: none;
            position: running(footer);
            margin-top: -25px;
        }

        .page-container {
            display: block;
            position: running(pageContainer);
            margin-top: -25px;
            font-size: 12px;
            text-align: right;
            color: #999;
        }

        .page-container .page::after {
            content: counter(page);
        }

        .page-container .pages::after {
            content: counter(page);
        }

        @page {
            @bottom-right {
                content: element(pageContainer);
            }

            @bottom-left {
                content: element(footer);
            }
        }
    </style>
    <style>
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

        .swiss {
            max-width: 600px;
            opacity: 0.05;
        }
    </style>
</head>


<body>
    <div class="page-container">

        Page
        <span class="page"></span>
        of
        <span class="pages"></span>
    </div>

    <div class="logo-container">
        <img src="{{ public_path() . '/img/etbsa-logo-corporativo-dark.jpeg' }}" style="width: 254px;" />
    </div>
    <table class="invoice-info-container">
        <tr>
            <td rowspan="2" class="client-name">
                Client Name
            </td>
            <td>
                Anvil Co
            </td>
        </tr>
        <tr>
            <td>
                123 Main Street
            </td>
        </tr>
        <tr>
            <td>
                Invoice Date: <strong>May 24th, 2024</strong>
            </td>
            <td>
                San Francisco CA, 94103
            </td>
        </tr>
        <tr>
            <td>
                Invoice No: <strong>12345</strong>
            </td>
            <td>
                hello@useanvil.com
            </td>
        </tr>
    </table>

    <table class="line-items-container">
        <thead>
            <tr>
                <th class="heading-description">Concepto</th>
                <th class="heading-description">Descripcion</th>
                <th class="heading-description">Clave Unidad</th>
                <th class="heading-quantity">Qty</th>
                <th class="heading-price">Precio Unitario</th>
                <th class="heading-price">Descuento</th>
                <th class="heading-subtotal">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->products as $product)
                <tr>
                    <td>{{ $product['group']['name'] }}</td>
                    <td>{{ $product['description'] }}</td>
                    <td class="right">{{ $product['unit']->name }} - {{ $product['unit']->clave }}</td>
                    <td class="right">{{ $product['qty'] }}</td>
                    <td class="right">{{ '$ ' . number_format($product['price'], 2, '.', ',') }}</td>
                    <td class="right">{{ '$ ' . number_format($product['discount'], 2, '.', ',') }}</td>
                    <td class="bold">{{ '$ ' . number_format($product['subtotal'], 2, '.', ',') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5"></td>
                <td class="right">IVA:</td>
                <td class="bold">$35.00</td>
            </tr>
            <tr>
                <td colspan="5"></td>
                <td class="right">Descuento:</td>
                <td class="bold">$35.00</td>
            </tr>
        </tbody>
    </table>


    <table class="line-items-container has-bottom-border">
        <thead>
            <tr>
                <th>Info. Facturacion</th>
                <th>Total Due</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="payment-info">
                    <div>
                        Metodo de Pago:
                        <b>{{ $data->metodopago->clave }}</b>
                        <strong>({{ $data->metodopago->description }})</strong>
                    </div>
                    <div>
                        Forma Pago: <b>{{ $data->formapago->clave }} </b>
                        <strong>({{ $data->formapago->description }})</strong>
                    </div>
                    <div>
                        USO CFDI:
                        <b>{{ $data->usocfdi->clave }} </b>
                        <strong>({{ $data->usocfdi->description }})</strong>
                    </div>
                </td>
                <td class="large total">$105.00</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        {{-- <div class="footer-thanks">
            <img src="https://github.com/anvilco/html-pdf-invoice-template/raw/main/img/heart.png" alt="heart">
            <span>Thank you!</span>
        </div> --}}
        <div class="footer-info">
            <span>hello@useanvil.com</span> |
            <span>555 444 6666</span> |
            <span>useanvil.com</span>
        </div>
    </div>

</body>

</html>
