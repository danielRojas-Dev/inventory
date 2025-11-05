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
                        <form id="loanForm" action="{{ route('loan.storeLoan') }}" method="POST" enctype="multipart/form-data">
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
                                    <label for="total_loan">Monto del Prestamo</label>
                                    <input type="text" class="form-control" id="total_loan" name="total_loan"
                                        placeholder="Ingrese el monto del Prestamo" required>
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
                                    <input type="text" class="form-control" id="interest_rate" name="interest_rate"
                                        placeholder="Ingrese el % de interés">
                                </div>
                                <div class="col-md-12 mt-3 d-none" id="cuota_section">
                                    <label for="monto_cuota">Monto de Cuota</label>
                                    <input type="text" class="form-control" id="monto_cuota" name="monto_cuota"
                                        placeholder="Ingrese el monto de la cuota">
                                </div>

                                <div class="col-md-12 mt-3 d-none" id="fecha_pactada">
                                    <label for="payment_date">Fecha de Inicio y Día Pactado de Pago</label>
                                    <input type="date" class="form-control" id="payment_date" name="payment_date">
                                    <input type="hidden" id="start_month" name="start_month">
                                    <input type="hidden" id="estimated_payment_date" name="estimated_payment_date">
                                </div>



                                <div class="col-md-12 mt-3 d-none" id="cuotas_info_section">
                                    <h5>Detalles del Plan de Cuotas</h5>
                                    <p><strong>Total Original:</strong> <span id="total_original">0.00</span></p>
                                    <p><strong>Total con Interés:</strong> <span id="total_interes">0.00</span></p>
                                    <p><strong>Cuotas:</strong> <span id="monto_cuota_info">0.00</span> cada una</p>
                                </div>
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
        // Función para formatear números con puntos
        function formatNumberWithDots(value) {
            // Eliminar todo excepto números
            let number = value.replace(/[^\d]/g, '');
            // Agregar puntos como separadores de miles
            return number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Función para remover formato de puntos
        function removeDotsFormat(value) {
            return value.replace(/\./g, '');
        }

        // Actualizar campos hidden cuando cambia la fecha
        document.addEventListener('DOMContentLoaded', function() {
            const paymentDateInput = document.getElementById('payment_date');
            if (paymentDateInput) {
                paymentDateInput.addEventListener('change', function() {
                    if (this.value) {
                        const date = new Date(this.value + 'T00:00:00');
                        document.getElementById('start_month').value = date.getMonth() + 1;
                        document.getElementById('estimated_payment_date').value = date.getDate();
                    }
                });
            }

            // Aplicar formato a los inputs numéricos
            const numericInputs = ['total_loan', 'interest_rate', 'monto_cuota'];
            numericInputs.forEach(function(inputId) {
                const input = document.getElementById(inputId);
                if (input) {
                    // Formatear mientras se escribe
                    input.addEventListener('input', function(e) {
                        let cursorPosition = this.selectionStart;
                        let oldLength = this.value.length;
                        let value = this.value;
                        
                        // Si es el campo de interés, permitir decimales
                        if (inputId === 'interest_rate') {
                            // Permitir solo números y un punto decimal
                            value = value.replace(/[^\d.]/g, '');
                            // Asegurar solo un punto decimal
                            let parts = value.split('.');
                            if (parts.length > 2) {
                                value = parts[0] + '.' + parts.slice(1).join('');
                            }
                            // Formatear la parte entera con puntos de miles
                            if (parts.length > 1) {
                                parts[0] = formatNumberWithDots(parts[0]);
                                this.value = parts.join('.');
                            } else {
                                this.value = formatNumberWithDots(value);
                            }
                        } else {
                            // Para otros campos, solo números enteros
                            this.value = formatNumberWithDots(value);
                        }
                        
                        // Ajustar posición del cursor
                        let newLength = this.value.length;
                        cursorPosition += (newLength - oldLength);
                        this.setSelectionRange(cursorPosition, cursorPosition);
                    });

                    // Al salir del campo, disparar evento change para cálculos
                    input.addEventListener('blur', function() {
                        if (this.value) {
                            this.dispatchEvent(new Event('change'));
                        }
                    });
                }
            });

            // Remover formato antes de enviar el formulario
            const form = document.getElementById('loanForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    numericInputs.forEach(function(inputId) {
                        const input = document.getElementById(inputId);
                        if (input && input.value) {
                            input.value = removeDotsFormat(input.value);
                        }
                    });
                });
            }
        });

        document.getElementById('payment_method').addEventListener('change', function() {
            let cuotasSection = document.getElementById('cuotas_section');
            let cuotasInfoSection = document.getElementById('cuotas_info_section');
            let fechaPactada = document.getElementById('fecha_pactada');
            let interesSection = document.getElementById('interes_section');
            let montoCuota = document.getElementById('cuota_section');
            let selectCuotas = document.getElementById('quotas');
            let paymentDate = document.getElementById('payment_date');
            let interestInput = document.getElementById('interest_rate');

            if (this.value === 'CUOTAS') {
                cuotasSection.classList.remove('d-none');
                cuotasInfoSection.classList.remove('d-none');
                fechaPactada.classList.remove('d-none');
                interesSection.classList.remove('d-none');
                montoCuota.classList.remove('d-none');
                selectCuotas.setAttribute('required', true);
                paymentDate.setAttribute('required', true);
                interestInput.setAttribute('required', true);
            } else {
                cuotasSection.classList.add('d-none');
                cuotasInfoSection.classList.add('d-none');
                fechaPactada.classList.add('d-none');
                interesSection.classList.add('d-none');
                montoCuota.classList.add('d-none');
                selectCuotas.removeAttribute('required');
                paymentDate.removeAttribute('required');
                interestInput.removeAttribute('required');
                selectCuotas.value = '';
                paymentDate.value = '';
                document.getElementById('start_month').value = '';
                document.getElementById('estimated_payment_date').value = '';
                interestInput.value = '';
            }
        });

        document.getElementById('total_loan').addEventListener('input', function() {
            // Disparar cálculo si hay cuotas seleccionadas
            if (document.getElementById('quotas').value) {
                calcularCuotas();
            }
        });
        document.getElementById('quotas').addEventListener('change', calcularCuotas);
        document.getElementById('interest_rate').addEventListener('input', calcularCuotas);
        document.getElementById('monto_cuota').addEventListener('input', calcularInteres);

        function formatCurrency(value) {
            return `$ ${value.toLocaleString('es-AR', { maximumFractionDigits: 0 })}`;
        }

        function calcularCuotas() {
            let totalLoanValue = removeDotsFormat(document.getElementById('total_loan').value);
            let totalOriginal = parseFloat(totalLoanValue) || 0;
            let cuotas = parseInt(document.getElementById('quotas').value) || 1;
            let interestRateValue = removeDotsFormat(document.getElementById('interest_rate').value.replace(',', '.'));
            let interestRate = parseFloat(interestRateValue) || 0;

            let totalConInteres = totalOriginal * (1 + (interestRate / 100));
            let montoCuota = totalConInteres / cuotas;

            document.getElementById('total_original').innerText = formatCurrency(totalOriginal);
            document.getElementById('total_interes').innerText = formatCurrency(totalConInteres);
            
            // Formatear el monto de cuota con puntos
            let montoCuotaFormateado = Math.round(montoCuota);
            document.getElementById('monto_cuota').value = formatNumberWithDots(montoCuotaFormateado.toString());
            document.getElementById('monto_cuota_info').innerText = formatCurrency(montoCuota);
        }

        function calcularInteres() {
            let totalLoanValue = removeDotsFormat(document.getElementById('total_loan').value);
            let totalOriginal = parseFloat(totalLoanValue) || 0;
            let cuotas = parseInt(document.getElementById('quotas').value) || 1;
            let montoCuotaValue = removeDotsFormat(document.getElementById('monto_cuota').value);
            let montoCuota = parseFloat(montoCuotaValue) || 0;

            if (montoCuota <= 0 || isNaN(montoCuota)) {
                document.getElementById('interest_rate').value = '';
                return;
            }

            let totalConInteres = montoCuota * cuotas;
            let interestRate = ((totalConInteres / totalOriginal) - 1) * 100;

            document.getElementById('total_original').innerText = formatCurrency(totalOriginal);
            document.getElementById('total_interes').innerText = formatCurrency(totalConInteres);
            document.getElementById('monto_cuota_info').innerText = formatCurrency(montoCuota);
            
            // Formatear el porcentaje de interés
            document.getElementById('interest_rate').value = interestRate.toFixed(3);
        }
    </script>

    @include('components.preview-img-form')
@endsection
