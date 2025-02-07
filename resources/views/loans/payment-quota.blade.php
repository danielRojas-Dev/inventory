@extends('dashboard.body.main')

@section('container')
    <div class="container mb-5">
        <h1 class="mb-4">COBRAR CUOTA</h1>

        @if ($daysOverdue > 0)
            <div class="alert text-white bg-danger" role="alert">
                <div class="iq-alert-text">La cuota está vencida, se debió haber abonado hace {{ $daysOverdue }} día/s.
                </div>
            </div>
        @endif

        <form action="{{ Route('loan.paymentLoan') }}" method="POST">
            @csrf
            <input type="hidden" name="overdue" value="{{ $daysOverdue }}">
            <input type="hidden" name="quotaId" value="{{ $quota->id }}">

            <div class="mb-3">
                <label class="form-label">Número de Cuota</label>
                <input type="text" class="form-control" value="{{ $quota->number_quota }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha Pactada a Pagar</label>
                <input type="date" class="form-control" value="{{ $quota->estimated_payment_date }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Total Estimado a Pagar</label>
                <input type="text" class="form-control"
                    value="$ {{ number_format($quota->estimated_payment, 0, ',', '.') }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Formas de Pago</label>
                <select class="form-control" name="payment_method" required>
                    <option value="" selected disabled>Seleccione un Método de Pago</option>
                    <option value="EFECTIVO">Efectivo</option>
                    <option value="TRANSFERENCIA">Tarjeta</option>
                    <option value="DEBITO">Transferencia</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Moneda de Pago</label>
                <select class="form-control" name="currency" required>
                    <option value="" selected disabled>Seleccione una Moneda</option>
                    <option value="DOLARES">Dólares</option>
                    <option value="PESOS">Pesos</option>
                </select>
            </div>

            <div class="mb-3" id="interestContainer" style="display: none;">
                <label class="form-label">Interés (%)</label>
                <select class="form-control" name="interest" id="interest" required>
                    <option value="0" disabled selected>Seleccione un % de interes</option>
                    <option value="0.5">0.5%</option>
                    <option value="1">1%</option>
                    <option value="1.5">1.5%</option>
                </select>
            </div>
            <div class="mb-3" id="incrementContainer" style="display: none;">
                <label class="form-label">Incremento por Interes</label>
                <input type="text" class="form-control" id="increment" name="increment" readonly>
                <input type="hidden" id="hidden_increment" name="increment">
            </div>

            <div class="mb-3">
                <label class="form-label">Total a Abonar</label>
                <input type="text" class="form-control" id="total_to_pay" name="total_to_pay" readonly>
                <input type="hidden" id="hidden_total_to_pay" name="total_to_pay">
            </div>


            <button type="submit" class="btn btn-primary">Confirmar Pago</button>
            <a href="{{ url()->previous() }}" class="btn btn-danger">Cancelar</a>

        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedPayment = parseFloat({{ $quota->estimated_payment }});
            const daysOverdue = {{ $daysOverdue }};
            const interestContainer = document.getElementById('interestContainer');
            const interestSelect = document.getElementById('interest');
            const incrementContainer = document.getElementById('incrementContainer');
            const incrementInput = document.getElementById('increment');
            const totalToPayInput = document.getElementById('total_to_pay');

            const hiddenIncrement = document.getElementById('hidden_increment');
            const hiddenTotalToPay = document.getElementById('hidden_total_to_pay');

            function formatCurrency(value) {
                return `$ ${value.toLocaleString('es-AR', { maximumFractionDigits: 0 })}`;
            }

            function updateTotal() {
                const dailyInterest = parseFloat(interestSelect.value) / 100;
                const increment = Math.round(estimatedPayment * dailyInterest * daysOverdue);
                const totalToPay = Math.round(estimatedPayment + increment);

                incrementInput.value = formatCurrency(increment);
                totalToPayInput.value = formatCurrency(totalToPay);

                // Actualizar los campos ocultos para enviar valores sin formato en el formulario
                hiddenIncrement.value = increment;
                hiddenTotalToPay.value = totalToPay;
            }

            if (daysOverdue > 0) {
                interestContainer.style.display = 'block';
                incrementContainer.style.display = 'block';
                interestSelect.addEventListener('change', updateTotal);
                updateTotal();
            } else {
                totalToPayInput.value = formatCurrency(estimatedPayment);
                hiddenTotalToPay.value = estimatedPayment;
            }
        });
    </script>
@endsection
