@extends('layouts.admin')

@section('title', 'Add Actor')

@section('content')
    <!-- Main content -->
    <section class="content">
    <div class="container-fluid">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Información del Actor:</h3>

                <div class="card-tools">
            
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="/actors">
                @csrf
                @method('POST')
                  <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name">Nombre</label>
                            <input type="text" class="form-control" id="first_name" placeholder="">
                        </div>
                    
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                                <label for="last_name">Apellidos</label>
                                <input type="text" class="form-control" id="last_name" placeholder="">
                        </div>
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