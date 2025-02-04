@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">

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
                <div>
                    <h4 class="mb-3">Punto de Venta</h4>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 mb-3">
                <table class="table">
                    <thead>
                        <tr class="ligth">
                            <th scope="col">Nombre</th>
                            <th scope="col">Cantidad</th>
                            <th scope="col">Precio</th>
                            <th scope="col">Subtotal</th>
                            <th scope="col">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productItem as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td style="min-width: 140px;">
                                    <form action="{{ route('pos.updateCart', $item->rowId) }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="qty" required
                                                value="{{ old('qty', $item->qty) }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-success border-none"
                                                    data-toggle="tooltip" data-placement="top" title=""
                                                    data-original-title="Enviar"><i class="fas fa-check"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                <td> $ {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>$ {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('pos.deleteCart', $item->rowId) }}" class="btn btn-danger border-none"
                                        data-toggle="tooltip" data-placement="top" title=""
                                        data-original-title="Eliminar"><i class="fa-solid fa-trash mr-0"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="container text-center">
                    <div class="row">
                        <div class="col-md-6 col-sm-12 mb-2">
                            <p class="h5 text-primary">Cantidad: {{ Cart::count() }}</p>
                        </div>
                        <div class="col-md-6 col-sm-12 mb-2">
                            <p class="h5 text-primary">Subtotal: $ {{ number_format(Cart::subtotal(), 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-6 col-sm-12 mb-2">
                            <p class="h5 text-primary">Total: $ {{ number_format(Cart::total(), 0, ',', '.') }}</p>
                            <input type="hidden" id="total_compra" value="{{ Cart::total() }}">
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-center">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                        Seleccionar metodo de pago
                    </button>
                </div>

            </div>

            <div class="col-lg-6 col-md-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <form action="#" method="get">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <div class="form-group row">
                                    <label for="row" class="align-self-center mx-2">Filas:</label>
                                    <div>
                                        <select class="form-control" name="row">
                                            <option value="10"
                                                @if (request('row') == '10') selected="selected" @endif>10</option>
                                            <option value="25"
                                                @if (request('row') == '25') selected="selected" @endif>25</option>
                                            <option value="50"
                                                @if (request('row') == '50') selected="selected" @endif>50</option>
                                            <option value="100"
                                                @if (request('row') == '100') selected="selected" @endif>100</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="control-label col-sm-3 align-self-center" for="search">Buscar:</label>
                                    <div class="input-group col-sm-8">
                                        <input type="text" id="search" class="form-control" name="search"
                                            placeholder="Buscar Producto" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="input-group-text bg-primary"><i
                                                    class="fas fa-search font-size-20"></i></button>
                                            <a href="{{ route('pos.index') }}" class="input-group-text bg-danger"><i
                                                    class="fas fa-trash"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>


                        <div class="table-responsive rounded mb-3 border-none">
                            <table class="table mb-0">
                                <thead class="bg-white text-uppercase">
                                    <tr class="ligth ligth-data">
                                        <th>No.</th>
                                        <th>Foto</th>
                                        <th>@sortablelink('product_name', 'Nombre')</th>
                                        <th>@sortablelink('selling_price', 'Precio')</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="ligth-body">
                                    @forelse ($products as $product)
                                        <tr>
                                            <td>{{ $products->currentPage() * 10 - 10 + $loop->iteration }}</td>
                                            <td>
                                                <img class="avatar-60 rounded"
                                                    src="{{ $product->product_image ? asset('storage/products/' . $product->product_image) : asset('assets/images/product/default.webp') }}">
                                            </td>
                                            <td>{{ $product->product_name }}</td>
                                            <td>$ {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                            <td>
                                                <form action="{{ route('pos.addCart') }}" method="POST"
                                                    style="margin-bottom: 5px">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                                    <input type="hidden" name="name"
                                                        value="{{ $product->product_name }}">
                                                    <input type="hidden" name="price"
                                                        value="{{ $product->selling_price }}">

                                                    <button type="submit" class="btn btn-primary border-none"
                                                        data-toggle="tooltip" data-placement="top" title=""
                                                        data-original-title="Agregar"><i
                                                            class="far fa-plus mr-0"></i></button>
                                                </form>
                                            </td>
                                        </tr>

                                    @empty
                                        <div class="alert text-white bg-danger" role="alert">
                                            <div class="iq-alert-text">No se encontraron datos.</div>
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Cerrar">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </div>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Seleccionar Cliente y Método de Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('pos.createInvoice') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <label for="customer_id">Seleccionar Cliente</label>
                                <select class="form-control" id="customer_id" name="customer_id" required>
                                    <option selected disabled>-- Seleccionar Cliente --</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mt-3">
                                <label for="payment_method">Método de Pago</label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
                                    <option selected disabled>-- Seleccionar Método --</option>
                                    <option value="EFECTIVO">Efectivo</option>
                                    <option value="TRANSFERENCIA">Transferencia</option>
                                    <option value="DEBITO">Débito</option>
                                    <option value="CUOTAS">Cuotas</option>
                                </select>
                            </div>

                            <div class="col-md-12 mt-3" id="cuotas_section" hidden>
                                <label for="quotas">Número de Cuotas</label>
                                <select class="form-control" id="quotas" name="quotas" required>
                                    <option value="" selected disabled>Seleccione cuotas</option>
                                    <option value="6">6</option>
                                    <option value="9">9</option>
                                </select>
                            </div>

                            <div class="col-md-12 mt-3" id="fecha_pactada" hidden>
                                <label for="day">Día Pactado a pagar Cuota</label>
                                <select class="form-control" id="estimated_payment_date" name="estimated_payment_date"
                                    required>
                                    <option value="" selected disabled>Seleccione Día</option>
                                    @for ($i = 1; $i <= 30; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>


                            <div class="col-md-12 mt-3" id="cuotas_info_section" hidden>
                                <h5>Detalles del Plan de Cuotas</h5>
                                <p><strong>Total Original:</strong> $<span id="total_original">0.00</span></p>
                                <p><strong>Total con Interés:</strong> $<span id="total_interes">0.00</span></p>
                                <p><strong>Cuotas:</strong> $<span id="monto_cuota">0.00</span> cada una</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-success">Crear Factura</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('payment_method').addEventListener('change', function() {
            let cuotasSection = document.getElementById('cuotas_section');
            let cuotasInfoSection = document.getElementById('cuotas_info_section');
            let fechaPactada = document.getElementById('fecha_pactada');
            let selectCuotas = document.getElementById('quotas');
            let day = document.getElementById('estimated_payment_date');

            if (this.value === 'CUOTAS') {
                cuotasSection.removeAttribute('hidden');
                cuotasInfoSection.removeAttribute('hidden');
                fechaPactada.removeAttribute('hidden');
            } else {
                cuotasSection.setAttribute('hidden', 'true');
                cuotasInfoSection.setAttribute('hidden', 'true');
                fechaPactada.setAttribute('hidden', 'true');
                selectCuotas.value = '';
                day.value = '';

            }
        });

        document.getElementById('quotas').addEventListener('change', calcularCuotas);
        document.getElementById('day').addEventListener('input', calcularCuotas);

        function calcularCuotas() {
            let totalOriginal = parseFloat(document.getElementById('total_compra').value) || 0;

            let cuotas = parseInt(document.getElementById('quotas').value);
            let interes = 0;

            if (cuotas === 9) {
                interes = 0.70;
            } else if (cuotas === 6) {
                interes = 0.35;
            }

            let totalConInteres = (totalOriginal) * (1 + interes);
            let montoCuota = totalConInteres / cuotas;

            document.getElementById('total_original').innerText = totalOriginal.toFixed(2);
            document.getElementById('total_interes').innerText = totalConInteres.toFixed(2);
            document.getElementById('monto_cuota').innerText = montoCuota.toFixed(2);
        }
    </script>
@endsection
