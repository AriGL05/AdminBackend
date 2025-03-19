@extends('layouts.admin')

@section('title', 'Edit ' . $itemType)

@section('content')
<div class="wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit {{ ucfirst($itemType) }}</h3>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <div id="loading" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2">Loading data...</p>
                            </div>

                            <div id="error-message" class="alert alert-danger d-none"></div>

                            <form id="edit-form" class="d-none">
                                @csrf
                                @method('PUT')
                                <div id="form-fields">
                                    <!-- Dynamic form fields will be inserted here -->
                                </div>
                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemType = '{{ $itemType }}';
        const itemId = '{{ $itemId }}';

        // Fetch the item data for editing
        fetchItemData(itemType, itemId);

        // Set up form submission
        document.getElementById('edit-form').addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(itemType, itemId);
        });
    });

    function fetchItemData(itemType, itemId) {
        // Show loading indicator
        document.getElementById('loading').classList.remove('d-none');
        document.getElementById('edit-form').classList.add('d-none');

        // Determine the API endpoint based on the item type
        let endpoint;
        switch (itemType) {
            case 'peliculas':
                endpoint = `/films/${itemId}/edit`;
                break;
            case 'actores':
                endpoint = `/actors/${itemId}/edit`;
                break;
            case 'categorias':
                endpoint = `/categories/${itemId}/edit`;
                break;
            case 'customers':
                endpoint = `/customers/${itemId}/edit`;
                break;
            case 'address':
                endpoint = `/address/${itemId}/edit`;
                break;
            default:
                console.error('Unknown item type');
                return;
        }

        console.log(`Fetching data from endpoint: ${endpoint}`); // Debug logging

        // Fetch the data
        fetch(endpoint)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server responded with status: ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data); // Debug logging
                buildForm(itemType, data);
                document.getElementById('loading').classList.add('d-none');
                document.getElementById('edit-form').classList.remove('d-none');
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                document.getElementById('loading').classList.add('d-none');
                document.getElementById('error-message').classList.remove('d-none');
                document.getElementById('error-message').textContent = `Error loading data: ${error.message}. Please try again.`;
            });
    }

    function buildForm(itemType, data) {
        const formFields = document.getElementById('form-fields');
        formFields.innerHTML = '';

        // Define field configurations based on item type
        const fieldConfig = getFieldConfig(itemType);

        // Create form fields based on configuration
        Object.entries(fieldConfig).forEach(([fieldName, config]) => {
            if (data[fieldName] !== undefined) {
                const fieldWrapper = document.createElement('div');
                fieldWrapper.className = 'form-group';

                const label = document.createElement('label');
                label.textContent = config.label;
                label.setAttribute('for', fieldName);

                let input;

                if (config.type === 'select') {
                    input = document.createElement('select');
                    input.className = 'form-control';

                    // Fetch options for select fields
                    fetchSelectOptions(fieldName, config.endpoint)
                        .then(options => {
                            options.forEach(option => {
                                const opt = document.createElement('option');
                                opt.value = option.value;
                                opt.textContent = option.label;
                                if (data[fieldName] == option.value) {
                                    opt.selected = true;
                                }
                                input.appendChild(opt);
                            });
                        });
                } else {
                    input = document.createElement('input');
                    input.className = 'form-control';
                    input.type = config.type || 'text';
                    input.value = data[fieldName] || '';

                    if (config.type === 'number') {
                        input.min = config.min || 0;
                        if (config.max) input.max = config.max;
                    }
                }

                input.id = fieldName;
                input.name = fieldName;

                if (config.readonly) {
                    input.readOnly = true;
                    input.classList.add('bg-light');
                }

                if (config.required) {
                    input.required = true;
                    label.innerHTML += ' <span class="text-danger">*</span>';
                }

                fieldWrapper.appendChild(label);
                fieldWrapper.appendChild(input);
                formFields.appendChild(fieldWrapper);
            }
        });
    }

    function getFieldConfig(itemType) {
        // Define field configurations for each item type
        switch (itemType) {
            case 'peliculas':
                return {
                    film_id: { label: 'Film ID', readonly: true },
                    title: { label: 'Title', required: true },
                    release_year: { label: 'Release Year', type: 'number', required: true, min: 1900, max: new Date().getFullYear() + 10 },
                    language_id: { label: 'Language', type: 'select', required: true, endpoint: '/languages/all' },
                    length: { label: 'Length (minutes)', type: 'number', required: true, min: 1 },
                    category_id: { label: 'Category', type: 'select', required: true, endpoint: '/categories/all' }
                };

            case 'actores':
                return {
                    actor_id: { label: 'Actor ID', readonly: true },
                    first_name: { label: 'First Name', required: true },
                    last_name: { label: 'Last Name', required: true }
                };

            case 'categorias':
                return {
                    category_id: { label: 'Category ID', readonly: true },
                    name: { label: 'Name', required: true }
                };

            case 'customers':
                return {
                    customer_id: { label: 'Customer ID', readonly: true },
                    store_id: { label: 'Store ID', type: 'number', required: true },
                    first_name: { label: 'First Name', required: true },
                    last_name: { label: 'Last Name', required: true },
                    email: { label: 'Email', type: 'email', required: true },
                    address_id: { label: 'Address ID', type: 'number', required: true }
                };

            case 'address':
                return {
                    address_id: { label: 'Address ID', readonly: true },
                    address: { label: 'Address Line', required: true },
                    district: { label: 'District', required: true },
                    city_id: { label: 'City', type: 'select', required: true, endpoint: '/api/cities' },
                    postal_code: { label: 'Postal Code', required: false } // Made postal code optional
                };

            default:
                return {};
        }
    }

    function fetchSelectOptions(fieldName, endpoint) {
        return fetch(endpoint)
            .then(response => response.json())
            .then(data => {
                // Transform data into options based on field name
                switch (fieldName) {
                    case 'language_id':
                        return data.map(item => ({
                            value: item.language_id,
                            label: item.name
                        }));
                    case 'category_id':
                        return data.map(item => ({
                            value: item.category_id,
                            label: item.name
                        }));
                    case 'city_id':
                        return data.map(item => ({
                            value: item.city_id,
                            label: item.city
                        }));
                    default:
                        return data;
                }
            })
            .catch(error => {
                console.error('Error fetching options:', error);
                return [];
            });
    }

    function submitForm(itemType, itemId) {
        // Get form data
        const form = document.getElementById('edit-form');
        const formData = new FormData(form);

        // Convert FormData to JSON object
        const jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Determine endpoint based on item type
        let endpoint;
        switch (itemType) {
            case 'peliculas':
                endpoint = `/films/${itemId}/edit`;
                break;
            case 'actores':
                endpoint = `/actors/${itemId}/edit`;
                break;
            case 'categorias':
                endpoint = `/categories/${itemId}/edit`;
                break;
            case 'customers':
                endpoint = `/customers/${itemId}/edit`;
                break;
            case 'address':
                endpoint = `/address/${itemId}/edit`;
                break;
            default:
                console.error('Unknown item type');
                return;
        }

        // Send PUT request
        fetch(endpoint, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Error updating item');
                });
            }
            return response.json().catch(() => {
                // Some endpoints might not return JSON
                return { success: true };
            });
        })
        .then(() => {
            alert('Item updated successfully');
            // Redirect to tables page
            window.location.href = `/tablas/${itemType}`;
        })
        .catch(error => {
            console.error('Error:', error);
            alert(`Error: ${error.message}`);
        });
    }
</script>
@endsection
