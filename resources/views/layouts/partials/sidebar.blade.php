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

                <li class="nav-item">
                    <a href="{{ route('about') }}" class="nav-link">
                        <i class="nav-icon fas fa-info-circle"></i>
                        <p>About</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('contact') }}" class="nav-link">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>Contact</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
