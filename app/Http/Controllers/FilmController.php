<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Film;

class FilmController extends Controller
{
    public function index()
    {
        $films = Film::with('language')->get();
        return response()->json($films);
    }
    public function store(Request $request)
    {
        $request->validate([
            "title" => "required|min:3|max:128",
            "description" => "required|min:3|max:250",
            'language_id' => 'required',
            'rental_duration' => 'required|integer',
            'rental_rate' => 'required|numeric',
            'replacement_cost' => 'required|numeric',
        ]);
        $film = new Film();
        $film->title = $request->get('title');
        $film->description = $request->get('description');
        $film->language_id = $request->get('language_id');
        $film->rental_duration = $request->get('rental_duration');
        $film->rental_rate = $request->get('rental_rate');
        $film->replacement_cost = $request->get('replacement_cost');
        $film->save();
    }
    public function update(Request $request, int $id)
    {
        $film = Film::find($id);
        if (!$film) {
            return response()->json(["msg" => "film no encontrado"], 404);
        }
        $request->validate([
            "title" => "required|min:3|max:128",
            "description" => "required|min:3|max:250",
            'language_id' => 'required',
            'rental_duration' => 'required|integer',
            'rental_rate' => 'required|numeric',
            'replacement_cost' => 'required|numeric',
        ]);
        $film->title = $request->get('title');
        $film->description = $request->get('description');
        $film->language_id = $request->get('language_id');
        $film->rental_duration = $request->get('rental_duration');
        $film->rental_rate = $request->get('rental_rate');
        $film->replacement_cost = $request->get('replacement_cost');
        $film->save();
    }
    public function edit(int $id)
    {
        $film = Film::find($id);
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
