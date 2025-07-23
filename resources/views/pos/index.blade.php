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
                <table id="table" class=" display nowrap" style="width:100%">
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
                    <button type="button" class="btn btn-success " data-bs-toggle="modal" data-bs-target="#paymentModal"
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
                            </div>
                        </form>


                        <div class="table-responsive rounded mb-3 border-none">
                            <table id="table" class=" display nowrap" style="width:100%">

                                <thead class="bg-white text-uppercase">
                                    <tr class="ligth ligth-data">
                                        <th data-priority="1">Foto</th>
                                        <th>Nombre</th>
                                        <th>Marca</th>
                                        <th>Precio</th>
                                        <th data-priority="2">Stock</th>
                                        <th data-priority="3">Acción</th>
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
                                                        class="badge bg-danger btn-sm text-white">{{ $product->product_store }}</span>
                                                @else
                                                    <span id="stock-{{ $product->id }}"
                                                        class="badge bg-success btn-sm text-white">{{ $product->product_store }}</span>
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
                                                            <button type="submit" disabled
                                                                class="btn btn-primary btn-sm " data-toggle="tooltip"
                                                                data-placement="top" title="Agregar">
                                                                <i class="fas fa-plus mr-0"></i>
                                                            </button>
                                                        @else
                                                            <button type="submit"
                                                                style="width: 30px; height: 25px; padding: 0; font-size: 12px;"
                                                                class="btn btn-primary btn-sm"
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
                                    min="0" step="0.001" placeholder="Ingrese el % de interés">
                            </div>
                            <div class="col-md-12 mt-3" id="cuota_section" hidden>
                                <label for="monto_cuota">Monto de Cuota</label>
                                <input type="number" class="form-control" id="monto_cuota" name="monto_cuota"
                                    min="0" step="0.001" placeholder="Ingrese el monto de la cuota">
                            </div>

                            <div class="col-md-12 mt-3" id="fecha_pactada" hidden>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="month">Mes Pactado</label>
                                        <select class="form-control" id="payment_month" name="payment_month">
                                            <option value="" selected disabled>Seleccione Mes</option>
                                            <option value="1">Enero</option>
                                            <option value="2">Febrero</option>
                                            <option value="3">Marzo</option>
                                            <option value="4">Abril</option>
                                            <option value="5">Mayo</option>
                                            <option value="6">Junio</option>
                                            <option value="7">Julio</option>
                                            <option value="8">Agosto</option>
                                            <option value="9">Septiembre</option>
                                            <option value="10">Octubre</option>
                                            <option value="11">Noviembre</option>
                                            <option value="12">Diciembre</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="day">Día Pactado</label>
                                        <select class="form-control" id="estimated_payment_date"
                                            name="estimated_payment_date">
                                            <option value="" selected disabled>Seleccione Día</option>
                                            @for ($i = 1; $i <= 29; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-3" id="entrega_section" hidden>
                                <label for="entrega">Monto de Entrega</label>
                                <input type="number" class="form-control" id="entrega" name="entrega" min="0"
                                    step="0.001" placeholder="Ingrese el monto de la entrega">
                            </div>

                            <div class="col-md-12 mt-3" id="presupuesto_section" hidden>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="valid_days">Días de Validez del Presupuesto</label>
                                        <input type="number" class="form-control" id="valid_days" name="valid_days"
                                            min="1" max="90" value="30" placeholder="30">
                                        <small class="text-muted">El presupuesto será válido por estos días</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="notes">Notas del Presupuesto (Opcional)</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="2"
                                            placeholder="Agregar notas sobre el presupuesto..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-3" id="cuotas_info_section" hidden>
                                <h5>Detalles del Plan de Cuotas</h5>
                                <p><strong>Total Original:</strong> <span id="total_original">0.00</span></p>
                                <p><strong>Total con Interés:</strong> <span id="total_interes">0.00</span></p>
                                <p><strong>Cuotas:</strong> <span id="monto_cuota_info">0.00</span> cada una</p>
                                <p><strong>Entrega:</strong> <span id="entrega_info">0.00</span></p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-success" name="action" value="invoice">Crear
                                Factura</button>
                            <button type="submit" class="btn btn-primary" name="action" value="budget"
                                formaction="{{ route('budgets.create') }}">Generar Presupuesto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Mostrar sección de presupuesto cuando se hace click en el botón
        const budgetBtn = document.querySelector('button[value="budget"]');
        const invoiceBtn = document.querySelector('button[value="invoice"]');
        const presupuestoSection = document.getElementById('presupuesto_section');
        let budgetFieldsShown = false;

        if (budgetBtn && invoiceBtn && presupuestoSection) {
            budgetBtn.addEventListener('click', function(e) {
                if (!budgetFieldsShown) {
                    // Prevenir el envío del formulario la primera vez
                    e.preventDefault();

                    // Mostrar campos específicos del presupuesto
                    presupuestoSection.removeAttribute('hidden');

                    // Cambiar el texto del botón para indicar que ya se pueden completar los campos
                    budgetBtn.innerHTML = '<i class="fas fa-check"></i> Crear Presupuesto';
                    budgetBtn.classList.remove('btn-primary');
                    budgetBtn.classList.add('btn-success');

                    budgetFieldsShown = true;
                } else {
                    // Ya se mostraron los campos, permitir el envío del formulario
                    // Validar que se haya seleccionado cliente
                    const customerId = document.getElementById('customer_id').value;
                    const paymentMethod = document.getElementById('payment_method').value;

                    if (!customerId) {
                        e.preventDefault();
                        alert('Por favor seleccione un cliente');
                        return;
                    }

                    if (!paymentMethod) {
                        e.preventDefault();
                        alert('Por favor seleccione un método de pago');
                        return;
                    }

                    // Si todo está bien, el formulario se enviará normalmente
                }
            });

            invoiceBtn.addEventListener('click', function(e) {
                // Ocultar campos específicos del presupuesto
                presupuestoSection.setAttribute('hidden', true);

                // Resetear el botón de presupuesto
                budgetBtn.innerHTML = 'Generar Presupuesto';
                budgetBtn.classList.remove('btn-success');
                budgetBtn.classList.add('btn-primary');
                budgetFieldsShown = false;
            });
        }
    </script>
    <script>
        document.getElementById('payment_method').addEventListener('change', function() {
            let cuotasSection = document.getElementById('cuotas_section');
            let cuotasInfoSection = document.getElementById('cuotas_info_section');
            let fechaPactada = document.getElementById('fecha_pactada');
            let interesSection = document.getElementById('interes_section');
            let montoCuota = document.getElementById('cuota_section');
            let entrega = document.getElementById('entrega_section');
            let selectCuotas = document.getElementById('quotas');
            let day = document.getElementById('estimated_payment_date');
            let interestInput = document.getElementById('interest_rate');
            let paymentMonth = document.getElementById('payment_month');

            if (this.value === 'CUOTAS') {
                cuotasSection.removeAttribute('hidden');
                cuotasInfoSection.removeAttribute('hidden');
                fechaPactada.removeAttribute('hidden');
                interesSection.removeAttribute('hidden');
                montoCuota.removeAttribute('hidden');
                entrega.removeAttribute('hidden');
                selectCuotas.setAttribute('required', true);
                day.setAttribute('required', true);
                paymentMonth.setAttribute('required', true);
                interestInput.setAttribute('required', true);
            } else {
                cuotasSection.setAttribute('hidden', 'true');
                cuotasInfoSection.setAttribute('hidden', 'true');
                fechaPactada.setAttribute('hidden', 'true');
                interesSection.setAttribute('hidden', 'true');
                selectCuotas.removeAttribute('required');
                entrega.setAttribute('hidden', 'true');
                day.removeAttribute('required');
                paymentMonth.removeAttribute('required');
                interestInput.removeAttribute('required');
                selectCuotas.value = '';
                day.value = '';
                paymentMonth.value = '';
                interestInput.value = '';
            }
        });

        document.getElementById('quotas').addEventListener('change', calcularCuotas);
        document.getElementById('interest_rate').addEventListener('input', calcularCuotas);
        document.getElementById('monto_cuota').addEventListener('input', calcularInteres);

        function formatCurrency(value) {
            return `$ ${value.toLocaleString('es-AR', { maximumFractionDigits: 0 })}`;
        }

        document.getElementById('entrega').addEventListener('input', function() {
            let entrega = parseFloat(document.getElementById('entrega').value) || 0;
            document.getElementById('entrega_info').innerText = formatCurrency(entrega);
        });


        function calcularCuotas() {
            let totalOriginal = parseFloat(document.getElementById('total_compra').value) || 0;
            let cuotas = parseInt(document.getElementById('quotas').value) || 1;
            let interestRate = parseFloat(document.getElementById('interest_rate').value) || 0;

            let totalConInteres = totalOriginal * (1 + (interestRate / 100));
            let montoCuota = totalConInteres / cuotas;

            document.getElementById('total_original').innerText = formatCurrency(totalOriginal);
            document.getElementById('total_interes').innerText = formatCurrency(totalConInteres);
            document.getElementById('monto_cuota_info').innerText = formatCurrency(montoCuota);
            document.getElementById('monto_cuota').value = montoCuota.toFixed();
        }

        function calcularInteres() {
            let totalOriginal = parseFloat(document.getElementById('total_compra').value) || 0;
            let cuotas = parseInt(document.getElementById('quotas').value) || 1;
            let montoCuota = parseFloat(document.getElementById('monto_cuota').value) || 0;

            let totalConInteres = montoCuota * cuotas;
            let interestRate = ((totalConInteres / totalOriginal) - 1) * 100;

            document.getElementById('total_original').innerText = formatCurrency(totalOriginal);
            document.getElementById('total_interes').innerText = formatCurrency(totalConInteres);
            document.getElementById('monto_cuota_info').innerText = formatCurrency(montoCuota);
            document.getElementById('interest_rate').value = interestRate.toFixed(3);
        }
    </script>
@endsection
