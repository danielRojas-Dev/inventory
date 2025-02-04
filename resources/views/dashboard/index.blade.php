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
            <div class="col-lg-4">
                <div class="card card-transparent card-block card-stretch card-height border-none">
                    <div class="card-body p-0 mt-lg-2 mt-0">
                        <h3 class="mb-3">Hola {{ auth()->user()->name }}, ¡Buenos días!</h3>
                        <p class="mb-0 mr-4">Tu panel te proporciona vistas de los indicadores clave de rendimiento o
                            procesos de negocio.</p>
                    </div>
                </div>
            </div>

            @if (auth()->user()->can('salary.menu'))
                <div class="col-lg-12">
                    <div class="row">
                        <!-- Total Paid -->
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
                        <!-- Total Due -->
                        <div class="col-lg-4 col-md-4">
                            <div class="card card-block card-stretch card-height bg-danger">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-4 card-total-sale">
                                        <div class="icon iq-icon-box-2 bg-danger-light">
                                            <img src="../assets/images/product/2.png" class="img-fluid" alt="imagen">
                                        </div>
                                        <div>
                                            <p class="mb-2">Total Adeudado</p>
                                            {{-- <h4>$ {{ number_format($total_due, 2, ',', '.') }}</h4> --}}
                                        </div>
                                    </div>
                                    <div class="iq-progress-bar mt-2">
                                        <span class="bg-white iq-progress progress-1" data-percent="70"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Complete Orders -->
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
            @endif



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
@endsection

@section('specificpagescripts')
    <!-- JavaScript para Treeview de Tabla -->
    <script src="{{ asset('assets/js/table-treeview.js') }}"></script>
    <!-- JavaScript Personalizado para Gráficos -->
    <script src="{{ asset('assets/js/customizer.js') }}"></script>
    <!-- JavaScript Personalizado para Gráficos -->
    <script async src="{{ asset('assets/js/chart-custom.js') }}"></script>
@endsection
