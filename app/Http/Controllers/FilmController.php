<?php

namespace App\Http\Controllers;

use App\Models\Film_Category;
use App\Models\Inventory;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Film;
use App\Models\Film_Actor;
use App\Models\Film_Text;
use App\Models\Language;

class FilmController extends Controller
{
    public function index()
    {
        $films = Film::select('film.film_id', 'film.title', 'film.release_year', 'language.name as language')
            ->join('language', 'film.language_id', '=', 'language.language_id')
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
        'description' => 'nullable|string' 
    ]);

    $film = new Film();
    $film->title = $request->get('title');
    $film->release_year = $request->get('release_year');
    $film->description = $request->get('description'); 
    $film->language_id = $request->get('language_id');
    $film->rental_duration = 4;
    $film->length = $request->get('length');
    $film->rental_rate = 0.99;
    $film->replacement_cost = 20.50;
    $film->save();

    $filmId = $film->film_id; 

    $connect = new Film_Category();
    $connect->film_id = $filmId;
    $connect->category_id = $request->get('category_id');
    $connect->save();

    return response()->json(["msg" => "Película guardada correctamente"], 201);
}

public function update(Request $request, int $id)
{
    $film = Film::find($id);
    if (!$film) {
        return response()->json(["msg" => "Film no encontrado"], 404);
    }

    $request->validate([
        "title" => "required|min:3|max:128",
        "release_year" => "required",
        'language_id' => 'required',
        'length' => 'required',
        'category_id' => 'required',
        'description' => 'nullable|string'
    ]);

    $film->title = $request->get('title');
    $film->release_year = $request->get('release_year');
    $film->description = $request->get('description');
    $film->language_id = $request->get('language_id');
    $film->rental_duration = 4;
    $film->length = $request->get('length');
    $film->rental_rate = 0.99;
    $film->replacement_cost = 20.50;
    $film->save();

  
    $connect = Film_Category::where('film_id', $film->film_id)->first();
    if ($connect) {
        $connect->category_id = $request->get('category_id');
        $connect->save();
    } else {
        $newConnect = new Film_Category();
        $newConnect->film_id = $film->film_id;
        $newConnect->category_id = $request->get('category_id');
        $newConnect->save();
    }

    return response()->json(["msg" => "Película actualizada correctamente"], 200);
}

    public function edit(int $id)
    {
        $film = Film::with('language:language_id,name')->find($id);
        if (!$film) {
            return response()->json(["msg" => "film no encontrado"], 404);
        }
        return response()->json($film);
    }
    public function destroy(int $id)
{
    Log::info($id); 

    $film = Film::find($id);
    if (!$film) {
        return response()->json(["msg" => "Película no encontrada"], 404);
    }

    try {
       
        Inventory::where('film_id', $id)->delete();
        Film_Actor::where('film_id', $id)->delete();
        Film_Text::where('film_id', $id)->delete();
        Film_Category::where('film_id', $id)->delete();

     
        $film->delete();

       
        return response()->json(["msg" => "Película eliminada correctamente"], 200);
    } catch (\Exception $e) {
        
        Log::error("Error al eliminar la película: " . $e->getMessage());
        return response()->json(["msg" => "Error al eliminar la película"], 500);
    }
}

}
