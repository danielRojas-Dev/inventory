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
                @if (session()->has('error'))
                    <div class="alert text-white bg-danger" role="alert">
                        <div class="iq-alert-text">{{ session('error') }}</div>
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
                                                style="width: 50px; height: 30px;" value="{{ old('qty', $item->qty) }}">
                                            <div class="input-group-append">
                                                <button style="width: 45px; height: 30px;" type="submit"
                                                    class="btn btn-success border-none" data-toggle="tooltip"
                                                    data-placement="top" title="" data-original-title="Enviar"><i
                                                        style="width: 25px; height: 20px;"
                                                        class="fas fa-check"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                <td> ${{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-danger" style="width: 30px; height: 25px;">
                                        <a href="{{ route('pos.deleteCart', $item->rowId) }}" data-toggle="tooltip"
                                            data-placement="top" title="" data-original-title="Eliminar"><i
                                                style="width: 15px; height: 15px; font-size: 12px;color: white"class="fa fa-trash mr-0"></i></a>
                                    </span>
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
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal"
                        {{ Cart::total() == 0 ? 'disabled' : '' }}>
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
                                        <th>Foto</th>
                                        <th>@sortablelink('product_name', 'Nombre')</th>
                                        <th>@sortablelink('brand_name', 'Marca')</th>
                                        <th>@sortablelink('selling_price', 'Precio')</th>
                                        <th>@sortablelink('product_store', 'Stock')</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="ligth-body">
                                    @forelse ($products as $product)
                                        <tr>
                                            <td>
                                                <img class="avatar-60 rounded"
                                                    src="{{ $product->product_image ? asset('storage/products/' . $product->product_image) : asset('assets/images/product/default.webp') }}">
                                            </td>
                                            <td>{{ $product->product_name }}</td>
                                            <td>{{ $product->brand->name }}</td>
                                            <td>$ {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                            <td style="padding: 8px; font-size: 18px; text-align: center;">
                                                @if ($product->product_store == 0)
                                                    <span id="stock-{{ $product->id }}"
                                                        class="badge bg-danger text-white">{{ $product->product_store }}</span>
                                                @else
                                                    <span id="stock-{{ $product->id }}"
                                                        class="badge bg-success text-white">{{ $product->product_store }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('pos.addCart') }}" method="POST"
                                                    style="margin-bottom: 5px">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                                    <input type="hidden" name="name"
                                                        value="{{ $product->product_name }}">
                                                    <input type="hidden" name="price"
                                                        value="{{ $product->selling_price }}">

                                                    <div class="product-options">
                                                        @if ($product->product_store == 0)
                                                            <button type="submit" disabled class="btn btn-primary "
                                                                data-toggle="tooltip" data-placement="top"
                                                                title="Agregar">
                                                                <i class="fas fa-plus mr-0"></i>
                                                            </button>
                                                        @else
                                                            <button type="submit"
                                                                style="width: 30px; height: 25px; padding: 0; font-size: 12px;"
                                                                class="btn btn-primary"
                                                                data-product-stock="{{ $product->product_store }}">
                                                                <i style="width: 15px; height: 15px; font-size: 12px;"
                                                                    class="fas fa-plus mr-0"></i>
                                                            </button>
                                                        @endif
                                                    </div>
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
                                    <option value="" selected disabled>-- Seleccionar Cliente --</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mt-3">
                                <label for="payment_method">Método de Pago</label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
                                    <option value="" selected disabled>-- Seleccionar Método --</option>
                                    <option value="EFECTIVO">Efectivo</option>
                                    <option value="TRANSFERENCIA">Transferencia</option>
                                    <option value="DEBITO">Débito</option>
                                    <option value="CUOTAS">Cuotas</option>
                                </select>
                            </div>

                            <div class="col-md-12 mt-3" id="cuotas_section" hidden>
                                <label for="quotas">Número de Cuotas</label>
                                <select class="form-control" id="quotas" name="quotas">
                                    <option value="" selected disabled>Seleccione cuotas</option>
                                    @for ($i = 1; $i <= 18; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-12 mt-3" id="interes_section" hidden>
                                <label for="interest_rate">Porcentaje de Interés (%)</label>
                                <input type="number" class="form-control" id="interest_rate" name="interest_rate"
                                    min="0" placeholder="Ingrese el % de interés">
                            </div>

                            <div class="col-md-12 mt-3" id="fecha_pactada" hidden>
                                <label for="day">Día Pactado a pagar Cuota</label>
                                <select class="form-control" id="estimated_payment_date" name="estimated_payment_date">
                                    <option value="" selected disabled>Seleccione Día</option>
                                    @for ($i = 1; $i <= 30; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>


                            <div class="col-md-12 mt-3" id="cuotas_info_section" hidden>
                                <h5>Detalles del Plan de Cuotas</h5>
                                <p><strong>Total Original:</strong> <span id="total_original">0.00</span></p>
                                <p><strong>Total con Interés:</strong> <span id="total_interes">0.00</span></p>
                                <p><strong>Cuotas:</strong> <span id="monto_cuota">0.00</span> cada una</p>
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
            let interesSection = document.getElementById('interes_section');
            let selectCuotas = document.getElementById('quotas');
            let day = document.getElementById('estimated_payment_date');
            let interestInput = document.getElementById('interest_rate');

            if (this.value === 'CUOTAS') {
                cuotasSection.removeAttribute('hidden');
                cuotasInfoSection.removeAttribute('hidden');
                fechaPactada.removeAttribute('hidden');
                interesSection.removeAttribute('hidden');
                selectCuotas.setAttribute('required', true);
                day.setAttribute('required', true);
                interestInput.setAttribute('required', true);
            } else {
                cuotasSection.setAttribute('hidden', 'true');
                cuotasInfoSection.setAttribute('hidden', 'true');
                fechaPactada.setAttribute('hidden', 'true');
                interesSection.setAttribute('hidden', 'true');
                selectCuotas.removeAttribute('required');
                day.removeAttribute('required');
                interestInput.removeAttribute('required');
                selectCuotas.value = '';
                day.value = '';
                interestInput.value = '';
            }
        });

        document.getElementById('quotas').addEventListener('change', calcularCuotas);
        document.getElementById('interest_rate').addEventListener('input', calcularCuotas);

        function formatCurrency(value) {
            return `$ ${value.toLocaleString('es-AR', { maximumFractionDigits: 0 })}`;
        }

        function calcularCuotas() {
            let totalOriginal = parseFloat(document.getElementById('total_compra').value) || 0;
            let cuotas = parseInt(document.getElementById('quotas').value) || 1;
            let interestRate = parseFloat(document.getElementById('interest_rate').value) || 0;

            let totalConInteres = totalOriginal * (1 + (interestRate / 100));
            let montoCuota = totalConInteres / cuotas;

            document.getElementById('total_original').innerText = formatCurrency(totalOriginal);
            document.getElementById('total_interes').innerText = formatCurrency(totalConInteres);

            document.getElementById('monto_cuota').innerText = formatCurrency(montoCuota);

        }
    </script>
@endsection
