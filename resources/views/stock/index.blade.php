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
                        <div class="iq-alert-text">{{ session('success') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Listado de Productos</h4>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <form action="{{ route('order.stockManage') }}" method="get">
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
                            <div class="input-group col-sm-8">
                                <input type="text" id="search" class="form-control" name="search"
                                    placeholder="Search product" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text bg-primary"><i
                                            class="fas fa-search font-size-20"></i></button>
                                    <a href="{{ route('order.stockManage') }}" class="input-group-text bg-danger"><i
                                            class="fas fa-trash"></i></a>
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
                                <th>@sortablelink('product_name', 'Nombre')</th>
                                <th>@sortablelink('category.name', 'Categoria')</th>
                                {{-- <th>@sortablelink('supplier.name', 'Proveedor')</th> --}}
                                <th>@sortablelink('bulk_price', 'precio por Mayor')</th>
                                <th>@sortablelink('price_for_curves', 'precio por curva')</th>
                                <th>@sortablelink('product_store', 'stock')</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @forelse ($products as $product)
                                <tr>
                                    <td>{{ $products->currentPage() * 10 - 10 + $loop->iteration }}</td>
                                    <td>
                                        <img class="avatar-60 rounded"
                                            src="{{ $product->product_image ? asset('storage/products/' . $product->product_image) : asset('assets/images/product/default.webp') }}">
                                    </td>
                                    <td>{{ $product->product_name }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    {{-- <td>{{ $product->supplier->name }}</td> --}}
                                    <td>$ {{ number_format($product->bulk_price, 2, ',', '.') }}</td>
                                    <td>$ {{ number_format($product->price_for_curves, 2, ',', '.') }}</td>
                                    <td>
                                        <span class="btn btn-warning text-white mr-2">{{ $product->product_store }}</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                            style="margin-bottom: 5px">
                                            @method('delete')
                                            @csrf
                                            <div class="d-flex align-items-center list-action">

                                                <a class="btn btn-success mr-2" data-toggle="tooltip" data-placement="top"
                                                    title="" data-original-title="Editar"
                                                    href="{{ route('products.edit', $product->id) }}">Agregar Stock <i
                                                        class="far fa-plus mr-0"></i>
                                                </a>

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
                {{ $products->links() }}
            </div>
        </div>
        <!-- Page end  -->
    </div>
@endsection
