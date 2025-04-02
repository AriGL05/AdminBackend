<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Film_Category;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\Film;
use App\Models\Film_Actor;
use App\Models\Film_Text;
use App\Models\Language;
use App\Models\Rental;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FilmController extends Controller
{
    public function index()
    {
        $films = Film::select('film.film_id', 'film.title', 'film.release_year', 'language.name as language')
            ->join('language', 'film.language_id', '=', 'language.language_id')
            ->orderBy('film.film_id')
            ->get();
        return response()->json($films);
    }
    public function store(Request $request)
    {
        $request->validate([
            "title" => "required|min:3|max:128",
            "release_year" => "required",
            'language_id' => 'required',
            'length' => 'required',
            'category_id' => 'required',
            'description' => 'required',
        ]);
        $film = Film::create([
            'title' => $request->get('title'),
            'release_year' => $request->get('release_year'),
            'description' => $request->get('description'),
            'language_id' => $request->get('language_id'),
            'rental_duration' => 4,
            'length' => $request->get('length'),
            'rental_rate' => 0.99,
            'replacement_cost' => 20.50,
        ]);

        Log::info($film);

        $filmId = $film->film_id;

        $connect = new Film_Category();
        $connect->film_id = $filmId;
        $connect->category_id = $request->get('category_id');
        $connect->save();
        return redirect()->route('tablas', ['tipo' => 'peliculas']);
    }
    public function update(Request $request, int $id)
    {
        try {
            // Find the film or return 404
            $film = Film::find($id);
            if (!$film) {
                return response()->json(["error" => "Film not found"], 404);
            }

            // Validate the request
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                "title" => "required|min:3|max:128",
                "description" => "required",
                "release_year" => "required|numeric|min:1900|max:2099",
                'language_id' => 'required|exists:language,language_id',
                'length' => 'required|numeric|min:1',
                'rental_duration' => 'required|numeric|min:1',
                'rental_rate' => 'required|numeric|min:0',
                'replacement_cost' => 'required|numeric|min:0',
                'rating' => 'nullable|in:G,PG,PG-13,R,NC-17',
                'special_features' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            // Update the film
            $film->title = $request->title;
            $film->description = $request->description;
            $film->release_year = $request->release_year;
            $film->language_id = $request->language_id;
            $film->original_language_id = $request->original_language_id;
            $film->rental_duration = $request->rental_duration;
            $film->rental_rate = $request->rental_rate;
            $film->length = $request->length;
            $film->replacement_cost = $request->replacement_cost;
            $film->rating = $request->rating;
            $film->special_features = $request->special_features;
            $film->last_update = now();

            $film->save();

            // Handle categories if provided
            if ($request->has('categories')) {
                // Delete existing categories
                Film_Category::where('film_id', $id)->delete();

                // Add new categories
                $categories = $request->categories;
                if (is_array($categories)) {
                    foreach ($categories as $categoryId) {
                        Film_Category::create([
                            'film_id' => $id,
                            'category_id' => $categoryId,
                            'last_update' => now()
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Film updated successfully',
                'film_id' => $film->film_id
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating film: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update film',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get film data for editing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            // Get film with basic details
            $film = DB::table('film')
                ->where('film_id', $id)
                ->first();

            if (!$film) {
                return response()->json(['error' => 'Film not found'], 404);
            }

            // Get film categories
            $categories = DB::table('film_category')
                ->join('category', 'film_category.category_id', '=', 'category.category_id')
                ->where('film_category.film_id', $id)
                ->select('category.category_id', 'category.name')
                ->get();

            // Add the categories to the film data
            $filmData = (array)$film;
            $filmData['categories'] = $categories;

            return response()->json($filmData);
        } catch (\Exception $e) {
            \Log::error('Error retrieving film data: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error retrieving film data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        $film = Film::find($id);
        if (!$film) {
            return response()->json(["msg" => "film no encontrado"], 404);
        }
        $inventories = Inventory::where('film_id', $id)->get();
        foreach ($inventories as $inventory) {
            Rental::where('inventory_id', $inventory->inventory_id)->delete();
        }
        Inventory::where('film_id', $id)->delete();
        Film_Actor::where('film_id', $id)->delete();
        Film_Text::where('film_id', $id)->delete();
        Film_Category::where('film_id', $id)->delete();
        $film->delete();
    }

    public function about(int $id)
    {
        $film = Film::find($id);
        if (!$film) {
            abort(404, 'Film not found');
        }
        $language = Language::find($film->language_id);
        $languages = Language::all();

        $categories = Category::all();
        $film_category = Film_Category::where('film_id', $film->film_id)->first();
        $category = Category::where('category_id', $film_category->category_id)->first();

        return view('films/about_film', [
            'film' => $film,
            'language' => $language,
            'languages' => $languages,
            'categories' => $categories,
            'category' => $category
        ]);
    }

}
