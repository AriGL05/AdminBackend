@extends('layouts.admin')

@section('title', 'Add Category')

@section('content')
    <!-- Main content -->
    <section class="content">
    <div class="container-fluid">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Sobre la categoría:</h3>

            </div>
            <div class="card-body">
            <form method="POST" action="/categories">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title">Nombre de la categoría</label>
                            <input type="text" class="form-control" id="title" placeholder="">
                        </div>
                    </div>
                        <div class="col-md-6">
                                
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
        </div>
</section>

@endsection