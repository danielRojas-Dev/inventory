@extends('auth.body.main')

@section('container')
    <div class="row align-items-center justify-content-center height-self-center">
        <div class="col-lg-9">
            <div class="card auth-card">
                <div class="card-body p-0">
                    <div class="d-flex align-items-center auth-content">
                        <div class="col-lg-7 align-self-center">
                            <div class="p-3">

                                <h2 class="mb-2">Iniciar sesión</h2>
                                <p>Inicia sesión para mantenerte conectado.</p>

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
                                        {{-- <div class="col-lg-6">
                                            <p>
                                                ¿No eres miembro? <a href="{{ route('register') }}"
                                                    class="text-primary">Regístrate</a>
                                            </p>
                                        </div> --}}
                                    </div>
                                    <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                                </form>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <img src="{{ asset('assets/images/login/MIA.png') }}"
                                style="width: 400px; height: auto; margin-right: 25%;" alt="">
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
