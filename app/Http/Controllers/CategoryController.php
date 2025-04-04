<?php

namespace App\Http\Controllers;

use App\Models\Film_Category;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $cat = Category::withCount('film_Category')->get();
        return response()->json($cat);
    }
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|min:3|max:25",
        ]);
        $cat = new Category();
        $cat->name = $request->get('name');
        $cat->save();
        return redirect()->route('tablas', ['tipo' => 'categorias']);
    }
    public function update(Request $request, int $id)
    {
        $cat = Category::find($id);
        if (!$cat) {
            return response()->json(["msg" => "Catergory no encontrado"], 404);
        }
        $request->validate([
            "name" => "required|min:3|max:25",
        ]);
        $cat->name = $request->get('name');
        $cat->save();
    }
    public function edit(int $id)
    {
        $cat = Category::find($id);
        if (!$cat) {
            return response()->json(["msg" => "Catergory no encontrado"], 404);
        }
        return response()->json($cat);
    }
    public function destroy(int $id)
    {
        $cat = Category::find($id);
        if (!$cat) {
            return response()->json(["msg" => "Catergory no encontrado"], 404);
        }
        Film_Category::where('film_id', $id)->delete();
        $cat->delete();
    }
}
