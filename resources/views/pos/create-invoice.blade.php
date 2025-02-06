@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block">
                    <div class="card-header d-flex justify-content-between bg-primary">
                        <div class="iq-header-title">
                            <h4 class="card-title mb-0">Factura</h4>
                        </div>

                        <div class="invoice-btn d-flex">
                            <form action="{{ route('pos.printInvoice') }}" method="post">
                                @csrf
                            </form>

                            <button type="button" class="btn btn-success mr-2" data-toggle="modal"
                                data-target=".bd-example-modal-lg">Confirmar Venta</button>

                            <a href="{{ route('pos.index') }}" class="btn btn-danger">Cancelar</a>

                            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-white">
                                            <h3 class="modal-title text-center mx-auto">Confirmación para la venta de
                                                {{ $customer->name }}
                                            </h3>
                                        </div>
                                        <form action="{{ route('pos.storeOrder') }}" method="post">
                                            @csrf
                                            <div class="modal-body">
                                                <p style="color:black">¿Estás seguro de que confirmar esta venta?</p>
                                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                                <input type="hidden" name="total" value="{{ $total_con_interes }}">
                                                <input type="hidden" name="estimated_payment_date"
                                                    value="{{ $estimated_payment_date }}">
                                                <input type="hidden" name="quotas" value="{{ $quotas }}">
                                                <input type="hidden" name="total_original" value="{{ $total_original }}">
                                                <input type="hidden" name="payment_method" value="{{ $payment_method }}">
                                                <input type="hidden" name="interest_rate" value="{{ $interest_rate }}">

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Confirmar</button>
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
                                <img src="{{ asset('assets/images/login/electrodr.png') }}" style="width: 150px"
                                    class="mb-3">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive-sm">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Fecha del Pedido</th>
                                                <th scope="col">Estado del Pedido</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ Carbon\Carbon::now()->format('d M, Y') }}</td>
                                                <td><span class="badge badge-danger">No Pagado</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <h5 class="mb-3">Resumen del Pedido</h5>
                                <div class="table-responsive-lg">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="text-center" scope="col">#</th>
                                                <th scope="col">Artículo</th>
                                                <th class="text-center" scope="col">Cantidad</th>
                                                @if ($quotas != null)
                                                    <th class="text-center" scope="col">Cuotas</th>
                                                    <th class="text-center" scope="col">Monto de Cuota</th>
                                                    <th class="text-center" scope="col">Total</th>
                                                @else
                                                    <th class="text-center" scope="col">Precio</th>

                                                    <th class="text-center" scope="col">Total</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($content as $item)
                                                <tr>
                                                    <th class="text-center" scope="row">{{ $loop->iteration }}</th>
                                                    <td>
                                                        <h6 class="mb-0">{{ $item->name }}</h6>
                                                    </td>
                                                    <td class="text-center">{{ $item->qty }}</td>


                                                    @if ($quotas != null)
                                                        <td class="text-center">
                                                            {{ $quotas }}
                                                        </td>
                                                        <td class="text-center">
                                                            ${{ number_format($monto_cuota, 0, ',', '.') }}
                                                        </td>
                                                        <td class="text-center">
                                                            <b>$ {{ number_format($total_con_interes, 0, ',', '.') }}</b>
                                                        </td>
                                                    @else
                                                        <td class="text-center">
                                                            ${{ number_format($item->price, 0, ',', '.') }}
                                                        </td>
                                                        <td class="text-center">
                                                            <b>$ {{ number_format($item->subtotal, 0, ',', '.') }}</b>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                        <div class="row mt-4 mb-3">
                            <div class="offset-lg-8 col-lg-4">
                                <div class="or-detail rounded">
                                    <div class="p-3">
                                        <h5 class="mb-3">Detalles del Pedido</h5>
                                    </div>


                                    <div class="ttl-amt py-2 px-3 d-flex justify-content-between align-items-center">
                                        <h6>Metodo de Pago</h6>
                                        <h6 class="text-primary font-weight-700">
                                            {{ $payment_method }}</h6>
                                    </div>

                                    @if ($quotas)
                                        <div class="ttl-amt py-2 px-3 d-flex justify-content-between align-items-center">
                                            <h6>Total Original</h6>
                                            <h6 class="text-primary font-weight-700">
                                                ${{ number_format($total_original, 0, ',', '.') }}</h6>
                                        </div>

                                        <div class="ttl-amt py-2 px-3 d-flex justify-content-between align-items-center">
                                            <h6>Total con Intereses</h6>
                                            <h6 class="text-primary font-weight-700">
                                                ${{ number_format($total_con_interes, 0, ',', '.') }}</h6>
                                        </div>

                                        <div class="ttl-amt py-2 px-3 d-flex justify-content-between align-items-center">
                                            <h6>Cuotas ({{ $quotas }})</h6>
                                            <h6 class="text-success font-weight-700">
                                                ${{ number_format($monto_cuota, 0, ',', '.') }} c/u</h6>
                                        </div>
                                    @else
                                        <div class="ttl-amt py-2 px-3 d-flex justify-content-between align-items-center">
                                            <h6>Total</h6>
                                            <h6 class="text-primary font-weight-700">
                                                ${{ number_format($total_original, 0, ',', '.') }}</h6>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
