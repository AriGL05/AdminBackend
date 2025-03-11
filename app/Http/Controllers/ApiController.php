<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Actor;

class ApiController extends Controller
{
    public function getActors()
    {
        $actors = Actor::all();
        return response()->json($actors);
    }

    public function getCategories()
    {
        $cat = Category::all();
        return response()->json($cat);
    }

    public function countFilmsCat()
    {
        $cat = Category::all();
        $catcount = $cat->count();
        return response()->json($catcount);
    }
}
