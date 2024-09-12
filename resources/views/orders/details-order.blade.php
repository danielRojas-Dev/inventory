@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Detalles del pedido</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- begin: Show Data -->
                        {{-- <div class="form-group row align-items-center">
                            <div class="col-md-12">
                                <div class="profile-img-edit">
                                    <div class="crm-profile-img-edit">
                                        <img class="crm-profile-pic rounded-circle avatar-100" id="image-preview"
                                            src="{{ $order->customer->photo ? asset('storage/customers/' . $order->customer->photo) : asset('storage/customers/default.png') }}"
                                            alt="profile-pic">
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <div class="row align-items-center">
                            <div class="form-group col-md-12">
                                <label>Nombre del Vendedor</label>
                                <input type="text" class="form-control bg-white" value="{{ $order->user->name }}"
                                    readonly>
                            </div>
                            {{-- <div class="form-group col-md-6">
                                <label>Correo Electrónico del Cliente</label>
                                <input type="text" class="form-control bg-white" value="{{ $order->customer->email }}"
                                    readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Teléfono del Cliente</label>
                                <input type="text" class="form-control bg-white" value="{{ $order->customer->phone }}"
                                    readonly>
                            </div> --}}
                            <div class="form-group col-md-6">
                                <label>Fecha del Pedido</label>
                                <input type="text" class="form-control bg-white" value="{{ $order->order_date }}"
                                    readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label>No. de Factura</label>
                                <input class="form-control bg-white" id="buying_date" value="{{ $order->invoice_no }}"
                                    readonly />
                            </div>
                            <div class="form-group col-md-6">
                                <label>Estado del Pago</label>
                                <input class="form-control bg-white" id="expire_date" value="{{ $order->payment_status }}"
                                    readonly />
                            </div>
                            <div class="form-group col-md-6">
                                <label>Monto Pagado</label>
                                <input type="text" class="form-control bg-white"
                                    value="$ {{ number_format($order->pay, 2, ',', '.') }}" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Monto Pendiente</label>
                                <input type="text" class="form-control bg-white" value="{{ $order->due }}" readonly>
                            </div>
                        </div>

                        <!-- end: Show Data -->

                        @if ($order->order_status == 'Pendiente')
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="d-flex align-items-center list-action">
                                        <form action="{{ route('order.updateStatus') }}" method="POST"
                                            style="margin-bottom: 5px">
                                            @method('put')
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $order->id }}">
                                            <button type="submit" class="btn btn-success mr-2 border-none"
                                                data-toggle="tooltip" data-placement="top" title=""
                                                data-original-title="Completar">Completar Pedido</button>

                                            <a class="btn btn-danger mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="Cancelar"
                                                href="{{ route('order.pendingDue') }}">Cancelar</a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>


            <!-- end: Show Data -->
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="table mb-0">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>No.</th>
                                <th>Foto</th>
                                <th>Nombre del Producto</th>
                                <th>Código del Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach ($orderDetails as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <img class="avatar-60 rounded"
                                            src="{{ $item->product->product_image ? asset('storage/products/' . $item->product->product_image) : asset('storage/products/default.webp') }}">
                                    </td>
                                    <td>{{ $item->product->product_name }}</td>
                                    <td>{{ $item->product->product_code }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>$ {{ number_format($item->unitcost, 2, ',', '.') }}</td>
                                    <td>$ {{ number_format($item->total, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <!-- Page end  -->
    </div>

    @include('components.preview-img-form')
@endsection
