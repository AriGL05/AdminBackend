<?php

namespace App\Http\Controllers;

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
        $cat->first_name = $request->get('first_name');
        $cat->save();
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
        $cat->first_name = $request->get('first_name');
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
        $cat->delete();
    }
}
