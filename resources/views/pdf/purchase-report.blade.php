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
            font-size: 8pt;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
            font-size: 8pt
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
            font-size: 8pt;
            line-height: inherit;
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
            opacity: 0.03;
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

        table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <header>
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
    <pre>{{ $data }}</pre>
</body>

</html>
