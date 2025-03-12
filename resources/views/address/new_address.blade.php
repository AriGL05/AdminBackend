@extends('layouts.admin')

@section('title', 'Add Address')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Información de la Dirección:</h3>

                    <div class="card-tools">
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="/address">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Dirección</label>
                                    <input type="text" class="form-control" id="address" name="address" required placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="address2">Dirección 2 (opcional)</label>
                                    <input type="text" class="form-control" id="address2" name="address2" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="district">Distrito</label>
                                    <input type="text" class="form-control" id="district" name="district" required placeholder="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city_id">Ciudad</label>
                                    <select class="form-control" id="city_id" name="city_id" required>
                                        <option value="">Seleccionar ciudad</option>
                                        <!-- These will be loaded dynamically -->
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="postal_code">Código Postal</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Teléfono</label>
                                    <input type="text" class="form-control" id="phone" name="phone" required placeholder="">
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
        // Load cities for dropdown
        fetch('/api/cities')
            .then(response => response.json())
            .then(cities => {
                const citySelect = document.getElementById('city_id');
                cities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.city_id;
                    option.textContent = city.city;
                    citySelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading cities:', error));
    });
</script>
@endsection
