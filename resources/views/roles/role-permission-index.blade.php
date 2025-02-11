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
                    <h4 class="mb-3">Listado de Roles y Permisos</h4>
                    <a href="{{ route('rolePermission.create') }}" class="btn btn-primary add-list"><i
                            class="fas fa-plus mr-3"></i>Agregar Permiso a Rol</a>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table id="table" class="table table-hover" cellspacing="0">

                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th data-priority="1">Rol</th>
                                <th>Permisos</th>
                                <th data-priority="2">acciones</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        @foreach ($role->permissions as $permission)
                                            <span class="badge rounded-pill bg-danger">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <form action="{{ route('rolePermission.destroy', $role->id) }}" method="POST"
                                            style="margin-bottom: 5px">
                                            @method('delete')
                                            @csrf
                                            <div class="d-flex align-items-center list-action">
                                                <a class="btn btn-success btn-sm mr-2" data-toggle="tooltip"
                                                    data-placement="top" title="" data-original-title="Editar"
                                                    href="{{ route('rolePermission.edit', $role->id) }}"><i
                                                        class="ri-pencil-line mr-0"></i>
                                                </a>
                                                <button type="submit" class="btn btn-warning btn-sm mr-2 border-none"
                                                    onclick="return confirm('¿Esta seguro de eliminar estos roles y permisos')"
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
            </div>
        </div>
        <!-- Page end  -->
    </div>
@endsection
