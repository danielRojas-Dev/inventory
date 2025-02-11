@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @if (session()->has('error'))
                    <div class="alert text-white bg-danger" role="alert">
                        <div class="iq-alert-text">{{ session('error') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif
                @if (session()->has('success'))
                    <div class="alert text-white bg-success" role="alert">
                        <div class="iq-alert-text">{!! session('success') !!}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif

                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Listado de Ventas</h4>
                    </div>

                </div>
            </div>


            <div class="col-lg-12">
                <form action="{{ route('order.completeOrders') }}" method="get">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                    </div>
                </form>
            </div>


            <div class="col-lg-12">
                <table id="table" class=" display nowrap" style="width:100%">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th class="ligth-data" data-priority="1">Nombre</th>
                            <th class="ligth-data">Teléfono</th>
                            <th class="ligth-data">Ciudad</th>
                            <th class="ligth-data">Dirección</th>
                            <th class="ligth-data" data-priority="2">Acción</th>

                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @forelse ($customers as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ $customer->city }}</td>
                                <td>{{ $customer->address }}</td>
                                <td>
                                    <div class="d-flex align-items-center list-action">
                                        <a class="btn btn-info mr-2" data-toggle="tooltip" data-placement="top"
                                            title="" data-original-title="Detalles"
                                            href="{{ route('customer.customerDetails', $customer->id) }}">
                                            Detalles
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

        </div>
        <!-- Page end  -->
    </div>
@endsection
