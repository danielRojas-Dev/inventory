@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <h3 class="mb-4">Ventas del Cliente: <b> {{ $orders[0]->customer->name }}</b></h3>

        @foreach ($orders as $order)
            <div class="card mb-4">
                <div
                    class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <h5 class="mb-2 mb-md-0 text-break">
                        Factura No: {{ $order->invoice_no }} <br>
                        <small class="text-muted">Fecha: {{ $order->order_date }}</small>
                    </h5>

                    <div class="d-flex flex-column flex-md-row justify-content-between  align-items-md-center">
                        @if ($order->orderquotaDetails->count())
                            @if ($order->attachments->count())
                                <a href="#" class="badge text-white mb-1 mr-1" style="background: #6e40a3;"
                                    data-bs-toggle="modal" data-bs-target="#uploadAttachmentModal">
                                    Reemplazar Comprobante
                                </a>
                                <a href="{{ Route('order.downloadReceiptVenta', $order->id) }}" target="_blank"
                                    class="badge bg-primary mb-1 mr-1">
                                    Descargar Comprobante
                                </a>
                            @else
                                <a href="#" class="badge text-white mb-1 mr-1" style="background: #6e40a3;"
                                    data-bs-toggle="modal" data-bs-target="#uploadAttachmentModal">
                                    Subir Comprobante
                                </a>
                                <a href="{{ Route('order.downloadReceiptVenta', $order->id) }}" target="_blank"
                                    class="badge bg-primary mb-1 mr-1">
                                    Descargar Comprobante
                                </a>
                            @endif
                        @else
                            <a href="{{ Route('order.downloadReceiptVentaNormal', $order->id) }}" target="_blank"
                                class="badge bg-primary mb-1 mr-1">
                                Descargar Comprobante
                            </a>
                        @endif

                        <span class="badge bg-{{ $order->order_status == 'Pendiente' ? 'warning' : 'success' }} mb-1 mr-1">
                            {{ $order->order_status }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    @if ($order->orderquotaDetails->count())
                        <h6 class="card-title {{ $order->cantidadDeudas > 0 ? 'text-danger' : 'text-success' }}">
                            Estado: {{ $order->cantidadDeudas > 0 ? 'Hay cuotas vencidas' : 'Cliente al día' }}
                        </h6>

                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <h6 class="mb-0">Tiene cuotas Asociadas:</h6>
                            <a href="{{ Route('order.quotas', $order->id) }}" class="btn btn-success btn-sm">
                                Pagar Cuotas
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal para subir/reemplazar archivos -->
    <div class="modal fade" id="uploadAttachmentModal" tabindex="-1" aria-labelledby="uploadAttachmentLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadAttachmentLabel">Subir/Reemplazar Comprobante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="attachmentForm" action="{{ route('customer.attachmentOrderCustomer', $order->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">

                        <label for="attachment" class="form-label">Seleccione un archivo (PDF o imagen)</label>
                        <div class="mb-2">
                            <input type="file" id="attachment" name="attachment" accept="image/*,application/pdf"
                                required>
                        </div>

                        @if ($order->attachments->count())
                            <p class="text-muted">
                                Actualmente hay un comprobante subido: <b style="color: red">
                                    {{ basename($order->attachments[0]->path) }}</b>. Puede reemplazarlo con uno nuevo.
                            </p>
                        @endif

                        <button type="submit" class="btn btn-primary w-100" style="background: #6e40a3;">
                            Subir / Reemplazar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
