@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @if (session()->has('success'))
                    <div class="alert text-white bg-success" role="alert">
                        <div class="iq-alert-text">{{ session('success') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Lista de Pedidos Completos</h4>
                    </div>

                </div>
            </div>


            <div class="col-lg-12">
                <form action="{{ route('order.completeOrders') }}" method="get">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="form-group row">
                            <label for="row" class="col-sm-3 align-self-center">Filas:</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="row">
                                    <option value="10" @if (request('row') == '10') selected="selected" @endif>10
                                    </option>
                                    <option value="25" @if (request('row') == '25') selected="selected" @endif>25
                                    </option>
                                    <option value="50" @if (request('row') == '50') selected="selected" @endif>50
                                    </option>
                                    <option value="100" @if (request('row') == '100') selected="selected" @endif>100
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center" for="search">Buscar:</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="text" id="search" class="form-control" name="search"
                                        placeholder="Buscar pedido" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text bg-primary">
                                            <i class="fas fa-search font-size-20"></i>
                                        </button>
                                        <a href="{{ route('order.completeOrders') }}" class="input-group-text bg-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>


            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="table mb-0">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>No.</th>
                                <th>No. de Factura</th>
                                <th>@sortablelink('user.name', 'Vendedor')</th>
                                <th>@sortablelink('order_date', 'Fecha del Pedido')</th>
                                <th>@sortablelink('pay', 'Pago')</th>
                                <th>Pago</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @forelse ($orders as $order)
                                <tr>
                                    <td>{{ $orders->currentPage() * 10 - 10 + $loop->iteration }}</td>
                                    <td>{{ $order->invoice_no }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ $order->order_date }}</td>
                                    <td>$ {{ number_format($order->pay, 2, ',', '.') }}</td>
                                    <td>{{ $order->payment_status }}</td>
                                    <td>
                                        <span class="badge badge-success">{{ $order->order_status }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <a class="btn btn-info mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="Detalles"
                                                href="{{ route('order.orderDetails', $order->id) }}">
                                                Detalles
                                            </a>
                                            <a class="btn btn-success mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="Imprimir"
                                                href="{{ route('order.invoiceDownload', $order->id) }}">
                                                Imprimir
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <div class="alert text-white bg-danger" role="alert">
                                    <div class="iq-alert-text">Datos no encontrados.</div>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $orders->links() }}
            </div>

        </div>
        <!-- Page end  -->
    </div>
@endsection
