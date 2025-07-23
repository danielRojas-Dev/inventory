@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @if (session()->has('success'))
                    <div class="alert text-white bg-success" role="alert">
                        <div class="iq-alert-text">{!! session('success') !!}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert text-white bg-danger" role="alert">
                        <div class="iq-alert-text">{{ session('error') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif
            </div>

            <div class="col-lg-12">
                <div class="card card-block">
                    <div class="card-header d-flex justify-content-between bg-primary">
                        <div class="iq-header-title">
                            <h4 class="card-title mb-0 text-white">Presupuesto {{ $budget->budget_no }}</h4>
                        </div>

                        <div class="invoice-btn d-flex">
                            <a href="{{ route('budgets.print', $budget->id) }}" class="btn btn-secondary mr-2"
                                target="_blank">Imprimir</a>

                            @if ($budget->can_convert)
                                <form method="POST" action="{{ route('budgets.convert', $budget->id) }}"
                                    style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success mr-2"
                                        onclick="return confirm('¿Está seguro de convertir este presupuesto a venta?')">
                                        Convertir a Venta
                                    </button>
                                </form>
                            @endif

                            @if ($budget->budget_status == 'Pendiente')
                                <form method="POST" action="{{ route('budgets.cancel', $budget->id) }}"
                                    style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger mr-2"
                                        onclick="return confirm('¿Está seguro de cancelar este presupuesto?')">
                                        Cancelar
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('budgets.index') }}" class="btn btn-light">Volver</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="mb-3">Información del Presupuesto</h4>
                                            <p><strong>Número:</strong> {{ $budget->budget_no }}</p>
                                            <p><strong>Fecha:</strong> {{ $budget->budget_date_formatted }}</p>
                                            <p><strong>Estado:</strong>
                                                @if ($budget->budget_status == 'Pendiente')
                                                    <span class="badge badge-warning">{{ $budget->budget_status }}</span>
                                                @elseif($budget->budget_status == 'Convertido')
                                                    <span class="badge badge-success">{{ $budget->budget_status }}</span>
                                                @elseif($budget->budget_status == 'Vencido')
                                                    <span class="badge badge-danger">{{ $budget->budget_status }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $budget->budget_status }}</span>
                                                @endif
                                            </p>
                                            <p><strong>Válido hasta:</strong>
                                                <span class="@if ($budget->is_expired) text-danger @endif">
                                                    {{ $budget->valid_until_formatted }}
                                                </span>
                                            </p>
                                            @if ($budget->converted_order_id)
                                                <p><strong>Convertido a venta:</strong> #{{ $budget->converted_order_id }}
                                                </p>
                                                <p><strong>Fecha de conversión:</strong>
                                                    {{ $budget->converted_at->format('d/m/Y H:i') }}</p>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="mb-3">Información del Cliente</h4>
                                            <p><strong>Nombre:</strong> {{ $budget->customer->name }}</p>
                                            <p><strong>DNI:</strong> {{ $budget->customer->dni }}</p>
                                            <p><strong>Teléfono:</strong> {{ $budget->customer->phone }}</p>
                                            <p><strong>Dirección:</strong> {{ $budget->customer->address }}</p>
                                            <p><strong>Ciudad:</strong> {{ $budget->customer->city }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($budget->notes)
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-4">
                                        <h4 class="mb-3">Notas</h4>
                                        <p class="text-muted">{{ $budget->notes }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-sm-12">
                                <h4 class="mb-3">Productos</h4>
                                <div class="table-responsive-sm">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-center" scope="col">#</th>
                                                <th scope="col">Producto</th>
                                                <th scope="col">Código</th>
                                                <th class="text-center" scope="col">Precio Unitario</th>
                                                <th class="text-center" scope="col">Cantidad</th>
                                                <th class="text-center" scope="col">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($budget->budgetDetails as $detail)
                                                <tr>
                                                    <th class="text-center" scope="row">{{ $loop->iteration }}</th>
                                                    <td>{{ $detail->product->product_name }}</td>
                                                    <td>{{ $detail->product->product_code }}</td>
                                                    <td class="text-center">
                                                        ${{ number_format($detail->unitcost, 0, ',', '.') }}</td>
                                                    <td class="text-center">{{ $detail->quantity }}</td>
                                                    <td class="text-center">
                                                        ${{ number_format($detail->total, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4 col-sm-5 ml-md-auto">
                                <table class="table table-clear">
                                    <tbody>
                                        <tr>
                                            <td class="left"><strong>Total Productos</strong></td>
                                            <td class="right">{{ $budget->total_products }}</td>
                                        </tr>
                                        <tr>
                                            <td class="left"><strong>Método de Pago</strong></td>
                                            <td class="right">{{ $budget->payment_method }}</td>
                                        </tr>
                                        @if ($budget->quotas)
                                            <tr>
                                                <td class="left"><strong>Número de Cuotas</strong></td>
                                                <td class="right">{{ $budget->quotas }}</td>
                                            </tr>
                                        @endif
                                        @if ($budget->interest_plan > 0)
                                            <tr>
                                                <td class="left"><strong>Interés (%)</strong></td>
                                                <td class="right">{{ $budget->interest_plan }}%</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="left"><strong>Total</strong></td>
                                            <td class="right">
                                                <strong>${{ number_format($budget->total, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        @if ($budget->quotas && $budget->total)
                                            <tr>
                                                <td class="left"><strong>Cuota Mensual</strong></td>
                                                <td class="right">
                                                    <strong>${{ number_format($budget->total / $budget->quotas, 0, ',', '.') }}</strong>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
