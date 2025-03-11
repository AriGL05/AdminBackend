<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Actor;
use App\Models\Film;
use App\Models\Film_Actor;

class ApiController extends Controller
{
    public function getActors()
    {
        $actors = Actor::withCount('films')->get();
        return response()->json($actors);
    }

    public function getCategories()
    {
        $cat = Category::withCount('film_Category')->get();
        return response()->json($cat);
    }



    public function getFilms()
    {
        $films = Film::with('language')->get();
        return response()->json($films);
    }
    public function countFilmsCat()
    {
        $cat = Category::all();
        $catcount = $cat->count();
        return response()->json($catcount);
    }
}
