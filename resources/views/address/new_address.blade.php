@extends('layouts.admin')

@section('title', 'New Address')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Add New Address</h3>
                </div>
                <form id="newAddressForm" method="POST" action="/address">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="address" name="address" maxlength="50" required>
                        </div>
                        <div class="form-group">
                            <label for="address2">Address Line 2</label>
                            <input type="text" class="form-control" id="address2" name="address2" maxlength="50">
                        </div>
                        <div class="form-group">
                            <label for="district">District</label>
                            <input type="text" class="form-control" id="district" name="district" maxlength="20" required>
                        </div>
                        <div class="form-group">
                            <label for="city_id">City</label>
                            <select class="form-control" id="city_id" name="city_id" required>
                                <option value="">Select a city</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="postal_code">Postal Code</label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code" maxlength="10">
                            <small class="text-muted">Maximum 10 characters</small>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" maxlength="20" required>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ route('tablas', ['tipo' => 'address']) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch cities for the dropdown
        fetch('/api/cities')
            .then(response => response.json())
            .then(cities => {
                const citySelect = document.getElementById('city_id');
                cities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.city_id;
                    option.textContent = `${city.city}, ${city.country}`;
                    citySelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading cities:', error);
                alert('Error loading cities. Please try again later.');
            });

        // Handle form submission
        document.getElementById('newAddressForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const jsonData = {};

            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            // Submit form data via fetch API
            fetch('/address', {
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
                        throw new Error(data.message || 'Could not create address');
                    });
                }
                return response.json();
            })
            .then(data => {
                alert('Address created successfully!');
                window.location.href = '{{ route("tablas", ["tipo" => "address"]) }}';
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        });
    });
</script>
@endsection
