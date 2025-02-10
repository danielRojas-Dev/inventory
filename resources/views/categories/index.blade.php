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
                        <h4 class="mb-3">Listado de Categorias</h4>

                    </div>
                    <div>
                        <a href="{{ route('categories.create') }}" class="btn btn-primary add-list">Agregar Categoria</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <form action="{{ route('categories.index') }}" method="get">
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
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center" for="search">Buscar:</label>
                            <div class="input-group col-sm-8">
                                <input type="text" id="search" class="form-control" name="search"
                                    placeholder="Buscar Categoria" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text bg-primary"><i
                                            class="fas fa-search font-size-20"></i></button>
                                    <a href="{{ Route('categories.index') }}" class="input-group-text bg-danger"><i
                                            class="fas fa-trash"></i></a>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </form>
            </div>

            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table id="table" class="table nowrap table-hover" cellspacing="0">

                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>Nombre</th>
                                <th>slug</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @forelse ($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->slug }}</td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="Edit"
                                                href="{{ route('categories.edit', $category->slug) }}""><i
                                                    class="ri-pencil-line mr-0"></i>
                                            </a>
                                            <form action="{{ route('categories.destroy', $category->slug) }}" method="POST"
                                                style="margin-bottom: 5px">
                                                @method('delete')
                                                @csrf
                                                <button type="submit" class="badge bg-warning mr-2 border-none"
                                                    onclick="return confirm('¿Estas seguro de eliminar esta categoria?')"
                                                    data-toggle="tooltip" data-placement="top" title=""
                                                    data-original-title="Delete"><i
                                                        class="ri-delete-bin-line mr-0"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <div class="alert text-white bg-danger" role="alert">
                                    <div class="iq-alert-text">Datos no encontrados</div>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
@endsection
