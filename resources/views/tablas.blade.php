@extends('layouts.admin')

@section('title', 'Tablas')

@section('content')
<div class="wrapper">
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Todos los objetos de cada apartado (ya sea Peliculas, actores, categorias, etc)</h3>
<!--Aqui deberiamos validar q ruta usar, en caso de que el objeto que pasamos sea film, actor o category, pongo este de film por si acaso-->
                <a href="{{ route('newfilm') }}" class="btn btn-success btn-sm" style="float: right;"><i class="fas fa-plus"></i> Agregar</a>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example2" class="table table-bordered table-hover">
                  <thead>

                  <tr>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Año</th>
                    <th>Duración</th>
                    <th>Options</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td>Trident</td>
                    <td>Internet
                      Explorer 4.0
                    </td>
                    <td>Win 95+</td>
                    <td> 4</td>
                    <td>
                    <button class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
                    </td>
                  </tr>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>

@endsection