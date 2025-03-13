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
        return response()->json(['success' => true, 'message' => 'Film added successfully']);
    }
    public function update(Request $request, int $id)
    {
        $film = Film::find($id);
        if (!$film) {
            return response()->json(["msg" => "film no encontrado"], 404);
        }
        $request->validate([
            "title" => "required|min:3|max:128",
            "release_year" => "required",
            'language_id' => 'required',
            'length' => 'required',
            'category_id' => 'required',
        ]);
        $film->title = $request->get('title');
        $film->release_year = $request->get('release_year');
        $film->language_id = $request->get('language_id');
        $film->rental_duration = 4;
        $film->length = $request->get('length');
        $film->rental_rate = 0.99;
        $film->replacement_cost = 20.50;
        $film->save();

        $connect = Film_Category::where('film_id', $film->film_id)->first();
        if (!$connect) {
            $connect = new Film_Category();
            $connect->film_id = $film->film_id;
        }
        $connect->category_id = $request->get('category_id');
        $connect->save();

        return response()->json(['success' => true, 'message' => 'Film updated successfully']);
    }
    public function edit(int $id)
    {
        $film = Film::with('language:language_id,name')->find($id);
        if (!$film) {
            return response()->json(["msg" => "film no encontrado"], 404);
        }

        // Get the associated category
        $filmCategory = Film_Category::where('film_id', $id)->first();
        if ($filmCategory) {
            $film->category_id = $filmCategory->category_id;
        }

        return response()->json($film);
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
