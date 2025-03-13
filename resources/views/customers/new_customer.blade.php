@extends('layouts.admin')

@section('title', 'Add Customer')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Información del Cliente:</h3>

                    <div class="card-tools">
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="/customers">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">Nombre</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Apellidos</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required placeholder="">
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
                                    <label for="active">Estado</label>
                                    <select class="form-control" id="active" name="active" required>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
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
    });
</script>
@endsection
