<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function getActors()
    {
        $actors = DB::select("SELECT * FROM actor;");
        return response()->json($actors);
    }

    public function getCategories()
    {
        $cat = DB::select("SELECT name FROM category;");
        return response()->json($cat);
    }

    public function countFilmsCat()
    {
        $cat = DB::select("SELECT name FROM category;");
        return response()->json($cat);
    }
}
