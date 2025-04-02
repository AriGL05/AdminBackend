@extends('layouts.admin')

@section('title', 'Edit ' . ucfirst($itemType))

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Edit {{ ucfirst($itemType) }}</h3>
                </div>
                <div class="card-body">
                    <div id="loading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Loading data...</p>
                    </div>
                    <div id="error-message" class="alert alert-danger d-none" role="alert">
                        Error loading data. Please try again.
                    </div>
                    <div id="form-container" class="d-none">
                        <form id="edit-form" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row" id="form-fields">
                                <!-- Form fields will be dynamically inserted here -->
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" onclick="submitForm()" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('tablas', ['tipo' => $itemType === 'staff' ? 'staff' : ($itemType === 'peliculas' ? 'peliculas' : ($itemType === 'actores' ? 'actores' : ($itemType === 'customers' ? 'customers' : 'address')))]) }}"
                        class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const itemType = '{{ $itemType }}';
            const itemId = '{{ $itemId }}';

            console.log(`Loading edit form for ${itemType} with ID ${itemId}`);

            fetchItemData(itemType, itemId);
        });

        function fetchItemData(itemType, itemId) {
            // Show loading indicator
            document.getElementById('loading').classList.remove('d-none');
            document.getElementById('form-container').classList.add('d-none');
            document.getElementById('error-message').classList.add('d-none');

            // Define endpoint mapping for different item types
            const endpoints = {
                'peliculas': `/films/${itemId}/edit`,
                'actores': `/actors/${itemId}/edit`,
                'categorias': `/categories/${itemId}/edit`,
                'customers': `/customers/${itemId}/edit`,
                'address': `/address/${itemId}/edit`,
                'staff': `/staff/${itemId}/edit`
            };

            // Get the appropriate endpoint
            const endpoint = endpoints[itemType];

            if (!endpoint) {
                // Handle unknown item type
                document.getElementById('loading').classList.add('d-none');
                document.getElementById('error-message').classList.remove('d-none');
                document.getElementById('error-message').textContent = `Unknown item type: ${itemType}`;
                console.error(`Unknown item type: ${itemType}`);
                return;
            }

            // Fetch the item data
            fetch(endpoint)
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            try {
                                const errorData = JSON.parse(text);
                                console.error('Error details:', errorData);
                                throw new Error(errorData.message || `Error ${response.status}: ${response.statusText}`);
                            } catch (e) {
                                // If it's not valid JSON
                                console.error('Error response body:', text);
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);

                    // Hide loading indicator
                    document.getElementById('loading').classList.add('d-none');
                    document.getElementById('form-container').classList.remove('d-none');

                    // Generate form fields based on item type
                    generateFormFields(itemType, data);

                    // Setup form submission
                    document.getElementById('edit-form').action = endpoint.replace('/edit', '');
                })
                .catch(error => {
                    console.error('Error fetching item data:', error);
                    document.getElementById('loading').classList.add('d-none');
                    document.getElementById('error-message').classList.remove('d-none');
                    document.getElementById('error-message').textContent = `Error loading data: ${error.message}`;
                });
        }

        function generateFormFields(itemType, data) {
            const formFields = document.getElementById('form-fields');
            formFields.innerHTML = '';

            // Create form fields based on item type
            switch (itemType) {
                case 'staff':
                    createStaffForm(formFields, data);
                    break;
                case 'peliculas':
                    createFilmForm(formFields, data);
                    break;
                case 'actores':
                    createActorForm(formFields, data);
                    break;
                case 'categorias':
                    createCategoryForm(formFields, data);
                    break;
                case 'customers':
                    createCustomerForm(formFields, data);
                    break;
                case 'address':
                    createAddressForm(formFields, data);
                    break;
                default:
                    formFields.innerHTML = '<div class="col-12">Unknown item type</div>';
            }
        }

        function createStaffForm(container, data) {
            const leftColumn = document.createElement('div');
            leftColumn.className = 'col-md-6';

            const rightColumn = document.createElement('div');
            rightColumn.className = 'col-md-6';

            console.log('Staff data for form:', data); // Debug output

            // Ensure data is not null or undefined
            data = data || {};

            // Left column fields
            leftColumn.innerHTML = `
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="${data.first_name || ''}" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="${data.last_name || ''}" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="${data.email || ''}" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="${data.username || ''}" required>
            </div>
        `;

            // Right column fields - check for both rol_id and role_id
            const roleId = data.rol_id !== undefined ? data.rol_id : (data.role_id !== undefined ? data.role_id : '');

            rightColumn.innerHTML = `
            <div class="form-group">
                <label for="rol_id">Role</label>
                <select class="form-control" id="rol_id" name="rol_id" required>
                    <option value="1" ${roleId == 1 ? 'selected' : ''}>Administrator</option>
                    <option value="2" ${roleId == 2 ? 'selected' : ''}>Editor</option>
                </select>
            </div>
            <div class="form-group">
                <label for="active">Active</label>
                <select class="form-control" id="active" name="active" required>
                    <option value="1" ${data.active == 1 ? 'selected' : ''}>Active</option>
                    <option value="0" ${data.active == 0 ? 'selected' : ''}>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password (leave empty to keep current)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
        `;

            container.appendChild(leftColumn);
            container.appendChild(rightColumn);
        }

        function createFilmForm(container, data) {
            console.log('Creating film form with data:', data);

            // Ensure data exists
            if (!data) {
                container.innerHTML = '<div class="col-12 alert alert-warning">No film data available</div>';
                return;
            }

            const leftColumn = document.createElement('div');
            leftColumn.className = 'col-md-6';

            const rightColumn = document.createElement('div');
            rightColumn.className = 'col-md-6';

            // Left column fields
            leftColumn.innerHTML = `
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="${data.title || ''}" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4">${data.description || ''}</textarea>
            </div>
            <div class="form-group">
                <label for="release_year">Release Year</label>
                <input type="number" class="form-control" id="release_year" name="release_year" min="1900" max="2099" value="${data.release_year || ''}" required>
            </div>
            <div class="form-group">
                <label for="language_id">Language</label>
                <select class="form-control" id="language_id" name="language_id" required>
                    <option value="">Select Language</option>
                </select>
            </div>
            <div class="form-group">
                <label for="original_language_id">Original Language (optional)</label>
                <select class="form-control" id="original_language_id" name="original_language_id">
                    <option value="">None</option>
                </select>
            </div>
        `;

            // Right column fields
            rightColumn.innerHTML = `
            <div class="form-group">
                <label for="rental_duration">Rental Duration (days)</label>
                <input type="number" class="form-control" id="rental_duration" name="rental_duration" min="1" value="${data.rental_duration || '3'}" required>
            </div>
            <div class="form-group">
                <label for="rental_rate">Rental Rate</label>
                <input type="number" class="form-control" id="rental_rate" name="rental_rate" min="0" step="0.01" value="${data.rental_rate || '4.99'}" required>
            </div>
            <div class="form-group">
                <label for="length">Length (minutes)</label>
                <input type="number" class="form-control" id="length" name="length" min="1" value="${data.length || ''}" required>
            </div>
            <div class="form-group">
                <label for="replacement_cost">Replacement Cost</label>
                <input type="number" class="form-control" id="replacement_cost" name="replacement_cost" min="0" step="0.01" value="${data.replacement_cost || '19.99'}" required>
            </div>
            <div class="form-group">
                <label for="rating">Rating</label>
                <select class="form-control" id="rating" name="rating">
                    <option value="G" ${(data.rating === 'G') ? 'selected' : ''}>G</option>
                    <option value="PG" ${(data.rating === 'PG') ? 'selected' : ''}>PG</option>
                    <option value="PG-13" ${(data.rating === 'PG-13') ? 'selected' : ''}>PG-13</option>
                    <option value="R" ${(data.rating === 'R') ? 'selected' : ''}>R</option>
                    <option value="NC-17" ${(data.rating === 'NC-17') ? 'selected' : ''}>NC-17</option>
                </select>
            </div>
            <div class="form-group">
                <label for="special_features">Special Features</label>
                <input type="text" class="form-control" id="special_features" name="special_features" value="${data.special_features || ''}">
                <small class="form-text text-muted">Separate multiple features with commas</small>
            </div>
        `;

            // Append columns to container
            container.appendChild(leftColumn);
            container.appendChild(rightColumn);

            // Fetch languages for dropdowns
            fetch('/languages/all')
                .then(response => response.json())
                .then(languages => {
                    const languageDropdown = document.getElementById('language_id');
                    const originalLanguageDropdown = document.getElementById('original_language_id');

                    // Populate language dropdowns
                    languages.forEach(lang => {
                        // Main language dropdown
                        const langOption = document.createElement('option');
                        langOption.value = lang.language_id;
                        langOption.textContent = lang.name;
                        if (data.language_id == lang.language_id) {
                            langOption.selected = true;
                        }
                        languageDropdown.appendChild(langOption);

                        // Original language dropdown (clone the option)
                        const origLangOption = langOption.cloneNode(true);
                        if (data.original_language_id == lang.language_id) {
                            origLangOption.selected = true;
                        }
                        originalLanguageDropdown.appendChild(origLangOption);
                    });
                })
                .catch(error => {
                    console.error('Error loading languages:', error);
                });

            // Add film categories section (if category information is available)
            if (data.categories) {
                const categoriesSection = document.createElement('div');
                categoriesSection.className = 'col-12 mt-4';
                categoriesSection.innerHTML = `
                <h5>Film Categories</h5>
                <div id="categories-container" class="form-group">
                    <div class="row" id="categories-checkboxes"></div>
                </div>
            `;
                container.appendChild(categoriesSection);

                // Fetch all available categories
                fetch('/categories/all')
                    .then(response => response.json())
                    .then(categories => {
                        const categoriesContainer = document.getElementById('categories-checkboxes');

                        // Create a checkbox for each category
                        categories.forEach(category => {
                            const isSelected = data.categories.some(c => c.category_id == category.category_id);

                            const categoryCol = document.createElement('div');
                            categoryCol.className = 'col-md-3 mb-2';
                            categoryCol.innerHTML = `
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="category_${category.category_id}"
                                       name="categories[]" value="${category.category_id}" ${isSelected ? 'checked' : ''}>
                                <label class="custom-control-label" for="category_${category.category_id}">${category.name}</label>
                            </div>
                        `;

                            categoriesContainer.appendChild(categoryCol);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading categories:', error);
                    });
            }
        }

        function createActorForm(container, data) {
            console.log('Creating actor form with data:', data);

            // Basic actor form implementation
            const formContent = document.createElement('div');
            formContent.className = 'col-md-6 offset-md-3';

            formContent.innerHTML = `
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="${data.first_name || ''}" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="${data.last_name || ''}" required>
            </div>
        `;

            container.appendChild(formContent);
        }

        function createCategoryForm(container, data) {
            console.log('Creating category form with data:', data);

            // Basic category form implementation
            const formContent = document.createElement('div');
            formContent.className = 'col-md-6 offset-md-3';

            formContent.innerHTML = `
            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" class="form-control" id="name" name="name" value="${data.name || ''}" required>
            </div>
        `;

            container.appendChild(formContent);
        }

        function createCustomerForm(container, data) {
            console.log('Creating customer form with data:', data);

            const leftColumn = document.createElement('div');
            leftColumn.className = 'col-md-6';

            const rightColumn = document.createElement('div');
            rightColumn.className = 'col-md-6';

            // Left column fields
            leftColumn.innerHTML = `
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="${data.first_name || ''}" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="${data.last_name || ''}" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="${data.email || ''}" required>
            </div>
        `;

            // Right column fields
            rightColumn.innerHTML = `
            <div class="form-group">
                <label for="address_id">Address ID</label>
                <input type="number" class="form-control" id="address_id" name="address_id" value="${data.address_id || ''}" required>
            </div>
            <div class="form-group">
                <label for="store_id">Store ID</label>
                <select class="form-control" id="store_id" name="store_id" required>
                    <option value="1" ${data.store_id == 1 ? 'selected' : ''}>Store 1</option>
                    <option value="2" ${data.store_id == 2 ? 'selected' : ''}>Store 2</option>
                </select>
            </div>
            <div class="form-group">
                <label for="active">Active</label>
                <select class="form-control" id="active" name="active" required>
                    <option value="1" ${data.active == 1 ? 'selected' : ''}>Active</option>
                    <option value="0" ${data.active == 0 ? 'selected' : ''}>Inactive</option>
                </select>
            </div>
        `;

            container.appendChild(leftColumn);
            container.appendChild(rightColumn);
        }

        function createAddressForm(container, data) {
            console.log('Creating address form with data:', data);

            const formContent = document.createElement('div');
            formContent.className = 'col-md-8 offset-md-2';

            formContent.innerHTML = `
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="${data.address || ''}" maxlength="50" required>
            </div>
            <div class="form-group">
                <label for="address2">Address Line 2</label>
                <input type="text" class="form-control" id="address2" name="address2" value="${data.address2 || ''}" maxlength="50">
            </div>
            <div class="form-group">
                <label for="district">District</label>
                <input type="text" class="form-control" id="district" name="district" value="${data.district || ''}" maxlength="20" required>
            </div>
            <div class="form-group">
                <label for="city_id">City</label>
                <select class="form-control" id="city_id" name="city_id" required>
                    <option value="">Select City</option>
                </select>
            </div>
            <div class="form-group">
                <label for="postal_code">Postal Code</label>
                <input type="text" class="form-control" id="postal_code" name="postal_code" value="${data.postal_code || ''}" maxlength="10">
                <small class="form-text text-muted">Maximum 10 characters</small>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="${data.phone || ''}" maxlength="20" required>
            </div>
        `;

            container.appendChild(formContent);

            // Fetch cities for dropdown
            fetch('/api/cities')
                .then(response => response.json())
                .then(cities => {
                    const cityDropdown = document.getElementById('city_id');

                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.city_id;
                        option.textContent = `${city.city}, ${city.country}`;
                        if (data.city_id == city.city_id) {
                            option.selected = true;
                        }
                        cityDropdown.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading cities:', error);
                });
        }

        function submitForm() {
            const form = document.getElementById('edit-form');
            const formData = new FormData(form);
            const jsonData = {};

            formData.forEach((value, key) => {
                // Handle categories as an array
                if (key === 'categories[]') {
                    if (!jsonData.categories) {
                        jsonData.categories = [];
                    }
                    jsonData.categories.push(value);
                } else {
                    jsonData[key] = value;
                }
            });

            // Handle special case for categories if they exist
            if (document.querySelectorAll('[name="categories[]"]').length > 0) {
                jsonData.categories = Array.from(document.querySelectorAll('[name="categories[]"]:checked')).map(el => el.value);
            }

            // Get CSRF token from meta tag
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (!token) {
                console.error('CSRF token not found');
                alert('Error: CSRF token not found. Please refresh the page.');
                return;
            }

            // Show loading indicator
            document.getElementById('loading').classList.remove('d-none');
            document.getElementById('form-container').classList.add('d-none');

            // Determine the correct endpoint based on item type
            const itemType = '{{ $itemType }}';
            const itemId = '{{ $itemId }}';

            // Use direct endpoint URLs based on the item type
            let endpoint;
            switch (itemType) {
                case 'peliculas':
                    endpoint = `/films/${itemId}`;
                    break;
                case 'actores':
                    endpoint = `/actors/${itemId}`;
                    break;
                case 'categorias':
                    endpoint = `/categories/${itemId}`;
                    break;
                case 'customers':
                    endpoint = `/customers/${itemId}`;
                    break;
                case 'address':
                    endpoint = `/address/${itemId}`;
                    break;
                case 'staff':
                    endpoint = `/staff/${itemId}`;
                    break;
                default:
                    console.error('Unknown item type:', itemType);
                    alert('Error: Unknown item type');
                    return;
            }

            console.log(`Submitting to endpoint: ${endpoint}`);

            fetch(endpoint, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(jsonData)
            })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            try {
                                // Try to parse as JSON
                                const data = JSON.parse(text);
                                let errorMessage = data.message || data.error || `Error: ${response.status}`;

                                // Display validation errors in a more user-friendly way
                                if (data.details) {
                                    errorMessage += '\n\nValidation errors:';
                                    for (const [field, errors] of Object.entries(data.details)) {
                                        errorMessage += `\n• ${field}: ${errors.join(', ')}`;
                                    }
                                }

                                throw new Error(errorMessage);
                            } catch (e) {
                                // If not valid JSON, show the error
                                console.error('Error response:', text);
                                throw new Error(`Server error: ${response.status} ${response.statusText}`);
                            }
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Success:', data);
                    alert(data.message || 'Item updated successfully');

                    // Determine which table to redirect back to
                    let redirectPath = '/tablas/';

                    switch (itemType) {
                        case 'staff':
                            redirectPath += 'staff';
                            break;
                        case 'peliculas':
                            redirectPath += 'peliculas';
                            break;
                        case 'actores':
                            redirectPath += 'actores';
                            break;
                        case 'categorias':
                            redirectPath += 'categorias';
                            break;
                        case 'customers':
                            redirectPath += 'customers';
                            break;
                        case 'address':
                            redirectPath += 'address';
                            break;
                    }

                    window.location.href = redirectPath;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('loading').classList.add('d-none');
                    document.getElementById('form-container').classList.remove('d-none');
                    alert(`Failed to update: ${error.message}`);
                });
        }
    </script>
@endsection