@extends('layouts.admin')

@section('title', 'Panel de Control')

@section('content')
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $filmCount }}</h3>
                    <p>Películas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-film"></i>
                </div>
                <a href="{{ route('tablas', ['tipo' => 'peliculas']) }}" class="small-box-footer">Ver Películas <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $categoryCount }}</h3>
                    <p>Categorías</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tags"></i>
                </div>
                <a href="{{ route('tablas', ['tipo' => 'categorias']) }}" class="small-box-footer">Ver Categorías <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $actorCount }}</h3>
                    <p>Actores</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <a href="{{ route('tablas', ['tipo' => 'actores']) }}" class="small-box-footer">Ver Actores <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $customerCount }}</h3>
                    <p>Clientes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('tablas', ['tipo' => 'customers']) }}" class="small-box-footer">Ver Clientes <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>
    <!-- /.row -->

    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <section class="col-lg-7 connectedSortable">
            <!-- Films per category chart -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Películas por Categoría
                    </h3>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="chart">
                        <canvas id="categoryChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div><!-- /.card-body -->
                <div class="card-footer text-center">
                    <a href="{{ route('tablas', ['tipo' => 'categorias']) }}" class="uppercase">Ver Todas las Categorías</a>
                </div>
            </div>
            <!-- /.card -->

            <!-- Recently added films -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Películas Agregadas Recientemente</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Año</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentFilms as $film)
                            <tr>
                                <td>{{ $film->title }}</td>
                                <td>{{ $film->category_name }}</td>
                                <td>{{ $film->release_year }}</td>
                                <td>
                                    <a href="/edit/peliculas/{{ $film->film_id }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay películas disponibles</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('tablas', ['tipo' => 'peliculas']) }}" class="uppercase">Ver Todas las Películas</a>
                </div>
            </div>
        </section>
        <!-- /.Left col -->

        <!-- right col -->
        <section class="col-lg-5 connectedSortable">
            <!-- Top actors box -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actores Más Activos</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        @forelse($topActors as $actor)
                        <li class="item">
                            <div class="product-info">
                                <a href="{{ route('edit.item', ['itemType' => 'actores', 'itemId' => $actor->actor_id]) }}" class="product-title">
                                    {{ $actor->first_name }} {{ $actor->last_name }}
                                    <span class="badge badge-info float-right">{{ $actor->film_count }} películas</span>
                                </a>
                                <span class="product-description">
                                    ID Actor: {{ $actor->actor_id }}
                                </span>
                            </div>
                        </li>
                        @empty
                        <li class="item">
                            <div class="product-info">
                                No hay datos de actores disponibles
                            </div>
                        </li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('tablas', ['tipo' => 'actores']) }}" class="uppercase">Ver Todos los Actores</a>
                </div>
            </div>

            <!-- Languages distribution -->
            <div class="card bg-gradient-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-language mr-1"></i>
                        Idiomas y Cantidad de Películas
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-sm text-white">
                        <thead>
                            <tr>
                                <th>Idioma</th>
                                <th class="text-right">Películas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($languageStats as $language)
                            <tr>
                                <td>{{ $language->name }}</td>
                                <td class="text-right">{{ $language->film_count }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center">No hay datos de idiomas disponibles</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent text-center">
                    <a href="{{ route('tablas', ['tipo' => 'languages']) }}" class="text-white uppercase">Ver Todos los Idiomas</a>
                </div>
            </div>
        </section>
        <!-- right col -->
    </div>
    <!-- /.row (main row) -->
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Category distribution chart
        const categoryData = @json($filmsByCategory);

        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: categoryData.map(item => item.name),
                datasets: [{
                    label: 'Número de Películas',
                    data: categoryData.map(item => item.film_count),
                    backgroundColor: 'rgba(60,141,188,0.8)',
                    borderColor: 'rgba(60,141,188,1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
