<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota de Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .nota-pedido {
            width: 100%;
            border: 2px solid black;
            padding: 20px;
            padding-right: 20px
        }

        .header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .info {
            font-size: 13px;
            margin-bottom: 3px;
        }

        .section {
            margin-top: 10px;
            font-size: 13px;

        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }

        .important {
            font-size: 12px;
            margin-top: 10px;
        }

        .fecha {
            font-size: 15px;
            float: right;
            padding-right: 10px;
        }

        .content-fecha {
            text-align: left;
            padding-top: 15px;
        }

        .firma {
            font-size: 13px;
            text-align: right;
            padding-right: 20px;
            padding-top: 30px;
        }
    </style>
</head>

<body>
    <div class="nota-pedido">
        <div class="fecha">
            <div class="content-fecha" style="padding-top: 15px;">
                <small>N°: <b>{{ $quota->invoice_no }}</b></small>
                <br>
                <small>Fecha: <b>{{ date('d-m-Y', strtotime($quota->payment_date)) }}</b></small>
                <br>
                <small>Tel: <b>3704-590488</b></small>
            </div>
        </div>
        <div style="display: inline-block; width: 30%; text-align: left; margin-top: 30px;">
            <div class="logo">
                {!! $htmlLogo !!}
            </div>
        </div>

        <div style="display: inline-block; width: 35%; ">
            <div class="title">
                {!! $htmlTitle !!}
            </div>
        </div>

        <div>COMPROBANTE DE PAGO</div>
        @if ($quota->cancelated)
            <div style="text-align: center; margin-top: 10px;">
                {!! $htmlCancelado !!}
            </div>
        @endif

        <div class="header"></div>
        <table style="width: 100%;" class="info">
            <tr>
                <td style="text-align: left; vertical-align: top; width: 50%;">
                    <strong>Cliente:</strong> {{ $cliente->name }}<br>
                    <strong>Documento N°:</strong> {{ $cliente->dni }}<br>
                    <strong>Teléfono:</strong> {{ $cliente->phone }}
                </td>
                <td style="text-align: left; vertical-align: top; width: 50%;">
                    <strong>Domicilio:</strong> {{ $cliente->address }} <br>
                    <strong>Localidad:</strong> {{ $cliente->city }}
                </td>
            </tr>

        </table>
        <table style="width: 100%;" class="info">
            <tr>
                <td style="width: 25%;">
                    <strong>Forma de Pago:</strong> {{ $quota->payment_method }}
                </td>
                <td style="width: 25%;">
                    <strong>Cuota N° :</strong> {{ $quota->number_quota }}
                </td>
                <td style="width: 25%;">
                    <strong>Plan :</strong> {{ $loan->quotas }} <strong>cuotas</strong>
                </td>
                <td style="width: 25%;">
                    <strong>Importe Total :</strong> ${{ number_format($quota->total_payment, 0, ',', '.') }}

                </td>
            </tr>

        </table>

        <div class="section">
            <table style="width: 100%; border-collapse: collapse; text-align: center;">
                <tr>
                    <th style="border: 1px solid black; padding: 1px; text-align: center;">Descripción</th>
                    <th style="border: 1px solid black; padding: 1px; text-align: center;">Cuotas Pagadas</th>
                </tr>
                <tr>
                    </td>
                    <td style="border: 1px solid black; padding: 1px; text-align: center;">
                        Prestamo</td>
                    <td style="border: 1px solid black; padding: 1px; text-align: center;">
                        {{ $quota->number_quota }}/{{ $loan->quotas }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
