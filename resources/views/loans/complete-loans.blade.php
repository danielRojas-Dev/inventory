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
                        <h4 class="mb-3">Listado de Prestamos</h4>
                    </div>
                    <div>
                        <a href="{{ route('loan.createLoan') }}" class="btn btn-primary add-list">Agregar Prestamo</a>
                    </div>
                </div>
            </div>


            <div class="col-lg-12">
                <form action="{{ route('loan.completeLoans') }}" method="get">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        {{-- <div class="form-group row">
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
                        </div> --}}

                        {{-- <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center" for="search">Buscar:</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="text" id="search" class="form-control" name="search"
                                        placeholder="Buscar Cliente" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text bg-primary">
                                            <i class="fas fa-search font-size-20"></i>
                                        </button>
                                        <a href="{{ route('loan.completeLoans') }}" class="input-group-text bg-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
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
                                        {{-- <a class="btn btn-success mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="Imprimir"
                                                href="{{ route('order.invoiceDownload', $order->id) }}">
                                                Imprimir
                                            </a> --}}
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
                {{ $customers->links() }}
            </div>

        </div>
        <!-- Page end  -->
    </div>
@endsection
