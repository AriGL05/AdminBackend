@extends('layouts.admin')

@section('title', 'Add Film')

@section('content')
    <!-- Main content -->
    <section class="content">
    <div class="container-fluid">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Información de la Película:</h3>

                <div class="card-tools">
                    <!--
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                    -->
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="titulo">Título</label>
                            <input type="text" class="form-control" id="titulo" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="genero">Categoría/Género</label>
                            <input type="text" class="form-control" id="genero" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="anio">Idioma</label>
                            <select class="form-control select2" style="width: 100%;">
                            <!--Lista de idiomas de la bd
                            foreach (languages as language)
                            <option value=language.language_id >language.name</option>
                            -->
                                <option selected="selected">idioma 1</option>
                                <option>idioma 2</option>
                                <option>idioma 3</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="anio">Categoria</label>
                                <select class="form-control select2" style="width: 100%;">
                                 <!--Lista de categorias de la bd
                                foreach (categories as category)
                                <option value=category.category_id >category.name</option>
                                -->
                                    <option selected="selected">categoria 1</option>
                                    <option>categoria 2</option>
                                    <option>categoria 3</option>
                                </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="anio">Año de Lanzamiento</label>
                            <input type="number" class="form-control" id="anio" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="duracion">Duración (minutos)</label>
                            <input type="number" class="form-control" id="duracion" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="sinopsis">Sinopsis</label>
                            <textarea class="form-control" id="sinopsis" rows="3" placeholder="Sobre esta película.."></textarea>
                        </div>
                    </div>
                    </div>
                </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </div>
        </div>
</section>

@endsection