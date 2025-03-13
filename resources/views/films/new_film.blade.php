@extends('layouts.admin')

@section('title', 'Add Film')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Información de la Película:</h3>

                    <div class="card-tools">
                        <!--
                                                                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                                                                    <i class="fas fa-minus"></i>
                                                                                                </button>
                                                                                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                                                                    <i class="fas fa-times"></i>
                                                                                                </button>
                                                                                                -->
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="/films">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="titulo">Título</label>
                                    <input type="text" class="form-control" id="title" name="title" required placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="language">Idioma</label>
                                    <select id="language_id" name="language_id" required class="form-control select2"
                                        style="width: 100%;">
                                        @foreach ($languages as $language)
                                            <option value="{{ $language->language_id }}">{{ $language->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="category">Categoría/Género</label>
                                    <select name="category_id" class="form-control select2" required style="width: 100%;">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="anio">Año de Lanzamiento</label>
                                    <input type="number" class="form-control" id="release_year" name="release_year"
                                        placeholder="" required>
                                </div>
                                <div class="form-group">
                                    <label for="duracion">Duración (minutos)</label>
                                    <input type="number" class="form-control" id="length" name="length" placeholder=""
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="sinopsis">Sinopsis</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"
                                        placeholder="Sobre esta película.."></textarea>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
                @if(session('success'))
                    <script>
                        alert('Film added successfully');
                    </script>
                @endif

                @if(session('error'))
                    <script>
                        alert('Error adding film');
                    </script>
                @endif
                </form>

            </div>
            <div style="height: 15px">
            </div>
    </section>

@endsection

@section('scripts')
    <script>
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'btn btn-sm btn-danger';
        deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
        deleteBtn.onclick = function () { deleteItem(itemId); };
        actionsCell.appendChild(deleteBtn);

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

            // Get CSRF token from meta tag
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (!token) {
                console.error('CSRF token not found');
                alert('Error: CSRF token not found. Please refresh the page.');
                return;
            }

            fetch(endpoint, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Failed to delete item: ${response.status} ${response.statusText}`);
                    }
                    return response.json().catch(() => {
                        // Some endpoints might not return JSON
                        return { success: true };
                    });
                })
                .then(() => {
                    // Show success message
                    alert('Elemento eliminado correctamente');
                    // Reload the data after successful deletion
                    fetchData(tipo);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(`Error al eliminar el elemento: ${error.message}`);
                });
        }

    </script>
@endsection