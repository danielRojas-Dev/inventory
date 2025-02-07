<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Electro DR</title>

    <!-- Favicon -->

    <link rel="shortcut icon" href="{{ asset('assets/images/login/logodr.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/backend-plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/backend.css?v=1.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/remixicon/fonts/remixicon.css') }}">
    <!-- Bootstrap CSS -->
    <!-- Bootstrap Bundle con Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    @yield('specificpagestyles')
    <style>
        /* Estilo para pantallas grandes */
        @media (max-width: 1920px) {
            body {
                font-size: 14px !important;
            }
        }

        /* Estilo para pantallas medianas (tabletas y pantallas más pequeñas) */
        @media (max-width: 768px) {
            body {
                font-size: 14px !important;
            }
        }

        /* Estilo para pantallas pequeñas (teléfonos) */
        @media (max-width: 480px) {
            body {
                font-size: 14px !important;
            }
        }

        /* Estilo para pantallas de entre 481px y 767px (tabletas en orientación vertical) */
        @media (min-width: 481px) and (max-width: 767px) {
            body {
                font-size: 14px !important;
            }
        }

        /* Estilo para pantallas muy grandes (monitores más grandes que 1920px) */
        @media (min-width: 1921px) {
            body {
                font-size: 16px !important;
            }
        }
    </style>

</head>


<body>
    <!-- loader Start -->
    {{-- <div id="loading">
        <div id="loading-center"></div>
    </div> --}}
    <!-- loader END -->

    <!-- Wrapper Start -->
    <div class="wrapper">
        @include('dashboard.body.sidebar')

        @include('dashboard.body.navbar')

        <div class="content-page">
            @yield('container')
        </div>
    </div>
    <!-- Wrapper End-->

    {{-- @include('dashboard.body.footer') --}}

    <!-- Backend Bundle JavaScript -->
    <script src="{{ asset('assets/js/backend-bundle.min.js') }}"></script>
    <script src="https://kit.fontawesome.com/4c897dc313.js" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/vendor/@fortawesome/fontawesome-free/js/all.min.js') }}"></script>

    @yield('specificpagescripts')

    <!-- App JavaScript -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>

</html>
