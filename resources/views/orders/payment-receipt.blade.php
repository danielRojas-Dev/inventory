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
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .section {
            margin-top: 10px;
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
                <small>N°: <b>{{ $order->invoice_no }}</b></small>
                <br>
                <small>Fecha: <b>{{ date('d-m-Y', strtotime($order->order_date)) }}</b></small>
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


        <div class="header"></div>
        <div class="info">
            <div><strong>Cliente:</strong> {{ $cliente->name }}</div>
            <div><strong>Documento N°:</strong> 43712325</div>
        </div>
        <div class="info">
            <div><strong>Domicilio:</strong> {{ $cliente->address }}</div>
            <div><strong>Teléfono:</strong> {{ $cliente->phone }}</div>
        </div>
        <div class="info">
            <div><strong>Barrio:</strong> Republica Argentina</div>
            <div><strong>Localidad:</strong> {{ $cliente->city }}</div>
        </div>
        <div class="section">
            <table style="width: 100%; border-collapse: collapse; text-align: center;">
                <tr>
                    <th style="border: 1px solid black; padding: 1px; text-align: center;">Cantidad</th>
                    <th style="border: 1px solid black; padding: 1px; text-align: center;">Descripción</th>
                    <th style="border: 1px solid black; padding: 1px; text-align: center;">Plan de Cuotas</th>
                    <th style="border: 1px solid black; padding: 1px; text-align: center;">Monto de Cuotas</th>
                </tr>
                @foreach ($details as $detail)
                    <tr>
                        <td style="border: 1px solid black; padding: 1px; text-align: center;">{{ $detail->quantity }}
                        </td>
                        <td style="border: 1px solid black; padding: 1px; text-align: center;">
                            {{ $detail->product->product_name }}</td>
                        <td style="border: 1px solid black; padding: 1px; text-align: center;">{{ $order->quotas }}</td>
                        <td style="border: 1px solid black; padding: 1px; text-align: center;">$
                            {{ number_format($valorCuota, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>




        <?php
        $fecha = $estimatedPaymentDate;
        [$anio, $mes, $dia] = explode('-', $fecha);
        ?>

        <?php
        $fecha = $estimatedPaymentDate;
        [$anio, $mes, $dia] = explode('-', $fecha);
        ?>

        <div class="important">
            <p><strong>Nota Importante:</strong> Por falta de cualquier mensualidad nos reservamos el derecho de
                cobrar
                el total del saldo de la venta.
                <br> En caso de vencimiento de las cuotas, se aplicará un interés del
                <b>1.5%</b> diario sobre el valor de la cuota hasta su cancelación. <br>Sírvase avisar el cambio de
                domicilio.
            </p>

            <p><strong>Compromiso de pago:</strong> Conste por la presente que me comprometo a abonar la Mercadería
                detallada en <b>{{ $order->quotas }}</b> cuotas fijas de $
                <b>{{ number_format($valorCuota, 0, ',', '.') }}</b> a
                partir del día <b>{{ $dia }}</b> del <b>{{ $mes }}</b>
                del año <b>{{ $anio }}</b>.
            </p>
            <p class="firma"><strong>Firma:</strong> _______________</p>
        </div>


    </div>
</body>

</html>
