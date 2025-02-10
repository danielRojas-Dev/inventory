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
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Listado de Permisos</h4>
                    </div>
                    <div>
                        <a href="{{ route('permission.create') }}" class="btn btn-primary add-list"><i
                                class="fas fa-plus mr-3"></i>Agregar Permiso</a>
                    </div>
                </div>
            </div>

            {{-- <div class="col-lg-12">
            <form action="{{ route('customers.index') }}" method="get">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div class="form-group row">
                        <label for="row" class="col-sm-3 align-self-center">Row:</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="row">
                                <option value="10" @if (request('row') == '10')selected="selected"@endif>10</option>
                                <option value="25" @if (request('row') == '25')selected="selected"@endif>25</option>
                                <option value="50" @if (request('row') == '50')selected="selected"@endif>50</option>
                                <option value="100" @if (request('row') == '100')selected="selected"@endif>100</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label col-sm-3 align-self-center" for="search">Search:</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" id="search" class="form-control" name="search" placeholder="Search customer" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text bg-primary"><i class="fa-solid fa-magnifying-glass font-size-20"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div> --}}

            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table id="table" class="table nowrap table-hover" cellspacing="0">

                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>Permiso</th>
                                <th>Grupo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td>{{ $permission->name }}</td>
                                    <td>{{ $permission->group_name }}</td>
                                    <td>
                                        <form action="{{ route('permission.destroy', $permission->id) }}" method="POST"
                                            style="margin-bottom: 5px">
                                            @method('delete')
                                            @csrf
                                            <div class="d-flex align-items-center list-action">
                                                <a class="btn btn-success mr-2" data-toggle="tooltip" data-placement="top"
                                                    title="" data-original-title="Editar"
                                                    href="{{ route('permission.edit', $permission->id) }}""><i
                                                        class="ri-pencil-line mr-0"></i>
                                                </a>
                                                <button type="submit" class="btn btn-warning mr-2 border-none"
                                                    onclick="return confirm('¿Esta seguro de eliminar este permiso?')"
                                                    data-toggle="tooltip" data-placement="top" title=""
                                                    data-original-title="Eliminar"><i
                                                        class="ri-delete-bin-line mr-0"></i></button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $permissions->links() }}
            </div>
        </div>
        <!-- Page end  -->
    </div>
@endsection
