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
                    <h4 class="mb-3">Listado de Categorias</h4>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary add-list"><i
                            class="fas fa-plus mr-3"></i>Agregar Categoria</a>
                </div>
            </div>

            <div class="col-lg-12">
                <form action="{{ route('categories.index') }}" method="get">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                    </div>
                </form>
            </div>

            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table id="table" class=" display nowrap" style="width:100%">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th data-priority="1">Nombre</th>
                                <th>slug</th>
                                <th data-priority="2">Acciones</th>
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
