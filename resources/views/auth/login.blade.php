@extends('auth.body.main')

@section('container')
    <div class="row align-items-center justify-content-center min-vh-100">
        <div class="col-lg-4">
            <div class="card auth-card">
                <div class="card-body p-0">
                    <div class="d-flex align-items-center auth-content">
                        <div class="col-lg-12 align-self-center">
                            <div class=" text-center">
                                <img src="{{ asset('assets/images/login/logodr.png') }}" class="img-fluid"
                                    style="max-width: 40%; height: auto;" alt="">
                            </div>
                            <div class="">
                                <h2 class="mb-2 text-center">Iniciar sesión</h2>
                                <p class="text-center">Inicia sesión para mantenerte conectado.</p>
                                <form action="{{ route('login') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="floating-label form-group">
                                                <input
                                                    class="floating-input form-control @error('email') is-invalid @enderror @error('username') is-invalid @enderror"
                                                    type="text" name="input_type" placeholder=" "
                                                    value="{{ old('input_type') }}" autocomplete="off" required autofocus>
                                                <label>Email/Nombre de usuario</label>
                                            </div>
                                            @error('username')
                                                <div class="mb-4" style="margin-top: -20px">
                                                    <div class="text-danger small">Nombre de usuario o contraseña incorrectos.
                                                    </div>
                                                </div>
                                            @enderror
                                            @error('email')
                                                <div class="mb-4" style="margin-top: -20px">
                                                    <div class="text-danger small">Correo electrónico o contraseña incorrectos.
                                                    </div>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="floating-label form-group">
                                                <input
                                                    class="floating-input form-control @error('email') is-invalid @enderror @error('username') is-invalid @enderror"
                                                    type="password" name="password" placeholder=" " required>
                                                <label>Contraseña</label>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
