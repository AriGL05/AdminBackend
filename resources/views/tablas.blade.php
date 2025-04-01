@extends('layouts.admin')

@section('title', 'Tablas')

@section('content')
<div class="wrapper">
  <!--HOLA KASSY TRABJA EN ESTA RAMA -->
  <!--okis-->
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">{{ $titulo ?? 'Selecciona una tabla' }}</h3>
                @if($tipo)
                <div>
                  @php
                    $addRoute = '';
                    switch($tipo) {
                      case 'peliculas':
                        $addRoute = route('newfilm');
                        break;
                      case 'actores':
                        $addRoute = route('newactor');
                        break;
                      case 'categorias':
                        $addRoute = route('newcategory');
                        break;
                      case 'customers':
                        $addRoute = route('newcustomer');
                        break;
                      case 'address':
                        $addRoute = route('newaddress');
                        break;
                      case 'staff':
                        $addRoute = route('newstaff');
                        break;
                    }
                  @endphp
                  @if($addRoute)
                  <a href="{{ $addRoute }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Añadir nuevo
                  </a>
                  @endif
                </div>
                @endif
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div id="loading" class="text-center py-5">
                  <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                  </div>
                  <p class="mt-2">Cargando datos...</p>
                </div>

                <div id="error-message" class="alert alert-danger d-none" role="alert">
                  Error al cargar los datos. Por favor, intente nuevamente.
                </div>

                <div id="table-container" class="table-responsive d-none">
                  <table id="data-table" class="table table-bordered table-hover">
                    <thead>
                      <tr id="table-headers">
                        <!-- Headers will be dynamically inserted here -->
                      </tr>
                    </thead>
                    <tbody id="table-body">
                      <!-- Data rows will be dynamically inserted here -->
                    </tbody>
                  </table>
                </div>

                <div id="no-data" class="alert alert-info d-none" role="alert">
                  No hay datos disponibles en esta sección.
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>

