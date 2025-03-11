<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Actor;
use App\Models\Film;
use App\Models\Film_Actor;

class ApiController extends Controller
{
    public function getActors()
    {
        $actors = Actor::all();
        return response()->json($actors);
    }
    public function getLanguages()
    {
        $languages = Language::all();
        return response()->json($languages);
    }
    public function getCategories()
    {
        $categories = Category::all();
        return response()->json($categories);
    }
}
