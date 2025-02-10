@extends('dashboard.body.main')

@section('specificpagestyles')
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Agregar Prestamo</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('loan.storeLoan') }}" method="POST" enctype="multipart/form-data">
                            @csrf
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
                                <label for="total_loan">Monto del Prestamo</label>
                                <input type="number" class="form-control" id="total_loan" name="total_loan" step="0.01"
                                    min="0" placeholder="Ingrese el monto del Prestamo" required>
                            </div>

                            <div class="col-md-12 mt-3">
                                <label for="payment_method">Método de Pago</label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
                                    <option selected disabled>-- Seleccionar Método --</option>
                                    <option value="CUOTAS">Cuotas</option>
                                </select>
                            </div>

                            <div class="col-md-12 mt-3 d-none" id="cuotas_section">
                                <label for="quotas">Número de Cuotas</label>
                                <select class="form-control" id="quotas" name="quotas">
                                    <option value="" selected disabled>Seleccione cuotas</option>
                                    @for ($i = 1; $i <= 18; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-12 mt-3 d-none" id="interes_section">
                                <label for="interest_rate">Porcentaje de Interés (%)</label>
                                <input type="number" class="form-control" id="interest_rate" name="interest_rate"
                                    step="0.01" min="0" placeholder="Ingrese el % de interés">
                            </div>

                            <div class="col-md-12 mt-3 d-none" id="fecha_pactada">
                                <label for="estimated_payment_date">Día Pactado a pagar Cuota</label>
                                <select class="form-control" id="estimated_payment_date" name="estimated_payment_date">
                                    <option value="" selected disabled>Seleccione Día</option>
                                    @for ($i = 1; $i <= 29; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-12 mt-3 d-none" id="cuotas_info_section">
                                <h5>Detalles del Plan de Cuotas</h5>
                                <p><strong>Total Original:</strong> <span id="total_original">0.00</span></p>
                                <p><strong>Total con Interés:</strong> <span id="total_interes">0.00</span></p>
                                <p><strong>Cuotas:</strong> <span id="monto_cuota">0.00</span> cada una</p>
                            </div>

                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary mr-2">Guardar</button>
                                <a class="btn bg-danger" href="{{ route('loan.completeLoans') }}">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('payment_method').addEventListener('change', function() {
            let cuotasSection = document.getElementById('cuotas_section');
            let cuotasInfoSection = document.getElementById('cuotas_info_section');
            let fechaPactada = document.getElementById('fecha_pactada');
            let interesSection = document.getElementById('interes_section');

            if (this.value === 'CUOTAS') {
                cuotasSection.classList.remove('d-none');
                cuotasInfoSection.classList.remove('d-none');
                fechaPactada.classList.remove('d-none');
                interesSection.classList.remove('d-none');
            } else {
                cuotasSection.classList.add('d-none');
                cuotasInfoSection.classList.add('d-none');
                fechaPactada.classList.add('d-none');
                interesSection.classList.add('d-none');
            }
        });

        document.getElementById('quotas').addEventListener('change', calcularCuotas);
        document.getElementById('interest_rate').addEventListener('input', calcularCuotas);
        document.getElementById('total_loan').addEventListener('input', calcularCuotas);

        function formatCurrency(value) {
            return `$ ${value.toLocaleString('es-AR', { maximumFractionDigits: 0 })}`;
        }

        function calcularCuotas() {
            let totalOriginal = parseFloat(document.getElementById('total_loan').value) || 0;
            let cuotas = parseInt(document.getElementById('quotas').value) || 1;
            let interestRate = parseFloat(document.getElementById('interest_rate').value) || 0;

            if (totalOriginal <= 0 || isNaN(totalOriginal)) {
                document.getElementById('total_original').innerText = '$ 0.00';
                document.getElementById('total_interes').innerText = '$ 0.00';
                document.getElementById('monto_cuota').innerText = '$ 0.00';
                return;
            }

            let totalConInteres = totalOriginal * (1 + (interestRate / 100));
            let montoCuota = totalConInteres / cuotas;

            document.getElementById('total_original').innerText = formatCurrency(totalOriginal);
            document.getElementById('total_interes').innerText = formatCurrency(totalConInteres);
            document.getElementById('monto_cuota').innerText = formatCurrency(montoCuota);
        }
    </script>

    @include('components.preview-img-form')
@endsection