@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const tipo = '{{ $tipo ?? "" }}';
    if (tipo) {
      fetchData(tipo);
    } else {
      document.getElementById('loading').classList.add('d-none');
      document.getElementById('no-data').classList.remove('d-none');
      document.getElementById('no-data').textContent = 'Selecciona una categoría del menú lateral para ver los datos.';
    }
  });

  function fetchData(tipo) {
    // Show loading indicator
    document.getElementById('loading').classList.remove('d-none');
    document.getElementById('table-container').classList.add('d-none');
    document.getElementById('error-message').classList.add('d-none');
    document.getElementById('no-data').classList.add('d-none');

    // Determine the API endpoint based on the selected type
    let endpoint;
    switch (tipo) {
      case 'peliculas':
        endpoint = '/films';
        break;
      case 'actores':
        endpoint = '/actors';
        break;
      case 'categorias':
        endpoint = '/categories';
        break;
      case 'customers':
        endpoint = '/customers';
        break;
      case 'address':
        endpoint = '/address';
        break;
      case 'staff':
        endpoint = '/staff';
        break;
      default:
        endpoint = null;
    }

    if (!endpoint) {
      document.getElementById('loading').classList.add('d-none');
      document.getElementById('no-data').classList.remove('d-none');
      return;
    }

    // Fetch data from the API
    fetch(endpoint)
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        // Hide loading indicator
        document.getElementById('loading').classList.add('d-none');

        if (Array.isArray(data) && data.length > 0) {
          // Generate table headers
          generateTableHeaders(data[0], tipo);

          // Generate table rows
          generateTableRows(data, tipo);

          // Show the table
          document.getElementById('table-container').classList.remove('d-none');
        } else {
          document.getElementById('no-data').classList.remove('d-none');
        }
      })
      .catch(error => {
        console.error('Error fetching data:', error);
        document.getElementById('loading').classList.add('d-none');
        document.getElementById('error-message').classList.remove('d-none');
      });
  }

  function generateTableHeaders(item, tipo) {
    const headerRow = document.getElementById('table-headers');
    headerRow.innerHTML = '';

    // Create headers based on object keys
    Object.keys(item).forEach(key => {
      // Special handling for staff table - hide password
      if (key !== 'imagen' && (tipo !== 'staff' || key !== 'password')) {
        const th = document.createElement('th');
        th.textContent = formatHeaderName(key);
        headerRow.appendChild(th);
      }
    });

    // Add actions column
    const actionsHeader = document.createElement('th');
    actionsHeader.textContent = 'Acciones';
    headerRow.appendChild(actionsHeader);
  }

  function formatHeaderName(key) {
    // Spanish translations for common headers
    const translations = {
      'film_id': 'ID Película',
      'title': 'Título',
      'description': 'Descripción',
      'release_year': 'Año',
      'language_id': 'ID Idioma',
      'original_language_id': 'ID Idioma Original',
      'rental_duration': 'Duración Alquiler',
      'rental_rate': 'Tarifa Alquiler',
      'length': 'Duración',
      'replacement_cost': 'Costo Reemplazo',
      'rating': 'Calificación',
      'special_features': 'Características Especiales',
      'last_update': 'Última Actualización',
      'actor_id': 'ID Actor',
      'first_name': 'Nombre',
      'last_name': 'Apellido',
      'category_id': 'ID Categoría',
      'name': 'Nombre',
      'customer_id': 'ID Cliente',
      'store_id': 'ID Tienda',
      'email': 'Correo Electrónico',
      'active': 'Activo',
      'create_date': 'Fecha Creación',
      'address_id': 'ID Dirección',
      'address': 'Dirección',
      'address2': 'Dirección 2',
      'district': 'Distrito',
      'city_id': 'ID Ciudad',
      'postal_code': 'Código Postal',
      'phone': 'Teléfono',
      'city': 'Ciudad',
      'country_id': 'ID País',
      'country': 'País',
      'film_count': 'Cantidad Películas',
      'staff_id': 'ID Personal',
      'username': 'Usuario',
      'role_id': 'Rol'
    };

    // If we have a translation, use it
    if (translations[key]) {
      return translations[key];
    }

    // Otherwise use the default formatting
    return key
      .replace(/_/g, ' ')
      .replace(/([A-Z])/g, ' $1')
      .replace(/^./, function(str) { return str.toUpperCase(); })
      .trim();
  }

  function generateTableRows(data, tipo) {
    const tableBody = document.getElementById('table-body');
    tableBody.innerHTML = '';

    data.forEach(item => {
      const row = document.createElement('tr');

      // Get the correct ID field based on table type
      let itemId;
      switch(tipo) {
        case 'peliculas':
          itemId = item.film_id || item.id;
          break;
        case 'actores':
          itemId = item.actor_id || item.id;
          break;
        case 'categorias':
          itemId = item.category_id || item.id;
          break;
        case 'customers':
          itemId = item.customer_id || item.id;
          break;
        case 'address':
          itemId = item.address_id || item.id;
          break;
        case 'staff':
          itemId = item.staff_id || item.id;
          break;
        default:
          itemId = item.id;
      }

      Object.entries(item).forEach(([key, value]) => {
        if (key !== 'imagen' && (tipo !== 'staff' || key !== 'password')) {
          const cell = document.createElement('td');

          // Format the value based on the key
          if (key === 'duracion') {
            cell.textContent = `${value} min`;
          } else if (key === 'anio' || key === 'year') {
            cell.textContent = value;
          } else if (key === 'active') {
            cell.textContent = value == 1 ? 'Activo' : 'Inactivo';
          } else if (key === 'role_id') {
            cell.textContent = value == 1 ? 'Administrador' : 'Editor';
          } else {
            cell.textContent = value;
          }

          row.appendChild(cell);
        }
      });

      // Add action buttons
      const actionsCell = document.createElement('td');
      actionsCell.className = 'text-center';

      // Only show edit button for tables other than categorias
      if (tipo !== 'categorias') {
        // Create edit button
        const editBtn = document.createElement('button');
        editBtn.className = 'btn btn-sm btn-info mr-1';
        editBtn.innerHTML = '<i class="fas fa-edit"></i>';
        editBtn.onclick = function() { editItem(itemId); };
        actionsCell.appendChild(editBtn);
      }

      // Create delete button (with special handling for staff)
      if (tipo !== 'address') {
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'btn btn-sm btn-danger';
        deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
        deleteBtn.onclick = function() { deleteItem(itemId); };

        // For staff table, check if this is the current user before allowing deletion
        if (tipo === 'staff') {
          // Add a data attribute to identify admin users
          if (item.role_id === 1) {
            deleteBtn.dataset.isAdmin = 'true';
          }
        }

        actionsCell.appendChild(deleteBtn);
      }

      row.appendChild(actionsCell);
      tableBody.appendChild(row);
    });
  }

  function editItem(id) {
    const tipo = '{{ $tipo ?? "" }}';
    console.log(`Editing ${tipo} with ID: ${id}`); // Debug logging

    // Use our dynamic edit route for all item types
    window.location.href = `/edit/${tipo}/${id}`;
  }

  function deleteItem(id) {
    const tipo = '{{ $tipo ?? "" }}';

    // Special handling for staff table
    if (tipo === 'staff') {
      // Get the button that was clicked
      const btn = event.target.closest('button');

      // Check if this is an admin user
      if (btn.dataset.isAdmin === 'true') {
        // Confirm with extra warning
        if (!confirm('¿Estás seguro que deseas eliminar este administrador? Esta acción puede tener consecuencias graves para la gestión del sistema.')) {
          return;
        }
      } else if (!confirm('¿Estás seguro que deseas eliminar este miembro del personal?')) {
        return;
      }
    } else if (!confirm('¿Estás seguro que deseas eliminar este elemento?')) {
      return;
    }

    let endpoint;
    switch (tipo) {
      case 'peliculas':
        endpoint = `/films/${id}`;
        break;
      case 'actores':
        endpoint = `/actors/${id}`;
        break;
      case 'categorias':
        endpoint = `/categories/${id}`;
        break;
      case 'customers':
        endpoint = `/customers/${id}`;
        break;
      case 'address':
        endpoint = `/address/${id}`;
        break;
      case 'staff':
        endpoint = `/staff/${id}`;
        break;
      default:
        return;
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
    document.getElementById('table-container').classList.add('d-none');

    fetch(endpoint, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
      }
    })
    .then(response => {
      // Hide loading indicator
      document.getElementById('loading').classList.add('d-none');
      document.getElementById('table-container').classList.remove('d-none');

      if (!response.ok) {
        return response.json().then(data => {
          // For constraint violations, provide a more detailed error explanation
          if (response.status === 422 && data.error) {
            let errorMessage = data.error;

            // If we have details, show them
            if (data.details) {
              errorMessage += '\n\nDetalles:';
              for (const [key, value] of Object.entries(data.details)) {
                errorMessage += `\n• ${value}`;
              }
            }

            throw new Error(errorMessage);
          }

          throw new Error(data.error || data.message || `Error al eliminar: ${response.status} ${response.statusText}`);
        });
      }

      return response.json().catch(() => {
        // Some endpoints might not return JSON
        return { success: true };
      });
    })
    .then(data => {
      // Show success message
      alert(data.message || 'Elemento eliminado correctamente');
      // Reload the data after successful deletion
      fetchData(tipo);
    })
    .catch(error => {
      console.error('Error:', error);

      // Create a more styled error display
      showErrorMessage(error.message || 'Error desconocido');

      // Ensure the table is still visible
      document.getElementById('table-container').classList.remove('d-none');
    });
  }

  function showErrorMessage(message) {
    // Create or update error alert
    let errorAlert = document.getElementById('constraint-error');

    if (!errorAlert) {
      errorAlert = document.createElement('div');
      errorAlert.id = 'constraint-error';
      errorAlert.className = 'alert alert-warning alert-dismissible fade show mt-3';
      errorAlert.innerHTML = `
        <h5><i class="icon fas fa-exclamation-triangle"></i> Error</h5>
        <div id="constraint-error-text"></div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      `;

      // Insert after the error-message div
      const errorMessageDiv = document.getElementById('error-message');
      errorMessageDiv.parentNode.insertBefore(errorAlert, errorMessageDiv.nextSibling);
    }

    // Format the message by replacing newlines with <br> tags
    const formattedMessage = message.replace(/\n/g, '<br>');
    document.getElementById('constraint-error-text').innerHTML = formattedMessage;

    // Ensure the alert is visible
    errorAlert.classList.remove('d-none');

    // Hide regular error message if it's visible
    document.getElementById('error-message').classList.add('d-none');
  }
</script>
@endsection
