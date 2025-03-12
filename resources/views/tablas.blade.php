@extends('layouts.admin')

@section('title', 'Tablas')

@section('content')
<div class="wrapper">
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">{{ ucfirst($tipo ?? 'Selecciona una tabla') }}</h3>
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
                    }
                  @endphp
                  <a href="{{ $addRoute }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Añadir nuevo
                  </a>
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
          generateTableHeaders(data[0]);

          // Generate table rows
          generateTableRows(data);

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

  function generateTableHeaders(item) {
    const headerRow = document.getElementById('table-headers');
    headerRow.innerHTML = '';

    // Create headers based on object keys
    Object.keys(item).forEach(key => {
      if (key !== 'imagen') { // Skip image URLs in headers
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
    // Convert snake_case or camelCase to Title Case
    return key
      .replace(/_/g, ' ')
      .replace(/([A-Z])/g, ' $1')
      .replace(/^./, function(str) { return str.toUpperCase(); })
      .trim();
  }

  function generateTableRows(data) {
    const tableBody = document.getElementById('table-body');
    const tipo = '{{ $tipo ?? "" }}';
    tableBody.innerHTML = '';

    data.forEach(item => {
      const row = document.createElement('tr');

      Object.entries(item).forEach(([key, value]) => {
        if (key !== 'imagen') { // Skip image URLs in regular cells
          const cell = document.createElement('td');

          // Format the value based on the key
          if (key === 'duracion') {
            cell.textContent = `${value} min`;
          } else if (key === 'anio' || key === 'year') {
            cell.textContent = value;
          } else {
            cell.textContent = value;
          }

          row.appendChild(cell);
        }
      });

    const actionsCell = document.createElement('td');
    actionsCell.className = 'text-center';
    
    if (tipo === 'peliculas') {
            const editBtn = document.createElement('button');
            editBtn.className = 'btn btn-sm btn-info mr-1';
            editBtn.innerHTML = '<i class="fas fa-edit"></i>';
            editBtn.onclick = function() { editItem(item.film_id); };
            actionsCell.appendChild(editBtn);

            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'btn btn-sm btn-danger';
            deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
            deleteBtn.onclick = function() { deleteItem(item.film_id); };
            actionsCell.appendChild(deleteBtn);
        } else if (tipo === 'actores') {
            const editBtn = document.createElement('button');
            editBtn.className = 'btn btn-sm btn-info mr-1';
            editBtn.innerHTML = '<i class="fas fa-edit"></i>';
            editBtn.onclick = function() { editItem(item.id); };
            actionsCell.appendChild(editBtn);

            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'btn btn-sm btn-danger';
            deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
            deleteBtn.onclick = function() { deleteItem(item.id); };
            actionsCell.appendChild(deleteBtn);
        } else if (tipo === 'categorias') {
            const editBtn = document.createElement('button');
            editBtn.className = 'btn btn-sm btn-info mr-1';
            editBtn.innerHTML = '<i class="fas fa-edit"></i>';
            editBtn.onclick = function() { editItem(item.category_id); }; 
            actionsCell.appendChild(editBtn);

            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'btn btn-sm btn-danger';
            deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
            deleteBtn.onclick = function() { deleteItem(item.category_id); }; 
            actionsCell.appendChild(deleteBtn);
        }



    row.appendChild(actionsCell);
    tableBody.appendChild(row);
    });
  }

  function editItem(id) {
    const tipo = '{{ $tipo ?? "" }}';
    let endpoint;

    switch (tipo) {
      case 'peliculas':
        const film_id = Number(id)
        window.location.href = `/aboutfilm/${film_id}`;
        break;
      case 'actores':
        const actor_id = Number(id)
        window.location.href = `/aboutactor/${actor_id}`;
        break;
      case 'categorias':
        window.location.href = `/categories/${id}/edit`;
        break;
      case 'customers':
        window.location.href = `/customers/${id}/edit`;
        break;
      case 'address':
        window.location.href = `/address/${id}/edit`;
        break;
    }
  }

  function deleteItem(id) {
    if (!confirm('¿Estás seguro que deseas eliminar este elemento?')) {
      return;
    }

    const tipo = '{{ $tipo ?? "" }}';
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
      default:
        return;
    }

    fetch(endpoint, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Failed to delete item');
      }
      return response.json();
    })
    .then(() => {
      // Reload the data after successful deletion
      fetchData(tipo);
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al eliminar el elemento');
    });
  }
</script>
@endsection
