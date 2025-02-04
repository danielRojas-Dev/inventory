@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <h3 class="mb-4">Ventas del Cliente: <b> {{ $orders[0]->customer->name }}</b></h3>

        @foreach ($orders as $order)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h5>Factura No: {{ $order->invoice_no }} | Fecha: {{ $order->order_date }}</h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary"> <a
                                href="{{ Route('order.downloadReceipt', $order->id) }}"target='_blank' style="color: white"
                                class=" btn-sm me-2">
                                Descargar Comprobante
                            </a>
                        </span>
                        <span class="badge bg-{{ $order->order_status == 'Pendiente' ? 'warning' : 'success' }}">
                            {{ $order->order_status }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    @if ($order->orderquotaDetails->count())
                        <h6 class="card-title {{ $order->cantidadDeudas > 0 ? 'text-danger' : 'text-success' }}">
                            Estado:
                            {{ $order->cantidadDeudas > 0 ? 'Hay cuotas vencidas' : 'Cliente al día' }}
                        </h6>
                        <div class="d-flex align-items-center mb-3">

                            <h6 class="mb-0 me-2">Tiene cuotas Asociadas:</h6>
                            <span class="badge bg-success">
                                <a href="{{ Route('order.quotas', $order->id) }}" style="color:white" class="btn-sm">Pagar
                                    Cuotas</a>
                            </span>


                        </div>
                    @endif
                </div>

            </div>
        @endforeach
    </div>
@endsection
