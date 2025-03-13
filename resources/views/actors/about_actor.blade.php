@extends('layouts.admin')

@section('title', 'About')

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
                    <form method="PUT" action="/actors/{{ $actor->actor_id }}/edit">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">Nombre</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                        value="{{ $actor->first_name }}">
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Apellidos</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $actor->last_name }}">
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