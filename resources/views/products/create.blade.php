@extends('dashboard.body.main')

@section('specificpagestyles')
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Añadir Producto</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <!-- begin: Input Image -->
                            <div class="form-group row align-items-center">
                                <div class="col-md-12">
                                    <div class="profile-img-edit">
                                        <div class="crm-profile-img-edit">
                                            <img class="crm-profile-pic rounded-circle avatar-100" id="image-preview"
                                                src="{{ asset('assets/images/product/default.webp') }}" alt="profile-pic">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="input-group mb-4 col-lg-6">
                                    <div class="custom-file">
                                        <input type="file"
                                            class="custom-file-input @error('product_image') is-invalid @enderror"
                                            id="image" name="product_image" accept="image/*" onchange="previewImage();">
                                        <label class="custom-file-label" for="product_image">Elegir Archivo</label>
                                    </div>
                                    @error('product_image')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <!-- end: Input Image -->
                            <!-- begin: Input Data -->
                            <div class="row align-items-center">
                                <div class="form-group col-md-12">
                                    <label for="product_name">Nombre del Producto <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('product_name') is-invalid @enderror"
                                        id="product_name" name="product_name" value="{{ old('product_name') }}" required>
                                    @error('product_name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="category_id">Categoría <span class="text-danger">*</span></label>
                                    <select class="form-control" name="category_id" required>
                                        <option selected="" disabled>-- Seleccionar Categoría --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                {{-- <div class="form-group col-md-6">
                                    <label for="supplier_id">Proveedor <span class="text-danger">*</span></label>
                                    <select class="form-control" name="supplier_id" required>
                                        <option selected="" disabled>-- Seleccionar Proveedor --</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                                {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div> --}}
                                <div class="form-group col-md-6">
                                    <label for="product_garage">Almacén del Producto</label>
                                    <input type="text" class="form-control @error('product_garage') is-invalid @enderror"
                                        id="product_garage" name="product_garage" value="{{ old('product_garage') }}">
                                    @error('product_garage')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="product_store">Cantidad del Producto</label>
                                    <input type="text" class="form-control @error('product_store') is-invalid @enderror"
                                        id="product_store" name="product_store" value="{{ old('product_store') }}">
                                    @error('product_store')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="buying_date">Fecha de Compra</label>
                                    <input id="buying_date" class="form-control @error('buying_date') is-invalid @enderror"
                                        name="buying_date" value="{{ old('buying_date') }}" />
                                    @error('buying_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                {{-- <div class="form-group col-md-6">
                                    <label for="expire_date">Fecha de Expiración</label>
                                    <input id="expire_date" class="form-control @error('expire_date') is-invalid @enderror"
                                        name="expire_date" value="{{ old('expire_date') }}" />
                                    @error('expire_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div> --}}
                                <div class="form-group col-md-6">
                                    <label for="buying_price">Precio de Compra <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('buying_price') is-invalid @enderror"
                                        id="buying_price" name="buying_price" value="{{ old('buying_price') }}" required>
                                    @error('buying_price')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="bulk_price">Precio por Mayor <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('bulk_price') is-invalid @enderror"
                                        id="bulk_price" name="bulk_price" value="{{ old('bulk_price') }}" required>
                                    @error('bulk_price')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="price_for_curves">Precio por Curva <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('price_for_curves') is-invalid @enderror"
                                        id="price_for_curves" name="price_for_curves"
                                        value="{{ old('price_for_curves') }}" required>
                                    @error('price_for_curves')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <!-- end: Input Data -->
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary mr-2">Guardar</button>
                                <a class="btn bg-danger" href="{{ route('products.index') }}">Cancelar</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>

    <script>
        $('#buying_date').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
            // https://gijgo.com/datetimepicker/configuration/format
        });
        $('#expire_date').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
            // https://gijgo.com/datetimepicker/configuration/format
        });
    </script>

    @include('components.preview-img-form')
@endsection
