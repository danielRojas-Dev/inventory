<div class="iq-sidebar sidebar-default" style="background: #F7FDFF;">
    <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
        <a href="{{ route('dashboard') }}" class="header-logo">

            <h5 class="logo-title light-logo ml-3 text-center" style="color: #f8b6c4">MAYORISTA MIA </h5>
            {{-- <iclass="fas fa-shirt" style="font-size: 17px"></iclass=> --}}
        </a>
        <div class="iq-menu-bt-sidebar ml-0">
            <i class="las la-bars wrapper-menu"></i>
        </div>
    </div>
    <div class="data-scrollbar" data-scroll="1">
        <nav class="iq-sidebar-menu">
            <ul id="iq-sidebar-toggle" class="iq-menu">
                <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="svg-icon">
                        <svg class="svg-icon" id="p-dash1" width="20" height="20" style="color: #f8b6c4"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d=" M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1
                1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                            </path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                        <span class="ml-4">Inicio</span>
                    </a>
                </li>

                @if (auth()->user()->can('pos.menu'))
                    <li class="{{ Request::is('pos*') ? 'active' : '' }}">
                        <a href="{{ route('pos.index') }}" class="svg-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="ml-3">Ventas</span>
                        </a>
                    </li>
                @endif


                <hr>

                @if (auth()->user()->can('orders.menu'))
                    <li>
                        <a href="#orders" class="collapsed" data-toggle="collapse" aria-expanded="false">
                            <i class="fas fa-shopping-basket"></i>
                            <span class="ml-3">Pedidos</span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline>
                                <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>
                        <ul id="orders" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle" style="">
                            {{-- <li class="{{ Request::is('orders/pending*') ? 'active' : '' }}">
                                <a href="{{ route('order.pendingOrders') }}">
                                    <i class="fas fa-arrow-right"></i><span>Pedidos Pendientes</span>
                                </a>
                            </li> --}}
                            <li class="{{ Request::is('pending/due*') ? 'active' : '' }}">
                                <a href="{{ route('order.pendingDue') }}">
                                    <i class="fas fa-arrow-right"></i><span>Pedidos Pendientes</span>
                                </a>
                            </li>
                            <li class="{{ Request::is('orders/complete*') ? 'active' : '' }}">
                                <a href="{{ route('order.completeOrders') }}">
                                    <i class="fas fa-arrow-right"></i><span>Pedidos Completados</span>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endif


                @if (auth()->user()->can('product.menu'))
                    <li>
                        <a href="#products" class="collapsed" data-toggle="collapse" aria-expanded="false">
                            <i class="fas fa-boxes"></i>
                            <span class="ml-3">Productos</span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline>
                                <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>
                        <ul id="products" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle" style="">
                            <li class="{{ Request::is(['products']) ? 'active' : '' }}">
                                <a href="{{ route('products.index') }}">
                                    <i class="fas fa-arrow-right"></i><span>Productos</span>
                                </a>
                            </li>
                            <li class="{{ Request::is(['products/create']) ? 'active' : '' }}">
                                <a href="{{ route('products.create') }}">
                                    <i class="fas fa-arrow-right"></i><span>Agregar Producto</span>
                                </a>
                            </li>
                            {{-- <li class="{{ Request::is(['stock*']) ? 'active' : '' }}">
                                <a href="{{ route('order.stockManage') }}">
                                    <i class="fas fa-arrow-right"></i><span>Gestión de Stock</span>
                                </a>
                            </li> --}}
                            <li class="{{ Request::is(['categories*']) ? 'active' : '' }}">
                                <a href="{{ route('categories.index') }}">
                                    <i class="fas fa-arrow-right"></i><span>Categorías</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif


                <hr>

                @if (auth()->user()->can('employee.menu'))
                    <li class="{{ Request::is('employees*') ? 'active' : '' }}">
                        <a href="{{ route('employees.index') }}" class="svg-icon">
                            <i class="fas fa-users"></i>
                            <span class="ml-3">Empleados</span>
                        </a>
                    </li>
                @endif

                {{-- @if (auth()->user()->can('customer.menu'))
                    <li class="{{ Request::is('customers*') ? 'active' : '' }}">
                        <a href="{{ route('customers.index') }}" class="svg-icon">
                            <i class="fas fa-users"></i>
                            <span class="ml-3">Clientes</span>
                        </a>
                    </li>
                @endif --}}

                {{-- @if (auth()->user()->can('supplier.menu'))
                    <li class="{{ Request::is('suppliers*') ? 'active' : '' }}">
                        <a href="{{ route('suppliers.index') }}" class="svg-icon">
                            <i class="fas fa-users"></i>
                            <span class="ml-3">Proveedores</span>
                        </a>
                    </li>
                @endif --}}


                {{-- @if (auth()->user()->can('salary.menu'))
                    <li>
                        <a href="#advance-salary" class="collapsed" data-toggle="collapse" aria-expanded="false">
                            <i class="fas fa-cash-register"></i>
                            <span class="ml-3">Sueldos</span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline>
                                <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>
                        <ul id="advance-salary" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle"
                            style="">

                            <li class="{{ Request::is('pay-salary') ? 'active' : '' }}">
                                <a href="{{ route('pay-salary.index') }}">
                                    <i class="fas fa-arrow-right"></i><span>Sueldos</span>
                                </a>
                            </li>
                            <li
                                class="{{ Request::is(['advance-salary', 'advance-salary/*/edit']) ? 'active' : '' }}">
                                <a href="{{ route('advance-salary.index') }}">
                                    <i class="fas fa-arrow-right"></i><span>Anticipos de Sueldo</span>
                                </a>
                            </li>
                            <li class="{{ Request::is('advance-salary/create*') ? 'active' : '' }}">
                                <a href="{{ route('advance-salary.create') }}">
                                    <i class="fas fa-arrow-right"></i><span>Crear Anticipo de Sueldo</span>
                                </a>
                            </li>
                            <li class="{{ Request::is('pay-salary/history*') ? 'active' : '' }}">
                                <a href="{{ route('pay-salary.payHistory') }}">
                                    <i class="fas fa-arrow-right"></i><span>Historial de Pagos</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif --}}


                @if (auth()->user()->can('attendence.menu'))
                    {{-- <li> --}}
                    {{-- <a href="#attendence" class="collapsed" data-toggle="collapse" aria-expanded="false">
                            <i class="fas fa-calendar"></i>
                            <span class="ml-3">Asistencia</span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline>
                                <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>
                        <ul id="attendence" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle"
                            style=""> --}}

                    <li class="{{ Request::is(['employee/attendence']) ? 'active' : '' }} svg-icon">
                        <a href="{{ route('attendence.index') }}">
                            <i class="fas fa-calendar"></i><span class="ml-3">Asistencia</span>
                        </a>
                    </li>

                    {{-- <li class="{{ Request::is('employee/attendence/*') ? 'active' : '' }}">
                                <a href="{{ route('attendence.create') }}">
                                    <i class="fas fa-arrow-right"></i><span>Crear Asistencia</span>
                                </a>
                            </li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}
                @endif


                <hr>


                @if (auth()->user()->can('roles.menu'))
                    <li>
                        <a href="#permission" class="collapsed" data-toggle="collapse" aria-expanded="false">
                            <i class="fas fa-key"></i>
                            <span class="ml-3">Roles y Permisos</span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline>
                                <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>
                        <ul id="permission" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle"
                            style="">
                            <li class="{{ Request::is(['role', 'role/create', 'role/edit/*']) ? 'active' : '' }}">
                                <a href="{{ route('role.index') }}">
                                    <i class="fas fa-arrow-right"></i><span>Roles</span>
                                </a>
                            </li>
                            <li
                                class="{{ Request::is(['permission', 'permission/create', 'permission/edit/*']) ? 'active' : '' }}">
                                <a href="{{ route('permission.index') }}">
                                    <i class="fas fa-arrow-right"></i><span>Permisos</span>
                                </a>
                            </li>
                            <li class="{{ Request::is(['role/permission*']) ? 'active' : '' }}">
                                <a href="{{ route('rolePermission.index') }}">
                                    <i class="fas fa-arrow-right"></i><span>Roles y Permisos</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif


                @if (auth()->user()->can('user.menu'))
                    <li class="{{ Request::is('users*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" class="svg-icon">
                            <i class="fas fa-users"></i>
                            <span class="ml-3">Usuarios</span>
                        </a>
                    </li>
                @endif

                <hr>


                {{-- @if (auth()->user()->can('database.menu'))
                    <li class="{{ Request::is('database/backup*') ? 'active' : '' }}">
                        <a href="{{ route('backup.index') }}" class="svg-icon">
                            <i class="fas fa-database"></i>
                            <span class="ml-3">Respaldo de Base de Datos</span>
                        </a>
                    </li>
                @endif --}}

            </ul>
        </nav>
        <div class="p-3"></div>
    </div>
</div>
