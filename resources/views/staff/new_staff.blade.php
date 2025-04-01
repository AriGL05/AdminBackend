@extends('layouts.admin')

@section('title', 'Add Staff Member')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Información del Personal</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="/staff" id="staff-form">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">Nombre</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Apellidos</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="username">Nombre de Usuario</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address_id">Dirección</label>
                                    <select class="form-control" id="address_id" name="address_id" required>
                                        <option value="">Seleccionar dirección</option>
                                        <!-- These will be loaded dynamically -->
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="store_id">Tienda</label>
                                    <select class="form-control" id="store_id" name="store_id" required>
                                        <option value="">Seleccionar tienda</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->store_id }}">{{ $store->address }}, {{ $store->city }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="role_id">Rol</label>
                                    <select class="form-control" id="role_id" name="role_id" required>
                                        <option value="1">Administrador</option>
                                        <option value="2" selected>Editor</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="password">Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="form-group">
                                    <label for="active">Estado</label>
                                    <select class="form-control" id="active" name="active" required>
                                        <option value="1" selected>Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <a href="{{ route('tablas', ['tipo' => 'staff']) }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load addresses for dropdown
        fetch('/address')
            .then(response => response.json())
            .then(addresses => {
                const addressSelect = document.getElementById('address_id');
                addresses.forEach(address => {
                    const option = document.createElement('option');
                    option.value = address.address_id;
                    option.textContent = `${address.address}, ${address.district}, ${address.city}`;
                    addressSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading addresses:', error));

        // Form submission handling
        document.getElementById('staff-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const jsonData = {};
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            fetch('/staff', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(jsonData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Error creating staff member');
                    });
                }
                return response.json();
            })
            .then(data => {
                alert('Personal creado exitosamente');
                window.location.href = "{{ route('tablas', ['tipo' => 'staff']) }}";
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        });
    });
</script>
@endsection
