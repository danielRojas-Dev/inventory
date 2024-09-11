@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                @if (session()->has('success'))
                    <div class="alert text-white bg-success" role="alert">
                        <div class="iq-alert-text">{{ session('success') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @elseif(session()->has('error'))
                    <div class="alert text-white bg-danger" role="alert">
                        <div class="iq-alert-text">{{ session('error') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif

                <div>
                    <h4 class="mb-3">Punto de Venta</h4>
                </div>
            </div>


            <div class="col-lg-5 col-md-12 mb-3">
                <table class="table">
                    <thead>
                        <tr class="ligth">
                            <th scope="col">Nombre</th>
                            <th scope="col">Cantidad</th>
                            <th scope="col">Precio</th>
                            <th scope="col">Subtotal</th>
                            <th scope="col">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productItem as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td style="max-width: 140px;">
                                    <form action="{{ route('pos.updateCart', $item->rowId) }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="qty" required
                                                value="{{ old('qty', $item->qty) }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-success border-none"
                                                    data-toggle="tooltip" data-placement="top" title=""
                                                    data-original-title="Actualizar"><i class="fas fa-check"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                <td>$ {{ number_format($item->price, 2, ',', '.') }}</td>
                                <td>$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('pos.deleteCart', $item->rowId) }}" class="btn btn-danger border-none"
                                        data-toggle="tooltip" data-placement="top" title=""
                                        data-original-title="Eliminar"><i class="fa-solid fa-trash mr-0"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


                <div class="container row text-center">
                    <div class="form-group col-sm-6">
                        <p class="h4 text-primary">Cantidad: {{ Cart::count() }}</p>
                    </div>
                    <div class="form-group col-sm-6">
                        <p class="h4 text-primary">Subtotal: $ {{ number_format(Cart::subtotal(), 2, ',', '.') }}</p>
                    </div>
                    {{-- <div class="form-group col-sm-6">
                        <p class="h4 text-primary">IVA: {{ Cart::tax() }}</p>
                    </div> --}}
                    <div class="form-group col-sm-6">
                        <p class="h4 text-primary">Total: $ {{ number_format(Cart::total(), 2, ',', '.') }}</p>
                    </div>
                </div>


                <form action="{{ route('pos.createInvoice') }}" method="POST">
                    @csrf
                    <div class="row mt-3">
                        {{-- <div class="col-md-12">
                            <div class="input-group">
                                <select class="form-control" id="customer_id" name="customer_id">
                                    <option selected="" disabled="">-- Seleccionar Cliente --</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('customer_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div> --}}
                        <div class="col-md-12 mt-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-center">
                                {{-- <a href="{{ route('customers.create') }}" class="btn btn-primary add-list mx-1">Agregar
                                    Cliente</a> --}}
                                <button type="submit" class="btn btn-success add-list mx-1" style="width: 100%"
                                    {{ Cart::count() == 0 ? 'disabled' : '' }}>
                                    Crear Factura
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>

            <div class="col-lg-7 col-md-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <form action="#" method="get">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <div class="form-group row">
                                    <label for="row" class="align-self-center mx-2">Filas:</label>
                                    <div>
                                        <select class="form-control" name="row">
                                            <option value="10"
                                                @if (request('row') == '10') selected="selected" @endif>10</option>
                                            <option value="25"
                                                @if (request('row') == '25') selected="selected" @endif>25</option>
                                            <option value="50"
                                                @if (request('row') == '50') selected="selected" @endif>50</option>
                                            <option value="100"
                                                @if (request('row') == '100') selected="selected" @endif>100</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="control-label col-sm-3 align-self-center" for="search">Buscar:</label>
                                    <div class="input-group col-sm-8">
                                        <input type="text" id="search" class="form-control" name="search"
                                            placeholder="Buscar producto" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="input-group-text bg-primary">
                                                <i class="fa-solid fa-magnifying-glass font-size-20"></i>
                                            </button>
                                            <a href="{{ route('products.index') }}" class="input-group-text bg-danger">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>



                        <div class="table-responsive rounded mb-3 border-none">
                            <table class="table mb-0">
                                <thead class="bg-white text-uppercase">
                                    <tr class="ligth ligth-data">
                                        <th>Foto</th>
                                        <th>@sortablelink('product_name', 'Nombre')</th>
                                        <th>@sortablelink('bulk_price', 'Precio por Mayor')</th>
                                        <th>@sortablelink('price_for_curves', 'Precio por Curva')</th>
                                        <th>@sortablelink('product_store', 'Stock')</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="ligth-body">
                                    @forelse ($products as $product)
                                        <tr>
                                            <td>
                                                <img class="avatar-60 rounded"
                                                    src="{{ $product->product_image ? asset('storage/products/' . $product->product_image) : asset('assets/images/product/default.webp') }}">
                                            </td>
                                            <td>{{ $product->product_name }}</td>
                                            <td>$
                                                {{ number_format($product->bulk_price, 2, ',', '.') }}</td>
                                            <td>$
                                                {{ number_format($product->price_for_curves, 2, ',', '.') }}</td>
                                            <td>
                                                <span id="stock-{{ $product->id }}"
                                                    class="btn btn-warning text-white mr-2">{{ $product->product_store }}</span>
                                            </td>

                                            <td>
                                                <form action="{{ route('pos.addCart') }}" method="POST"
                                                    class="add-to-cart-form" data-product-id="{{ $product->id }}"
                                                    style="margin-bottom: 5px">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                                    <input type="hidden" name="name"
                                                        value="{{ $product->product_name }}">

                                                    <!-- Selector para el precio -->
                                                    <div class="product-options"
                                                        style="display: flex;align-items: center;gap: 10px;">
                                                        <select name="price" class="form-control mb-2" required>
                                                            <option value="{{ $product->bulk_price }}">Mayor</option>
                                                            <option value="{{ $product->price_for_curves }}">Curva
                                                            </option>
                                                        </select>

                                                        @if ($product->product_store == 0)
                                                            <button type="submit" disabled
                                                                class="btn btn-primary border-none" data-toggle="tooltip"
                                                                data-placement="top" title="Agregar">
                                                                <i class="far fa-plus mr-0"></i>
                                                            </button>
                                                        @else
                                                            <button type="submit"
                                                                class="btn btn-primary border-none add-to-cart-btn"
                                                                data-product-stock="{{ $product->product_store }}">
                                                                <i class="far fa-plus mr-0"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <div class="alert text-white bg-danger" role="alert">
                                            <div class="iq-alert-text">Datos no encontrados.</div>
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Cerrar">
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
            </div>
        </div>
    </div>
@endsection
