<!--Alguien haga mono este diseño, esto es puro html pelon help, y q llame a la ruta de put de films-->
@extends('layouts.admin')

@section('title', 'About this film')

@section('content')
    <!-- Main content -->
    <section class="content">
    <div class="container-fluid">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title" style="font-weight:semi-bold">Sobre la película</h3>

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
                <form action="/films/{{ $film->film_id }}/edit">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="titulo">Título</label>
                            <input type="text" class="form-control" id="title" value="{{ $film->title }}">
                        </div>
                        <div class="form-group">
                            <label for="language">Idioma</label>
                            <select id="language_id" class="form-control select2" style="width: 100%;">
                            <option selected value="{{ $film->language_id }}"> {{ $language->name }} </option>
                            @foreach ($languages as $language)
                                <option value="{{ $language->language_id }}">{{ $language->name }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="language">Categoría/Género</label>
                            <select id="language_id" class="form-control select2" style="width: 100%;">
                            <option selected value="{{ $category->category_id }}"> {{ $category->name }} </option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="anio">Año de Lanzamiento</label>
                            <input type="number" class="form-control" id="release_year" value="{{ $film->release_year }}">
                        </div>
                        <div class="form-group">
                            <label for="duracion">Duración (minutos)</label>
                            <input type="number" class="form-control" id="length" value="{{ $film->length}}">
                        </div>
                        <div class="form-group">
                            <label for="sinopsis">Sinopsis</label>
                            <textarea class="form-control" id="description" rows="3" value="{{ $film->description }}">{{ $film->description }}</textarea>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Editar datos</button>
                </div>
            </form>

        </div>
    <div style="height: 15px">
    </div>
</section>
<!--
@if(isset($film))
    <h1>{{ $film->title }}</h1>
    <p>Release Year: {{ $film->release_year }}</p>
    <p>Length: {{ $film->length }}</p>
    <p>Description: {{ $film->description }}</p>
    @else
    <p>Film not found.</p>
@endif
-->
@endsection

