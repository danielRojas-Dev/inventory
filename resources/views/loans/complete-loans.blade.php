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

                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
                    <h4 class="mb-3">Listado de Prestamos</h4>
                    <a href="{{ route('loan.createLoan') }}" class="btn btn-primary add-list"><i
                            class="fas fa-plus mr-3"></i>Agregar Prestamo</a>
                </div>
            </div>


            <div class="col-lg-12">
                <form action="{{ route('loan.completeLoans') }}" method="get">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                    </div>
                </form>
            </div>


            <div class="col-lg-12">
                <table id="table" class="table nowrap table-hover" cellspacing="0">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th class="ligth-data">Nombre</th>
                            <th class="ligth-data">Teléfono</th>
                            <th class="ligth-data">Ciudad</th>
                            <th class="ligth-data">Dirección</th>
                            <th class="ligth-data">Acción</th>

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
                                            href="{{ route('customer.customerLoanDetails', $customer->id) }}">
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
