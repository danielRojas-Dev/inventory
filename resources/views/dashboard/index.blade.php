@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid" style="background: #F7FDFF">
        <div class="row">
            <div class="col-lg-12">
                @if (session()->has('success'))
                    <div class="alert text-white bg-success" role="alert">
                        <div class="iq-alert-text">{{ session('success') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif
            </div>

            <div class="col-lg-8 mt-3 mt-lg-0">
                <div class="row">
                    {{-- Tarjeta resumen cuotas de ventas --}}
                    <div class="col-lg-6 col-md-6 mb-3">
                        <div class="card card-block card-stretch card-height bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <p class="mb-1 text-white-50">Cuotas de Ventas (mes actual)</p>
                                        <h4 class="mb-0 text-white font-weight-bold">
                                            {{ isset($salesQuotas) ? $salesQuotas->count() : 0 }}
                                        </h4>
                                    </div>
                                    <div class="icon iq-icon-box-2 bg-white text-primary rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="ri-shopping-cart-2-line"></i>
                                    </div>
                                </div>
                                @php
                                    $totalSalesQuotas = isset($salesQuotas)
                                        ? $salesQuotas->sum('estimated_payment')
                                        : 0;
                                @endphp
                                <p class="mb-1 text-white-50">Total estimado a cobrar</p>
                                <h5 class="text-white mb-3 font-weight-bold">
                                    ${{ number_format($totalSalesQuotas, 0, ',', '.') }}</h5>
                                <button type="button" class="btn btn-light btn-sm" data-toggle="modal"
                                    data-target="#modalSalesQuotas">
                                    Ver detalle
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta resumen cuotas de préstamos --}}
                    <div class="col-lg-6 col-md-6 mb-3">
                        <div class="card card-block card-stretch card-height"
                            style="background: linear-gradient(135deg, #ffb347 0%, #ffcc33 100%);">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <p class="mb-1 text-dark-50" style="color: rgba(0, 0, 0, 0.55);">Cuotas de
                                            Préstamos (mes actual)</p>
                                        <h4 class="mb-0 text-dark font-weight-bold">
                                            {{ isset($loanQuotas) ? $loanQuotas->count() : 0 }}
                                        </h4>
                                    </div>
                                    <div class="icon iq-icon-box-2 bg-white text-warning rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="ri-bank-card-line"></i>
                                    </div>
                                </div>
                                @php
                                    $totalLoanQuotas = isset($loanQuotas) ? $loanQuotas->sum('estimated_payment') : 0;
                                @endphp
                                <p class="mb-1" style="color: rgba(0, 0, 0, 0.55);">Total estimado a cobrar</p>
                                <h5 class="mb-3 font-weight-bold text-dark">
                                    ${{ number_format($totalLoanQuotas, 0, ',', '.') }}</h5>
                                <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal"
                                    data-target="#modalLoanQuotas">
                                    Ver detalle
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- @if (auth()->user()->can('salary.menu'))
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <div class="card card-block card-stretch card-height bg-primary">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-4 card-total-sale">
                                        <div class="icon iq-icon-box-2 bg-info-light">
                                            <img src="../assets/images/product/1.png" class="img-fluid" alt="imagen">
                                        </div>
                                        <div>
                                            <p class="mb-2">Total Pagado</p>
                                            <h4>$ {{ number_format($total_paid, 2, ',', '.') }}</h4>
                                        </div>
                                    </div>
                                    <div class="iq-progress-bar mt-2">
                                        <span class="bg-white  iq-progress progress-1" data-percent="85"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="card card-block card-stretch card-height bg-danger">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-4 card-total-sale">
                                        <div class="icon iq-icon-box-2 bg-danger-light">
                                            <img src="../assets/images/product/2.png" class="img-fluid" alt="imagen">
                                        </div>
                                        <div>
                                            <p class="mb-2">Total Adeudado</p>
                                        </div>
                                    </div>
                                    <div class="iq-progress-bar mt-2">
                                        <span class="bg-white iq-progress progress-1" data-percent="70"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="card card-block card-stretch card-height bg-success">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-4 card-total-sale">
                                        <div class="icon iq-icon-box-2 bg-success-light">
                                            <img src="../assets/images/product/3.png" class="img-fluid" alt="imagen">
                                        </div>
                                        <div>
                                            <p class="mb-2">Órdenes Completas</p>
                                            <h4>{{ count($complete_orders) }}</h4>
                                        </div>
                                    </div>
                                    <div class="iq-progress-bar mt-2">
                                        <span class="bg-white iq-progress progress-1" data-percent="75"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif --}}



                    {{-- <!-- Overview Chart -->
            <div class="col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Resumen</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton001"
                                    data-toggle="dropdown">
                                    Este Mes<i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none"
                                    aria-labelledby="dropdownMenuButton001">
                                    <a class="dropdown-item" href="#">Año</a>
                                    <a class="dropdown-item" href="#">Mes</a>
                                    <a class="dropdown-item" href="#">Semana</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="layout1-chart1"></div>
                    </div>
                </div>
            </div>

            <!-- Revenue Vs Cost Chart -->
            <div class="col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Ingresos vs Costos</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton002"
                                    data-toggle="dropdown">
                                    Este Mes<i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none"
                                    aria-labelledby="dropdownMenuButton002">
                                    <a class="dropdown-item" href="#">Anual</a>
                                    <a class="dropdown-item" href="#">Mensual</a>
                                    <a class="dropdown-item" href="#">Semanal</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="layout1-chart-2" style="min-height: 360px;"></div>
                    </div>
                </div>
            </div> --}}

                    <!-- Top Products -->
                    {{-- <div class="col-lg-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Productos Destacados</h4>
                        </div> --}}
                    {{-- <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton006"
                                    data-toggle="dropdown">
                                    Este Mes<i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none"
                                    aria-labelledby="dropdownMenuButton006">
                                    <a class="dropdown-item" href="#">Año</a>
                                    <a class="dropdown-item" href="#">Mes</a>
                                    <a class="dropdown-item" href="#">Semana</a>
                                </div>
                            </div>
                        </div> --}}
                    {{-- </div>
                    <div class="card-body">
                        <ul class="list-unstyled row top-product mb-0">
                            @foreach ($products as $product)
                                <li class="col-lg-3">
                                    <div class="card card-block card-stretch card-height mb-0">
                                        <div class="card-body">
                                            <div class="bg-warning-light rounded">
                                                <img src="{{ $product->product_image ? asset('storage/products/' . $product->product_image) : asset('assets/images/product/default.webp') }}"
                                                    class="style-img img-fluid m-auto p-3" alt="image">
                                            </div>
                                            <div class="style-text text-left mt-3">
                                                <h5 class="mb-1">{{ $product->product_name }}</h5>
                                                <p class="mb-0">{{ $product->product_store }} Unidad(es)</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div> --}}

                    <!-- New Products -->
                    {{-- <div class="col-lg-4">
                <div class="card card-transparent card-block card-stretch mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between p-0">
                        <div class="header-title">
                            <h4 class="card-title mb-0">Nuevos Productos</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div><a href="#" class="btn btn-primary view-btn font-size-14">Ver Todo</a></div>
                        </div>
                    </div>
                </div>
                @foreach ($new_products as $product)
                    <div class="card card-block card-stretch card-height-helf">
                        <div class="card-body card-item-right">
                            <div class="d-flex align-items-top">
                                <div class="bg-warning-light rounded">
                                    <img src="{{ $product->product_image ? asset('storage/products/' . $product->product_image) : asset('assets/images/product/default.webp') }}"
                                        class="style-img img-fluid m-auto" alt="imagen">
                                </div>
                                <div class="style-text text-left">
                                    <h5 class="mb-2">{{ $product->product_name }}</h5>
                                    <p class="mb-2">Stock: {{ $product->product_store }}</p>
                                    <p class="mb-0">Precio: ${{ $product->selling_price }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div> --}}
                </div>
                <!-- Fin de la página -->
            </div>
            {{-- Modal detalle cuotas de ventas --}}
            <div class="modal fade" id="modalSalesQuotas" tabindex="-1" role="dialog"
                aria-labelledby="modalSalesQuotasLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalSalesQuotasLabel">Detalle de cuotas de ventas - Mes actual</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @if (isset($salesQuotas) && $salesQuotas->count())
                                {{-- Vista de tabla para desktop/tablet --}}
                                <div class="d-none d-md-block">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-hover table-bordered mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Fecha pago</th>
                                                    <th>Cliente</th>
                                                    <th>Producto</th>
                                                    <th class="text-center" style="white-space: nowrap; min-width: 80px;">
                                                        Cuota</th>
                                                    <th class="text-right">Monto</th>
                                                    <th class="text-center">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($salesQuotas as $quota)
                                                    @php
                                                        $firstDetail = $quota->order->orderDetails[0] ?? null;
                                                        $productName = $firstDetail->product->product_name ?? 'N/A';
                                                    @endphp
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($quota->estimated_payment_date)->format('d/m/Y') }}
                                                        </td>
                                                        <td>{{ $quota->order->customer->name ?? 'N/A' }}</td>
                                                        <td>{{ $productName }}</td>
                                                        <td class="text-center" style="white-space: nowrap;">
                                                            {{ $quota->number_quota }} / {{ $quota->order->quotas }}</td>
                                                        <td class="text-right">
                                                            ${{ number_format($quota->estimated_payment, 0, ',', '.') }}
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="{{ route('order.quotas', $quota->order_id) }}"
                                                                class="btn btn-sm btn-primary" target="_blank">Ir a
                                                                cuotas</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Vista tipo tarjetas para móvil --}}
                                <div class="d-block d-md-none">
                                    @foreach ($salesQuotas as $quota)
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <div class="d-flex justify-content-between">
                                                    <span
                                                        class="font-weight-bold">{{ \Carbon\Carbon::parse($quota->estimated_payment_date)->format('d/m/Y') }}</span>
                                                    <span>Cuota {{ $quota->number_quota }} /
                                                        {{ $quota->order->quotas }}</span>
                                                </div>
                                                <div class="mt-1">
                                                    <small class="text-muted">Cliente</small>
                                                    <div>{{ $quota->order->customer->name ?? 'N/A' }}</div>
                                                </div>
                                                @php
                                                    $firstDetail = $quota->order->orderDetails[0] ?? null;
                                                    $productName = $firstDetail->product->product_name ?? 'N/A';
                                                @endphp
                                                <div class="mt-1">
                                                    <small class="text-muted">Producto</small>
                                                    <div>{{ $productName }}</div>
                                                </div>
                                                <div class="mt-1 d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <small class="text-muted">Monto</small>
                                                        <div class="font-weight-bold">
                                                            ${{ number_format($quota->estimated_payment, 0, ',', '.') }}
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('order.quotas', $quota->order_id) }}"
                                                        class="btn btn-sm btn-primary" target="_blank">Ir</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p>No tienes cuotas de ventas pendientes para este mes.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal detalle cuotas de préstamos --}}
            <div class="modal fade" id="modalLoanQuotas" tabindex="-1" role="dialog"
                aria-labelledby="modalLoanQuotasLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLoanQuotasLabel">Detalle de cuotas de préstamos - Mes actual
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @if (isset($loanQuotas) && $loanQuotas->count())
                                {{-- Vista de tabla para desktop/tablet --}}
                                <div class="d-none d-md-block">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-hover table-bordered mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Fecha pago</th>
                                                    <th>Cliente</th>
                                                    <th class="text-center" style="white-space: nowrap; min-width: 80px;">
                                                        Cuota</th>
                                                    <th class="text-right">Monto cuota</th>
                                                    <th class="text-right">Monto original préstamo</th>
                                                    <th class="text-center" style="white-space: nowrap; min-width: 90px;">
                                                        % Interés</th>
                                                    <th class="text-center">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($loanQuotas as $quota)
                                                    @php
                                                        $loan = $quota->loan;
                                                        $interestRate = $loan->interest_plan ?? 0;
                                                        $originalAmount =
                                                            $loan && $interestRate > 0
                                                                ? $loan->total / (1 + $interestRate / 100)
                                                                : $loan->total ?? 0;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($quota->estimated_payment_date)->format('d/m/Y') }}
                                                        </td>
                                                        <td>{{ $loan->customer->name ?? 'N/A' }}</td>
                                                        <td class="text-center"
                                                            style="white-space: nowrap; min-width: 80px;">
                                                            {{ $quota->number_quota }} / {{ $loan->quotas }}</td>
                                                        <td class="text-right">
                                                            ${{ number_format($quota->estimated_payment, 0, ',', '.') }}
                                                        </td>
                                                        <td class="text-right">
                                                            ${{ number_format($originalAmount, 0, ',', '.') }}</td>
                                                        <td class="text-center" style="white-space: nowrap;">
                                                            {{ number_format($interestRate, 0, ',', '.') }} %</td>
                                                        <td class="text-center">
                                                            <a href="{{ route('loan.quotasLoan', $quota->loan_id) }}"
                                                                class="btn btn-sm btn-primary" target="_blank">Ir a
                                                                cuotas</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Vista tipo tarjetas para móvil --}}
                                <div class="d-block d-md-none">
                                    @foreach ($loanQuotas as $quota)
                                        @php
                                            $loan = $quota->loan;
                                            $interestRate = $loan->interest_plan ?? 0;
                                            $originalAmount =
                                                $loan && $interestRate > 0
                                                    ? $loan->total / (1 + $interestRate / 100)
                                                    : $loan->total ?? 0;
                                        @endphp
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <div class="d-flex justify-content-between">
                                                    <span
                                                        class="font-weight-bold">{{ \Carbon\Carbon::parse($quota->estimated_payment_date)->format('d/m/Y') }}</span>
                                                    <span>Cuota {{ $quota->number_quota }} / {{ $loan->quotas }}</span>
                                                </div>
                                                <div class="mt-1">
                                                    <small class="text-muted">Cliente</small>
                                                    <div>{{ $loan->customer->name ?? 'N/A' }}</div>
                                                </div>
                                                <div class="mt-1">
                                                    <small class="text-muted">Monto cuota</small>
                                                    <div class="font-weight-bold">
                                                        ${{ number_format($quota->estimated_payment, 0, ',', '.') }}</div>
                                                </div>
                                                <div class="mt-1">
                                                    <small class="text-muted">Monto original préstamo</small>
                                                    <div>${{ number_format($originalAmount, 0, ',', '.') }}</div>
                                                </div>
                                                <div class="mt-1 d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <small class="text-muted">% Interés</small>
                                                        <div>{{ number_format($interestRate, 0, ',', '.') }} %</div>
                                                    </div>
                                                    <a href="{{ route('loan.quotasLoan', $quota->loan_id) }}"
                                                        class="btn btn-sm btn-primary" target="_blank">Ir</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p>No tienes cuotas de préstamos pendientes para este mes.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endsection

        @section('specificpagescripts')
            <!-- JavaScript para Treeview de Tabla -->
            <script src="{{ asset('assets/js/table-treeview.js') }}"></script>
            <!-- JavaScript Personalizado para Gráficos -->
            <script src="{{ asset('assets/js/customizer.js') }}"></script>
            <!-- JavaScript Personalizado para Gráficos -->
            <script async src="{{ asset('assets/js/chart-custom.js') }}"></script>
        @endsection
