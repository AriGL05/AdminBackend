<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Actor;
use App\Models\Film;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Address;
use App\Models\Language;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Count data for stat boxes
        $filmCount = DB::table('film')->count();
        $categoryCount = DB::table('category')->count();
        $actorCount = DB::table('actor')->count();
        $customerCount = DB::table('customer')->count();

        // Get recently added films with their categories
        $recentFilms = DB::table('film')
            ->join('film_category', 'film.film_id', '=', 'film_category.film_id')
            ->join('category', 'film_category.category_id', '=', 'category.category_id')
            ->select('film.*', 'category.name as category_name')
            ->orderBy('film.last_update', 'desc')
            ->limit(5)
            ->get();

        // Get film count by category
        $filmsByCategory = DB::table('category')
            ->leftJoin('film_category', 'category.category_id', '=', 'film_category.category_id')
            ->select('category.name', DB::raw('count(film_category.film_id) as film_count'))
            ->groupBy('category.name')
            ->orderBy('film_count', 'desc')
            ->limit(10)
            ->get();

        // Get top actors by number of films
        $topActors = DB::table('actor')
            ->join('film_actor', 'actor.actor_id', '=', 'film_actor.actor_id')
            ->select('actor.*', DB::raw('count(film_actor.film_id) as film_count'))
            ->groupBy('actor.actor_id', 'actor.first_name', 'actor.last_name', 'actor.last_update')
            ->orderBy('film_count', 'desc')
            ->limit(5)
            ->get();

        // Get release year distribution for the chart
        $releaseYears = DB::table('film')
            ->select('release_year', DB::raw('count(*) as total'))
            ->groupBy('release_year')
            ->orderBy('release_year')
            ->get();

        $releaseYearDistribution = [];
        foreach ($releaseYears as $year) {
            $releaseYearDistribution[$year->release_year] = $year->total;
        }

        // Get language statistics
        $languageStats = DB::table('language')
            ->leftJoin('film', 'language.language_id', '=', 'film.language_id')
            ->select('language.name', DB::raw('count(film.film_id) as film_count'))
            ->groupBy('language.language_id', 'language.name')
            ->orderBy('film_count', 'desc')
            ->get();

        return view('dashboard', compact(
            'filmCount',
            'categoryCount',
            'actorCount',
            'customerCount',
            'recentFilms',
            'filmsByCategory',
            'topActors',
            'releaseYearDistribution',
            'languageStats'
        ));
    }

    /**
     * Build dashboard data from table data sources.
     *
     * @param array $peliculas
     * @param array $categorias
     * @param array $actores
     * @return array
     */
    private function buildDashboardData($peliculas, $categorias, $actores)
    {
        // Get recent movies (would be ordered by date in a real DB query)
        $recentMovies = array_slice($peliculas, 0, 4);

        // Extract category names and movie counts for the chart
        $categoryLabels = array_column($categorias, 'nombre');
        $categoryData = array_column($categorias, 'cantidad_peliculas');

        // Extract years for movie distribution chart
        $years = $this->extractYears($peliculas);
        $yearlyMovieCounts = $this->countMoviesByYear($peliculas, $years);

        // Get top actors by movie count
        usort($actores, function ($a, $b) {
            return $b['peliculas'] - $a['peliculas'];
        });
        $topActors = array_slice($actores, 0, 3);

        return [
            'counts' => [
                'peliculas' => count($peliculas),
                'categorias' => count($categorias),
                'actores' => count($actores),
                'nuevosEstrenos' => count(array_filter($peliculas, function ($movie) {
                    return $movie['anio'] == date('Y');
                }))
            ],
            'recentMovies' => $recentMovies,
            'categoryDistribution' => [
                'labels' => $categoryLabels,
                'data' => $categoryData
            ],
            'yearlyReleases' => [
                'labels' => $years,
                'data' => $yearlyMovieCounts
            ],
            'topActors' => $topActors
        ];
    }

    /**
     * Extract distinct years from movie data.
     *
     * @param array $peliculas
     * @return array
     */
    private function extractYears($peliculas)
    {
        $years = [];
        foreach ($peliculas as $pelicula) {
            if (!in_array($pelicula['anio'], $years)) {
                $years[] = $pelicula['anio'];
            }
        }
        sort($years);
        return $years;
    }

    /**
     * Count movies by year.
     *
     * @param array $peliculas
     * @param array $years
     * @return array
     */
    private function countMoviesByYear($peliculas, $years)
    {
        $counts = array_fill(0, count($years), 0);
        foreach ($peliculas as $pelicula) {
            $index = array_search($pelicula['anio'], $years);
            if ($index !== false) {
                $counts[$index]++;
            }
        }
        return $counts;
    }

    /**
     * Display tables based on the selected type.
     *
     * @param string|null $tipo
     * @return \Illuminate\View\View
     */
    public function tablas($tipo = null)
    {
        // Handle the special case for languages
        if ($tipo === 'languages') {
            $titulo = 'Idiomas';
        } else {
            // Map English table types to Spanish titles
            $titulos = [
                'peliculas' => 'Películas',
                'categorias' => 'Categorías',
                'actores' => 'Actores',
                'customers' => 'Clientes',
                'address' => 'Direcciones'
            ];

            $titulo = $titulos[$tipo] ?? 'Selecciona una tabla';
        }

        // Only pass the type to the view, data will be fetched via AJAX
        return view('tablas', [
            'tipo' => $tipo,
            'titulo' => $titulo
        ]);
    }

    /**
     * Get mock data for demonstration purposes.
     * This will be replaced with database queries in the future.
     *
     * @param string|null $tipo
     * @return array
     */
    private function getMockData($tipo)
    {
        switch ($tipo) {
            case 'peliculas':
                return [
                    [
                        'id' => 1,
                        'titulo' => 'El Padrino',
                        'anio' => '1972',
                        'idioma_original' => 'Inglés',
                        'duracion' => 175,
                        'categoria' => 'Drama',
                        'imagen' => 'https://m.media-amazon.com/images/M/MV5BM2MyNjYxNmUtYTAwNi00MTYxLWJmNWYtYzZlODY3ZTk3OTFlXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg'
                    ],
                    [
                        'id' => 2,
                        'titulo' => 'Pulp Fiction',
                        'anio' => '1994',
                        'idioma_original' => 'Inglés',
                        'duracion' => 154,
                        'categoria' => 'Thriller',
                        'imagen' => 'https://m.media-amazon.com/images/M/MV5BNGNhMDIzZTUtNTBlZi00MTRlLWFjM2ItYzViMjE3YzI5MjljXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg'
                    ],
                    [
                        'id' => 3,
                        'titulo' => 'El Señor de los Anillos',
                        'anio' => '2001',
                        'idioma_original' => 'Inglés',
                        'duracion' => 178,
                        'categoria' => 'Fantasía',
                        'imagen' => 'https://m.media-amazon.com/images/M/MV5BN2EyZjM3NzUtNWUzMi00MTgxLWI0NTctMzY4M2VlOTdjZWRiXkEyXkFqcGdeQXVyNDUzOTQ5MjY@._V1_.jpg'
                    ],
                    [
                        'id' => 4,
                        'titulo' => 'Matrix',
                        'anio' => '1999',
                        'idioma_original' => 'Inglés',
                        'duracion' => 136,
                        'categoria' => 'Ciencia Ficción',
                        'imagen' => 'https://m.media-amazon.com/images/M/MV5BNzQzOTk3OTAtNDQ0Zi00ZTVkLWI0MTEtMDllZjNkYzNjNTc4L2ltYWdlXkEyXkFqcGdeQXVyNjU0OTQ0OTY@._V1_.jpg'
                    ],
                    [
                        'id' => 5,
                        'titulo' => 'Dune: Part Two',
                        'anio' => '2024',
                        'idioma_original' => 'Inglés',
                        'duracion' => 166,
                        'categoria' => 'Ciencia Ficción',
                        'imagen' => 'https://m.media-amazon.com/images/M/MV5BN2QyOGIyZDgtNzIzZC00MzJiLWI2Y2YtNjM3ZTMyYjCiYWMzXkEyXkFqcGdeQXVyODE5NzE3OTE@._V1_.jpg'
                    ],
                ];

            case 'categorias':
                return [
                    ['id' => 1, 'nombre' => 'Acción', 'cantidad_peliculas' => 42],
                    ['id' => 2, 'nombre' => 'Drama', 'cantidad_peliculas' => 65],
                    ['id' => 3, 'nombre' => 'Comedia', 'cantidad_peliculas' => 38],
                    ['id' => 4, 'nombre' => 'Ciencia Ficción', 'cantidad_peliculas' => 25],
                    ['id' => 5, 'nombre' => 'Terror', 'cantidad_peliculas' => 18],
                    ['id' => 6, 'nombre' => 'Fantasía', 'cantidad_peliculas' => 22],
                    ['id' => 7, 'nombre' => 'Romance', 'cantidad_peliculas' => 15],
                    ['id' => 8, 'nombre' => 'Thriller', 'cantidad_peliculas' => 30],
                ];

            case 'actores':
                return [
                    [
                        'id' => 1,
                        'nombre' => 'Robert De Niro',
                        'peliculas' => 18,
                        'imagen' => 'https://m.media-amazon.com/images/M/MV5BMjAwNDU3MzcyOV5BMl5BanBnXkFtZTcwMjc0MTIxMw@@._V1_UY317_CR13,0,214,317_AL_.jpg'
                    ],
                    [
                        'id' => 2,
                        'nombre' => 'Meryl Streep',
                        'peliculas' => 16,
                        'imagen' => 'https://m.media-amazon.com/images/M/MV5BMTU4Mjk5MDExOF5BMl5BanBnXkFtZTcwOTU1MTMyMw@@._V1_.jpg'
                    ],
                    [
                        'id' => 3,
                        'nombre' => 'Antonio Banderas',
                        'peliculas' => 12,
                        'imagen' => 'https://m.media-amazon.com/images/M/MV5BMTUyOTQ3NTYyNF5BMl5BanBnXkFtZTcwMTY2NjIzNQ@@._V1_UX214_CR0,0,214,317_AL_.jpg'
                    ],
                    [
                        'id' => 4,
                        'nombre' => 'Salma Hayek',
                        'peliculas' => 10,
                        'imagen' => 'https://m.media-amazon.com/images/M/MV5BMzkyMTk2NzM2Ml5BMl5BanBnXkFtZTcwNDQ4MjYzMg@@._V1_UY317_CR7,0,214,317_AL_.jpg'
                    ],
                    [
                        'id' => 5,
                        'nombre' => 'Tom Hanks',
                        'peliculas' => 20,
                        'imagen' => 'https://m.media-amazon.com/images/M/MV5BMTQ2MjMwNDA3Nl5BMl5BanBnXkFtZTcwMTA2NDY3NQ@@._V1_.jpg'
                    ],
                    [
                        'id' => 6,
                        'nombre' => 'Leonardo DiCaprio',
                        'peliculas' => 15,
                        'imagen' => 'https://m.media-amazon.com/images/M/MV5BMjI0MTg3MzI0M15BMl5BanBnXkFtZTcwMzQyODU2Mw@@._V1_.jpg'
                    ],
                ];

            default:
                return [];
        }
    }

    /**
     * Get column definitions for each content type.
     *
     * @param string|null $tipo
     * @return array
     */
    private function getColumnsForType($tipo)
    {
        switch ($tipo) {
            case 'peliculas':
                return [
                    'id' => 'ID',
                    'titulo' => 'Título',
                    'anio' => 'Año',
                    'idioma_original' => 'Idioma Original',
                    'duracion' => 'Duración (min)',
                    'categoria' => 'Categoría'
                ];

            case 'categorias':
                return [
                    'id' => 'ID',
                    'nombre' => 'Nombre',
                    'cantidad_peliculas' => 'Cantidad de Películas'
                ];

            case 'actores':
                return [
                    'id' => 'ID',
                    'nombre' => 'Nombre',
                    'peliculas' => 'Películas'
                ];

            default:
                return [];
        }
    }

    public function newFilm()
    {
        $languages = Language::all();
        $categories = Category::all();
        return view('films/new_film', ['languages' => $languages, 'categories' =>$categories]);
    }

    public function contact()
    {
        return view('contact');
    }

    public function submitContact(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Here you can add logic to send emails or store contact messages
        // For now, just redirect with a success message

        return redirect()->back()->with('success', 'Your message has been sent successfully!');
    }
    public function newActor()
    {
        return view('actors/new_actor');
    }
    public function newCat()
    {
        return view('categories/new_category');
    }

    public function newCustomer()
    {
        return view('customers.new_customer');
    }

    public function newAddress()
    {
        return view('address.new_address');
    }

    /**
     * Display the edit form for any item type.
     *
     * @param string $itemType
     * @param int $itemId
     * @return \Illuminate\View\View
     */
    public function editItem($itemType, $itemId)
    {
        return view('edit_item', [
            'itemType' => $itemType,
            'itemId' => $itemId
        ]);
    }
}
