@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <!-- Mostrar alerta solo si hay error -->
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
                        <div class="iq-alert-text">{{ session('success') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif

                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <h4 class="mb-3">Cuotas de la Venta #{{ $order->invoice_no }}</h4>
                    <a href="{{ route('customer.customerDetails', $order->customer->id) }}"
                        class="btn btn-secondary">Volver</a>
                </div>
                <p class="text-center mt-3">
                    <b style="color:green;">DD-MM-AAAA</b> - <span>Cuota al Día</span> |
                    <b style="color:red;">DD-MM-AAAA</b> - <span>Cuota Vencida</span> |
                    <b style="color:orange;">DD-MM-AAAA</b> - <span>Cuota Pagada Vencida</span>
                </p>

            </div>

            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table id="table" class=" display nowrap" style="width:100%">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th class="text-center">No</th>
                                <th class="text-center">Estimado a Pagar</th>
                                <th class="text-center">Vencimiento - Pago</th>
                                <th class="text-center">Interés por Vencimiento</th>
                                <th class="text-center">Pago</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @forelse ($quotas as $index => $quota)
                                <tr>
                                    <td class="text-center">{{ $quota->number_quota }}</td>
                                    <td class="text-center">$ {{ number_format($quota->estimated_payment) }}</td>
                                    <td class="text-center">
                                        @php
                                            $estimatedDate = \Carbon\Carbon::parse($quota->estimated_payment_date);
                                            $pago = $quota->payment_date
                                                ? \Carbon\Carbon::parse($quota->payment_date)->format('d/m/Y')
                                                : 'No Pago';
                                            $statusQuota = '';

                                            // Si tiene interés por vencimiento, el color será warning
                                            if ($quota->interest_due) {
                                                $statusQuota = 'text-warning';
                                            } elseif (
                                                $estimatedDate->greaterThan(\Carbon\Carbon::now()) < date('Y-m-d') &&
                                                $quota->status_payment == 'Pendiente'
                                            ) {
                                                $statusQuota = 'text-danger';
                                            } else {
                                                $statusQuota = 'text-success';
                                            }
                                        @endphp

                                        <b class="{{ $statusQuota }}">{{ $estimatedDate->format('d/m/Y') }}</b> |
                                        <b>{{ $pago }}</b>
                                    </td>

                                    <td class="text-center">{{ $quota->interest_due ? '%' : '' }}
                                        {{ $quota->interest_due ?? '-' }}</td>
                                    <td class="text-center">
                                        {{ $quota->total_payment ? '$ ' : '' }}{{ $quota->total_payment ? number_format($quota->total_payment) : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($quota->status_payment != 'Pendiente')
                                            <span class="badge bg-success">Pagada</span>
                                        @else
                                            <span class="badge bg-warning">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($quota->status_payment != 'Pendiente')
                                            <span class="badge bg-success">
                                                <a href="{{ Route('order.downloadReceiptQuota', $quota->id) }}"
                                                    target='_blank' class="text-white">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </span>
                                        @else
                                            <a href="{{ Route('quota.paymentQuota', $quota->id) }}"
                                                class="btn btn-primary btn-sm">
                                                Pagar
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-danger">No hay cuotas registradas para esta
                                        orden.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
