<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="dashboard" class="brand-link d-flex align-items-center justify-content-center">
        <img src="{{asset('dist/img/logo_utt.png')  }}" alt="Logo" class="elevation-3"
            style="opacity: .8;width: 80%;height: auto;">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Removed user panel section as requested -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- New Film Content Management Menu -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-film"></i>
                        <p>
                            Contenido
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('tablas', ['tipo' => 'peliculas']) }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Películas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('tablas', ['tipo' => 'categorias']) }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Categorías</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('tablas', ['tipo' => 'actores']) }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Actores</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- New User Management Menu -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Usuarios
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('tablas', ['tipo' => 'customers']) }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Clientes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('tablas', ['tipo' => 'address']) }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Direcciones</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Staff Management Menu (Only for Admins) -->
                @auth
                    @if(Auth::user()->rol_id == 1)
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>
                                Administración
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('tablas', ['tipo' => 'staff']) }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Personal</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                @endauth

                <li class="nav-item">
                    <a href="{{ route('contact') }}" class="nav-link">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>Contact</p>
                    </a>
                </li>

                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                @endguest

                @auth
                    <li class="nav-item">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                @endauth
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
