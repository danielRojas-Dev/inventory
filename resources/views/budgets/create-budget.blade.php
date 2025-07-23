@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block">
                    <div class="card-header d-flex justify-content-between bg-primary">
                        <div class="iq-header-title">
                            <h4 class="card-title mb-0 text-white">Presupuesto</h4>
                        </div>

                        <div class="invoice-btn d-flex">
                            <button type="button" class="btn btn-success mr-2" data-toggle="modal"
                                data-target=".bd-example-modal-lg">Confirmar Presupuesto</button>

                            <a href="{{ route('pos.index') }}" class="btn btn-danger">Cancelar</a>

                            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-white">
                                            <h3 class="modal-title text-center mx-auto">Confirmación del presupuesto para
                                                {{ $customer->name }}
                                            </h3>
                                        </div>
                                        <form action="{{ route('budgets.store') }}" method="post">
                                            @csrf
                                            <div class="modal-body">
                                                <p style="color:black">¿Estás seguro de que deseas crear este presupuesto?
                                                </p>
                                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                                <input type="hidden" name="total" value="{{ $total_con_interes }}">
                                                <input type="hidden" name="quotas" value="{{ $quotas }}">
                                                <input type="hidden" name="payment_method" value="{{ $payment_method }}">
                                                <input type="hidden" name="interest_rate" value="{{ $interest_rate }}">
                                                <input type="hidden" name="valid_until" value="{{ $valid_until }}">
                                                <input type="hidden" name="notes" value="{{ $notes }}">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Crear Presupuesto</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div>
                                    <h4 class="mb-3">Información del Cliente:</h4>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <p><strong>Nombre:</strong> {{ $customer->name }}</p>
                                            <p><strong>DNI:</strong> {{ $customer->dni }}</p>
                                            <p><strong>Teléfono:</strong> {{ $customer->phone }}</p>
                                        </div>
                                        <div class="col-lg-6">
                                            <p><strong>Dirección:</strong> {{ $customer->address }}</p>
                                            <p><strong>Ciudad:</strong> {{ $customer->city }}</p>
                                            <p><strong>Método de Pago:</strong> {{ $payment_method }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div>
                                    <h4 class="mb-3">Información del Presupuesto:</h4>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <p><strong>Válido hasta:</strong> {{ $valid_until->format('d/m/Y') }}</p>
                                            <p><strong>Días de validez:</strong> {{ $valid_until->diffInDays(now()) }} días
                                            </p>
                                            @if ($quotas)
                                                <p><strong>Número de cuotas:</strong> {{ $quotas }}</p>
                                                <p><strong>Interés aplicado:</strong> {{ $interest_rate }}%</p>
                                            @endif
                                        </div>
                                        <div class="col-lg-6">
                                            @if ($notes)
                                                <p><strong>Notas:</strong></p>
                                                <p class="text-muted">{{ $notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <h4 class="mb-3">Productos:</h4>
                                <div class="table-responsive-sm">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="text-center" scope="col">#</th>
                                                <th scope="col">Producto</th>
                                                <th class="text-center" scope="col">Precio</th>
                                                <th class="text-center" scope="col">Cantidad</th>
                                                <th class="text-center" scope="col">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($content as $item)
                                                <tr>
                                                    <th class="text-center" scope="row">{{ $loop->iteration }}</th>
                                                    <td>{{ $item->name }}</td>
                                                    <td class="text-center">${{ number_format($item->price, 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-center">{{ $item->qty }}</td>
                                                    <td class="text-center">${{ number_format($item->total, 0, ',', '.') }}
                                                    </td>
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
                                            <td class="left"><strong>Subtotal</strong></td>
                                            <td class="right">${{ number_format($total_original, 0, ',', '.') }}</td>
                                        </tr>
                                        @if ($interest_rate > 0)
                                            <tr>
                                                <td class="left"><strong>Interés ({{ $interest_rate }}%)</strong></td>
                                                <td class="right">
                                                    ${{ number_format($total_con_interes - $total_original, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="left"><strong>Total</strong></td>
                                            <td class="right">
                                                <strong>${{ number_format($total_con_interes, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        @if ($quotas && $monto_cuota)
                                            <tr>
                                                <td class="left"><strong>Cuota mensual</strong></td>
                                                <td class="right">
                                                    <strong>${{ number_format($monto_cuota, 0, ',', '.') }}</strong></td>
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
