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
        @if ($totalPreviousAmountDifference !== null && $totalPreviousAmountDifference != 0)
            @if ($totalPreviousAmountDifference < 0)
                <div class="alert text-white bg-danger" role="alert">
                    <div class="iq-alert-text">
                        El monto abonado anteriormente fue menor al de la cuota. Debe abonar
                        $ {{ number_format(abs($totalPreviousAmountDifference), 0, ',', '.') }} adicionales junto con esta
                        cuota.
                    </div>
                </div>
            @else
                <div class="alert text-white bg-success" role="alert">
                    <div class="iq-alert-text">
                        Tiene un saldo a favor de $ {{ number_format($totalPreviousAmountDifference, 0, ',', '.') }} que
                        será
                        descontado de esta cuota.
                    </div>
                </div>
            @endif
        @endif

        <form action="{{ Route('quota.payment') }}" method="POST">
            @csrf
            <input type="hidden" name="overdue" value="{{ $daysOverdue }}">
            <input type="hidden" name="totalPreviousAmountDifference" value="{{ $totalPreviousAmountDifference }}">
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
                <label class="form-label">Formas de Pago</label>
                <select class="form-control" name="payment_method" required>
                    <option value="" selected disabled>Seleccione un Método de Pago</option>
                    <option value="EFECTIVO">Efectivo</option>
                    <option value="DEBITO">Débito</option>
                    <option value="TRANSFERENCIA">Transferencia</option>
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
                    <option value="0" disabled selected>Seleccione un % de interés</option>
                    <option value="0.5">0.5%</option>
                    <option value="1">1%</option>
                    <option value="1.5">1.5%</option>
                </select>
            </div>
            <div class="mb-3" id="incrementContainer" style="display: none;">
                <label class="form-label">Incremento por Interés</label>
                <input type="text" class="form-control" id="increment" readonly>
                <input type="hidden" id="hidden_increment" name="increment">
            </div>

            <div class="mb-3">
                <label class="form-label">Total Estimado a Pagar</label>
                <input type="text" class="form-control"
                    value="$ {{ number_format($quota->estimated_payment, 0, ',', '.') }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Total a Abonar</label>
                <input type="text" class="form-control" id="total_to_pay" name="total_to_pay">
            </div>

            <button type="submit" class="btn btn-primary">Confirmar Pago</button>
            <a href="{{ url()->previous() }}" class="btn btn-danger">Cancelar</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estimatedPayment = parseFloat({{ $quota->estimated_payment }});
            const daysOverdue = {{ $daysOverdue }};
            const previousAmountDifference = parseFloat({{ $totalPreviousAmountDifference ?? 0 }});

            const interestContainer = document.getElementById('interestContainer');
            const interestSelect = document.getElementById('interest');
            const incrementContainer = document.getElementById('incrementContainer');
            const incrementInput = document.getElementById('increment');
            const totalToPayInput = document.getElementById('total_to_pay');
            const hiddenIncrement = document.getElementById('hidden_increment');

            function formatCurrency(value) {
                return `$ ${value.toLocaleString('es-AR', { maximumFractionDigits: 0 })}`;
            }

            function updateTotal() {
                const dailyInterest = parseFloat(interestSelect.value) / 100;
                const increment = Math.round(estimatedPayment * dailyInterest * daysOverdue);
                let totalToPay = estimatedPayment + increment;

                // Aplicar el saldo a favor o deuda pendiente
                totalToPay -= previousAmountDifference;

                incrementInput.value = formatCurrency(increment);
                totalToPayInput.value = totalToPay;
                hiddenIncrement.value = increment;
            }

            if (daysOverdue > 0) {
                interestContainer.style.display = 'block';
                incrementContainer.style.display = 'block';
                interestSelect.addEventListener('change', updateTotal);
                updateTotal();
            } else {
                let totalToPay = estimatedPayment - previousAmountDifference;
                totalToPayInput.value = totalToPay;
            }
        });
    </script>

@endsection
