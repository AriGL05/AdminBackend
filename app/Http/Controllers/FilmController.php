<?php

namespace App\Http\Controllers;

use App\Models\Film_Category;
use Illuminate\Http\Request;
use App\Models\Film;
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
        ]);
        $film = new Film();
        $film->title = $request->get('title');
        $film->release_year = $request->get('release_year');
        $film->description = "A movie lol";
        $film->language_id = $request->get('language_id');
        $film->rental_duration = 4;
        $film->length = $request->get('length');
        $film->rental_rate = 0.99;
        $film->replacement_cost = 20.50;
        $film->save();

        $filmId = $film->id;

        $connect = new Film_Category();
        $connect->film_id = $filmId;
        $connect->category_id = $request->get('category_id');
        $connect->save();

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
        $film->description = "A movie lol";
        $film->language_id = $request->get('language_id');
        $film->rental_duration = 4;
        $film->length = $request->get('length');
        $film->rental_rate = 0.99;
        $film->replacement_cost = 20.50;
        $film->save();

        $connect = Film_Category::find($film->id);

        $connect->film_id = $film->id;
        $connect->category_id = $request->get('category_id');
        $connect->save();
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
        $film = Film::find($id);
        if (!$film) {
            return response()->json(["msg" => "film no encontrado"], 404);
        }
        $film->delete();
    }

}
