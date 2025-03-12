@extends('layouts.admin')

@section('title', 'Clientes')

@section('content')
    <div class="wrapper">
        <h3 class="text-center mb-4">Lista de Clientes</h3>
        
        <!-- Botón para agregar un nuevo cliente -->
        <div class="text-right mb-3">
            <a href="{{ route('customers.create') }}" class="btn btn-primary">Agregar Cliente</a>
        </div>
        
        <!-- Tabla de clientes -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td>{{ $customer->first_name }}</td>
                        <td>{{ $customer->last_name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>
                            <!-- Botón de editar -->
                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">Editar</a>
                            
                            <!-- Formulario para eliminar cliente -->
                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que quieres eliminar este cliente?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
