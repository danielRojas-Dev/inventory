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

        .resumen {
            margin-top: 10px;
            text-align: left;
            font-size: 18px;
        }
    </style>
</head>

<body>
    <div class="nota-pedido">
        <div class="fecha">
            <div class="content-fecha" style="padding-top: 15px;">
                <small>N°: <b>{{ $order->invoice_no }}</b></small>
                <br>
                <small>Fecha: <b>{{ $order->order_date_receipt_formatted }}</b></small>
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

        <div>COMPROBANTE DE VENTA</div>

        <div class="header"></div>
        <!-- Columna izquierda -->
        <table style="width: 100%;" class="info">
            <tr>
                <!-- Primera columna -->
                <td style="text-align: left; vertical-align: top; width: 33%;">
                    <strong>Cliente:</strong> {{ $cliente->name }}<br>
                    <strong>Documento N°:</strong> {{ $cliente->dni }}<br>
                    <strong>Teléfono:</strong> {{ $cliente->phone }}

                </td>

                <!-- Segunda columna -->
                <td style="text-align: left; vertical-align: top; width: 33%;">
                    <strong>Domicilio:</strong> {{ $cliente->address }} <br>
                    <strong>Localidad:</strong> {{ $cliente->city }}
                </td>
            </tr>
        </table>




        <div class="section">
            <table style="width: 100%; border-collapse: collapse; text-align: center;">
                <tr>
                    <th style="border: 1px solid black; padding: 1px; text-align: center;">Descripción</th>
                    <th style="border: 1px solid black; padding: 1px; text-align: center;">Cantidad</th>
                    <th style="border: 1px solid black; padding: 1px; text-align: center;">Precio Unico</th>
                    <th style="border: 1px solid black; padding: 1px; text-align: center;">Precio Total</th>
                </tr>
                @foreach ($details as $detail)
                    <tr>
                        <td style="border: 1px solid black; padding: 1px; text-align: center;">
                            {{ $detail->product->product_name }}</td>
                        <td style="border: 1px solid black; padding: 1px; text-align: center;">{{ $detail->quantity }}
                        </td>
                        <td style="border: 1px solid black; padding: 1px; text-align: center;">$
                            {{ number_format($detail->unitcost, 0, ',', '.') }}
                        </td>
                        <td style="border: 1px solid black; padding: 1px; text-align: center;">$
                            {{ number_format($detail->quantity * $detail->unitcost, 0, ',', '.') }}
                        </td>

                    </tr>
                @endforeach
            </table>
        </div>
        <div class="resumen">
            <div><strong>Total:</strong> <b>$ {{ number_format($order->pay, 0, ',', '.') }}</b></div>
        </div>

    </div>
</body>

</html>
