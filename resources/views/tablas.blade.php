@extends('layouts.admin')

@section('title')
    @if($tipo == 'peliculas')
        Películas
    @elseif($tipo == 'categorias')
        Categorías
    @elseif($tipo == 'actores')
        Actores
    @else
        Tablas
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <a href="{{ route('newfilm') }}" class="btn btn-success btn-sm" style="float: right;"><i class="fas fa-plus"></i> Agregar</a>
                <div class="card-header">
                    <h3 class="card-title">
                        @if($tipo == 'peliculas')
                            Listado de Películas
                        @elseif($tipo == 'categorias')
                            Listado de Categorías
                        @elseif($tipo == 'actores')
                            Listado de Actores
                        @else
                            Seleccione una opción del menú lateral
                        @endif
                    </h3>

                    @if($tipo)
                        <div class="card-tools">
                            <button type="button" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Agregar
                                @if($tipo == 'peliculas') Película
                                @elseif($tipo == 'categorias') Categoría
                                @elseif($tipo == 'actores') Actor
                                @endif
                            </button>
                        </div>
                    @endif
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    @if($tipo && !empty($data))
                        <table class="table table-bordered table-striped dataTable">
                            <thead>
                                <tr>
                                    @foreach($columns as $key => $label)
                                        <th>{{ $label }}</th>
                                    @endforeach
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                    <tr>
                                        @foreach($columns as $key => $label)
                                            <td>
                                                @if($key == 'duracion' && $tipo == 'peliculas')
                                                    {{ $item[$key] }} min
                                                @elseif($key == 'cantidad_peliculas' && $tipo == 'categorias')
                                                    {{ $item[$key] }} películas
                                                @elseif($key == 'peliculas' && $tipo == 'actores')
                                                    {{ $item[$key] }} películas
                                                @else
                                                    {{ $item[$key] }}
                                                @endif
                                            </td>
                                        @endforeach
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                            <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                            <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @elseif($tipo)
                        <div class="alert alert-info">
                            No hay datos disponibles para mostrar.
                        </div>
                    @else
                        <div class="alert alert-info">
                            Por favor, seleccione una opción del menú lateral (Películas, Categorías o Actores).
                        </div>
                    @endif
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection

@section('scripts')
<script>
    $(function () {
        $('.dataTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            }
        });
    });
</script>
@endsection
