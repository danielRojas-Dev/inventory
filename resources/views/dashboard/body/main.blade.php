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

    <!-- Estilos de DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.css" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.dataTables.js"></script>

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
    <script src="{{ asset('assets/vendor/@fortawesome/fontawesome-free/js/all.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            new DataTable('table', {
                responsive: true,
                columnDefs: [{
                        responsivePriority: 1,
                        targets: 0
                    },
                    {
                        responsivePriority: 2,
                        targets: -1
                    }, {
                        responsivePriority: 3,
                        targets: -2
                    }
                ],
                language: {

                    search: "Buscar:", // Texto para la barra de búsqueda
                    lengthMenu: "Mostrar _MENU_", // Texto para la opción de registros por página
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros", // Texto de información
                    infoEmpty: "No hay registros disponibles", // Texto cuando no hay resultados
                    infoFiltered: "(filtrado de _MAX_ registros en total)",
                    emptyTable: "No hay datos disponibles en la tabla", // Mensaje cuando no hay datos en la tabla
                    zeroRecords: "No se encontraron registros que coincidan", // Texto de los filtros
                    paginate: {
                        previous: "Anterior", // Texto del botón anterior
                        next: "Siguiente" // Texto del botón siguiente
                    }
                }
            });
        });
    </script>

    @yield('specificpagescripts')

    <!-- App JavaScript -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>

</html>
