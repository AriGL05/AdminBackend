@extends('layouts.admin')

@section('title', 'Editar Cliente')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Editar Cliente</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('customers.update', $customer->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">Nombre</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $customer->first_name) }}" required placeholder="">
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Apellidos</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $customer->last_name) }}" required placeholder="">
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $customer->email) }}" required placeholder="">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address_id">Dirección</label>
                                    <select class="form-control @error('address_id') is-invalid @enderror" id="address_id" name="address_id" required>
                                        <option value="">Seleccionar dirección</option>
                                    </select>
                                    @error('address_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="active">Estado</label>
                                    <select class="form-control @error('active') is-invalid @enderror" id="active" name="active" required>
                                        <option value="1" {{ old('active', $customer->active) == '1' ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ old('active', $customer->active) == '0' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    @error('active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">Actualizar</button>
                            <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancelar</a>
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

                document.getElementById('address_id').value = '{{ old('address_id', $customer->address_id) }}';
            })
            .catch(error => console.error('Error loading addresses:', error));
    });
</script>
@endsection
