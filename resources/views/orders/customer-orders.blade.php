@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        @if (session()->has('error'))
            <div class="alert text-white bg-danger" role="alert">
                <div class="iq-alert-text">{{ session('error') }}</div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        @endif
        @if (session()->has('success'))
            <div class="alert text-white bg-success" role="alert">
                <div class="iq-alert-text">{!! session('success') !!}</div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        @endif
        <h3 class="mb-4">Ventas del Cliente: <b> {{ $orders[0]->customer->name }}</b></h3>

        @foreach ($orders as $order)
            <div class="card mb-4">
                <div
                    class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <h5 class="mb-2 mb-md-0 text-break">
                        Factura No: {{ $order->invoice_no }} <br>
                        <small class="text-muted">Fecha: {{ $order->order_date_formatted }}</small>
                    </h5>

                    <div class="d-flex flex-column flex-md-row justify-content-between  align-items-md-center">
                        @if ($order->orderquotaDetails->count())
                            @if ($order->attachments->count())
                                <a href="#" class="btn text-white mb-1 mr-1 btn-sm" style="background: #6e40a3;"
                                    data-bs-toggle="modal" data-bs-target="#uploadAttachmentModal-{{ $order->id }}">
                                    Reemplazar Comprobante
                                </a>
                            @else
                                <a href="#" class="btn text-white mb-1 mr-1 btn-sm" style="background: #6e40a3;"
                                    data-bs-toggle="modal" data-bs-target="#uploadAttachmentModal-{{ $order->id }}">
                                    Subir Comprobante
                                </a>
                            @endif
                            <a href="{{ Route('order.downloadReceiptVenta', $order->id) }}" target="_blank"
                                class="btn btn-primary btn-sm mb-1 mr-1">
                                Descargar Comprobante
                            </a>
                        @else
                            <a href="{{ Route('order.downloadReceiptVentaNormal', $order->id) }}" target="_blank"
                                class="btn btn-primary  btn-sm mb-1 mr-1">
                                Descargar Comprobante
                            </a>
                        @endif

                        <span
                            class="btn bg-{{ $order->order_status == 'Pendiente' ? 'warning' : 'success' }} btn-sm mb-1 mr-1">
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

            <!-- Modal para subir/reemplazar archivos (Modal único por pedido) -->
            <div class="modal fade" id="uploadAttachmentModal-{{ $order->id }}" tabindex="-1"
                aria-labelledby="uploadAttachmentLabel-{{ $order->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadAttachmentLabel-{{ $order->id }}">Subir/Reemplazar
                                Comprobante</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="attachmentForm-{{ $order->id }}"
                                action="{{ route('customer.attachmentOrderCustomer', $order->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">

                                <label for="attachment-{{ $order->id }}" class="form-label">Seleccione un archivo (PDF o
                                    imagen)</label>
                                <div class="mb-2">
                                    <input type="file" id="attachment-{{ $order->id }}" name="attachment"
                                        accept="image/*,application/pdf" required>
                                </div>

                                @if ($order->attachments->count())
                                    <p class="text-muted">
                                        Actualmente hay un comprobante subido: <b style="color: red">
                                            {{ basename($order->attachments[0]->path) }}</b>. Puede reemplazarlo con uno
                                        nuevo.
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
        @endforeach
    </div>
@endsection
