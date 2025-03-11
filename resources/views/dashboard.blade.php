@extends('layouts.admin')

@section('title', 'Dashboard de Películas')

@section('content')
<!-- Info boxes -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-4">
        <a href="{{ route('tablas', ['tipo' => 'peliculas']) }}" class="text-dark">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-film"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Películas</span>
                    <span class="info-box-number">{{ $dashboardData['counts']['peliculas'] }}</span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <a href="{{ route('tablas', ['tipo' => 'categorias']) }}" class="text-dark">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-list"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Categorías</span>
                    <span class="info-box-number">{{ $dashboardData['counts']['categorias'] }}</span>
                </div>
            </div>
        </a>
    </div>

    <div class="col-12 col-sm-6 col-md-4">
        <a href="{{ route('tablas', ['tipo' => 'actores']) }}" class="text-dark">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Actores</span>
                    <span class="info-box-number">{{ $dashboardData['counts']['actores'] }}</span>
                </div>
            </div>
        </a>
    </div>
</div>
<!-- /.row -->

<div class="row">
    <div class="col-md-8">
        <!-- Recent Movies -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Películas Recientes</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Año</th>
                                <th>Idioma</th>
                                <th>Duración</th>
                                <th>Categoría</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dashboardData['recentMovies'] as $movie)
                            <tr>
                                <td>
                                    <a href="{{ route('tablas', ['tipo' => 'peliculas']) }}">
                                        {{ $movie['titulo'] }}
                                    </a>
                                </td>
                                <td>{{ $movie['anio'] }}</td>
                                <td>{{ $movie['idioma_original'] }}</td>
                                <td>{{ $movie['duracion'] }} min</td>
                                <td><span class="badge badge-info">{{ $movie['categoria'] }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer text-center">
                <a href="{{ route('tablas', ['tipo' => 'peliculas']) }}" class="uppercase">Ver Todas Las Películas</a>
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->

        <!-- Charts -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Estadísticas de Películas</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="chart-responsive">
                            <canvas id="categoryChart" height="200"></canvas>
                        </div>
                        <h4 class="text-center mt-3">Películas por Categoría</h4>
                    </div>
                    <!-- /.col -->
                    <div class="col-md-6">
                        <div class="chart-responsive">
                            <canvas id="yearlyChart" height="200"></canvas>
                        </div>
                        <h4 class="text-center mt-3">Películas por Año</h4>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->

    <div class="col-md-4">
        <!-- Categories Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top Categorías</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach(array_slice($dashboardData['categoryDistribution']['labels'], 0, 5) as $index => $category)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $category }}
                            <span class="badge badge-primary badge-pill">{{ $dashboardData['categoryDistribution']['data'][$index] }} películas</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <!-- /.card-body -->
            <div class="card-footer text-center">
                <a href="{{ route('tablas', ['tipo' => 'categorias']) }}">Ver Todas Las Categorías</a>
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->

        <!-- Quick Actions Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Acciones Rápidas</h3>
            </div>
            <div class="card-body">
                <div class="btn-group w-100 mb-2">
                    <a href="{{ route('tablas', ['tipo' => 'peliculas']) }}" class="btn btn-info">
                        <i class="fas fa-film"></i> Gestionar Películas
                    </a>
                </div>
                <div class="btn-group w-100 mb-2">
                    <a href="{{ route('tablas', ['tipo' => 'categorias']) }}" class="btn btn-danger">
                        <i class="fas fa-list"></i> Gestionar Categorías
                    </a>
                </div>
                <div class="btn-group w-100">
                    <a href="{{ route('tablas', ['tipo' => 'actores']) }}" class="btn btn-success">
                        <i class="fas fa-user-alt"></i> Gestionar Actores
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
@endsection

@section('scripts')
<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function () {
        // Category Distribution Chart
        var categoryData = @json($dashboardData['categoryDistribution']);
        var categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryData.labels,
                datasets: [{
                    data: categoryData.data,
                    backgroundColor: [
                        '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', '#6f42c1', '#e83e8c'
                    ],
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
            }
        });

        // Yearly Releases Chart
        var yearlyData = @json($dashboardData['yearlyReleases']);
        var yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
        new Chart(yearlyCtx, {
            type: 'bar',
            data: {
                labels: yearlyData.labels,
                datasets: [{
                    label: 'Películas por año',
                    data: yearlyData.data,
                    backgroundColor: '#00a65a',
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection
