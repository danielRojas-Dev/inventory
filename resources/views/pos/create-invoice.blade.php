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
                                {{-- <input type="hidden" name="customer_id" value="{{ $customer->id }}"> --}}
                                {{-- <button type="submit" class="btn btn-primary-dark mr-2"><i class="las la-print"></i>
                                    Imprimir</button> --}}
                            </form>

                            <button type="button" class="btn btn-primary-dark mr-2" data-toggle="modal"
                                data-target=".bd-example-modal-lg">Crear</button>

                            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-white">
                                            <h3 class="modal-title text-center mx-auto">Factura
                                                {{-- de
                                                {{ $customer->name }} --}}
                                                <br />Monto Total $ {{ number_format(Cart::total(), 2, ',', '.') }}
                                            </h3>
                                        </div>
                                        <form action="{{ route('pos.storeOrder') }}" method="post">
                                            @csrf
                                            <div class="modal-body">
                                                {{-- <input type="hidden" name="customer_id" value="{{ $customer->id }}"> --}}

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="payment_status">Método de Pago</label>
                                                        <select
                                                            class="form-control @error('payment_status') is-invalid @enderror"
                                                            name="payment_status">
                                                            <option selected="" disabled="">-- Seleccionar Método de
                                                                Pago --</option>
                                                            <option value="Efectivo">Efectivo</option>
                                                            <option value="Transferencia">Transferencia</option>
                                                        </select>
                                                        @error('payment_status')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                {{-- <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="pay">Pagar Ahora</label>
                                                        <input type="text"
                                                            class="form-control @error('pay') is-invalid @enderror"
                                                            id="pay" name="pay" value="{{ old('pay') }}">
                                                        @error('pay')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div> --}}
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cerrar</button>
                                                <button type="submit" class="btn btn-primary">Guardar</button>
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
                                <img src="{{ asset('assets/images/login/MIA.png') }}" style="width: 150px" class="mb-3">
                                {{-- <h5 class="mb-3">Hola, {{ $customer->name }}</h5> --}}
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
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ Carbon\Carbon::now()->format('d M, Y') }}</td>
                                                <td><span class="badge badge-danger">No Pagado</span></td>
                                                {{-- <td>
                                                    <p class="mb-0">{{ $customer->address }}<br>
                                                        Nombre de la Tienda:
                                                        {{ $customer->shopname ? $customer->shopname : '-' }}<br>
                                                        Teléfono: {{ $customer->phone }}<br>
                                                        Correo Electrónico: {{ $customer->email }}<br>
                                                    </p>
                                                </td> --}}
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
                                                <th class="text-center" scope="col">Precio</th>
                                                <th class="text-center" scope="col">Totales</th>
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
                                                    <td class="text-center">$
                                                        {{ number_format($item->price, 2, ',', '.') }}
                                                    </td>
                                                    <td class="text-center">
                                                        <b>$ {{ number_format($item->subtotal, 2, ',', '.') }}</b>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="row">
                            <div class="col-sm-12">
                                <b class="text-danger">Notas:</b>
                                <p class="mb-0">Es un hecho establecido desde hace mucho tiempo que un lector se distraerá con el contenido legible de una página cuando mire su diseño. El objetivo de usar Lorem Ipsum es que tiene una distribución más o menos normal de letras, a diferencia de usar 'Contenido aquí, contenido aquí', haciéndolo parecer un inglés legible.</p>
                            </div>
                        </div> --}}

                        <div class="row mt-4 mb-3">
                            <div class="offset-lg-8 col-lg-4">
                                <div class="or-detail rounded">
                                    <div class="p-3">
                                        <h5 class="mb-3">Detalles del Pedido</h5>
                                        <div class="mb-2">
                                            <h6>Sub Total</h6>
                                            <p>${{ number_format(Cart::subtotal(), 2, ',', '.') }}</p>
                                        </div>
                                        {{-- <div>
                                            <h6>IVA (5%)</h6>
                                            <p>${{ Cart::tax() }}</p>
                                        </div> --}}
                                    </div>
                                    <div class="ttl-amt py-2 px-3 d-flex justify-content-between align-items-center">
                                        <h6>Total</h6>
                                        <h3 class="text-primary font-weight-700">
                                            ${{ number_format(Cart::total(), 2, ',', '.') }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
