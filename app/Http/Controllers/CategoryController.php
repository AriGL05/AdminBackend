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
        $cat->name = $request->get('name'); 
        $cat->save();

        return redirect()->back()->with('success', 'Categoría creada exitosamente');
    }

    public function update(Request $request, int $id)
    {
        $cat = Category::find($id);
        if (!$cat) {
            return redirect()->back()->with('error', 'Categoría no encontrada'); 
        }
    
        $request->validate([
            "name" => "required|min:3|max:25", 
        ]);
    
        $cat->name = $request->get('name'); 
        $cat->save();
    
        return redirect()->back()->with('success', 'Categoría actualizada exitosamente'); 
    }

    public function edit(int $id)
    {
        $cat = Category::find($id);
        if (!$cat) {
            return response()->json(["msg" => "Categoría no encontrada"], 404);
        }
        return response()->json($cat);
    }

    public function destroy(int $id)
{
    $cat = Category::find($id);
    if (!$cat) {
        return response()->json(['error' => 'Categoría no encontrada'], 404);
    }

 
    $cat->film_Category()->delete();

   
    $cat->delete();


    return response()->json(['success' => 'Categoría eliminada exitosamente'], 200);
}
}