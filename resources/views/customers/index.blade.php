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
                @if (session()->has('error'))
                    <div class="alert text-white bg-danger" role="alert">
                        <div class="iq-alert-text">{{ session('error') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
                    <h4 class="mb-3">Listado de Clientes</h4>
                    <a href="{{ route('customers.create') }}" class="btn btn-primary add-list"><i
                            class="fas fa-plus mr-3"></i>Agregar Cliente</a>
                </div>
            </div>

            <div class="col-lg-12">
                <form action="{{ route('customers.index') }}" method="get">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                    </div>
                </form>
            </div>

            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table id="table" class="table nowrap table-hover" cellspacing="0">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>Photo</th>
                                <th>Nombre</th>
                                <th>phone</th>
                                <th>Direccion</th>
                                <th>Ciudad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach ($customers as $customer)
                                <tr>
                                    <td>
                                        <img class="avatar-60 rounded"
                                            src="{{ $customer->photo ? asset('storage/customers/' . $customer->photo) : asset('assets/images/user/1.png') }}">
                                    </td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ $customer->address }}</td>
                                    <td>{{ $customer->city }}</td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="Edit"
                                                href="{{ route('customers.edit', $customer->id) }}""><i
                                                    class="ri-pencil-line mr-0"></i>
                                            </a>
                                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST"
                                                style="margin-bottom: 5px">
                                                @method('delete')
                                                @csrf
                                                <button type="submit" class="badge bg-warning mr-2 border-none"
                                                    onclick="return confirm('¿Estas seguro de eliminar este cliente?')"
                                                    data-toggle="tooltip" data-placement="top" title=""
                                                    data-original-title="Delete"><i
                                                        class="ri-delete-bin-line mr-0"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
@endsection
