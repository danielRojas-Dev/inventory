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
                        <h4 class="mb-3"> Lista de Usuario</h4>
                    </div>
                    <div>
                        <a href="{{ route('users.create') }}" class="btn btn-primary add-list">Crear Usuario</a>

                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <form action="{{ route('users.index') }}" method="get">
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
                                        placeholder="Buscar Usuario" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text bg-primary"><i
                                                class="fas fa-search font-size-20"></i></button>
                                        <a href="{{ route('users.index') }}" class="input-group-text bg-danger"><i
                                                class="fas fa-trash"></i></a>
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
                                <th>Foto</th>
                                <th>@sortablelink('name')</th>
                                <th>@sortablelink('username')</th>
                                <th>@sortablelink('email')</th>
                                <th>Rol</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @forelse ($users as $item)
                                <tr>
                                    <td>{{ $users->currentPage() * 10 - 10 + $loop->iteration }}</td>
                                    <td>
                                        <img class="avatar-60 rounded"
                                            src="{{ $item->photo ? asset('storage/profile/' . $item->photo) : asset('assets/images/user/1.png') }}">
                                    </td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->username }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>
                                        @foreach ($item->roles as $role)
                                            <span class="badge bg-danger">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <form action="{{ route('users.destroy', $item->username) }}" method="POST"
                                            style="margin-bottom: 5px">
                                            @method('delete')
                                            @csrf
                                            <div class="d-flex align-items-center list-action">
                                                {{-- <a class="btn btn-info mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"
                                            href="{{ route('users.show', $item->username) }}"><i class="ri-eye-line mr-0"></i>
                                        </a> --}}
                                                <a class="btn btn-success mr-2" data-toggle="tooltip" data-placement="top"
                                                    title="" data-original-title="Edit"
                                                    href="{{ route('users.edit', $item->username) }}"><i
                                                        class="ri-pencil-line mr-0"></i>
                                                </a>
                                                <button type="submit" class="btn btn-warning mr-2 border-none"
                                                    onclick="return confirm('Are you sure you want to delete this record?')"
                                                    data-toggle="tooltip" data-placement="top" title=""
                                                    data-original-title="Delete"><i
                                                        class="ri-delete-bin-line mr-0"></i></button>
                                            </div>
                                        </form>
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
                {{ $users->links() }}
            </div>
        </div>
        <!-- Page end  -->
    </div>

@endsection
