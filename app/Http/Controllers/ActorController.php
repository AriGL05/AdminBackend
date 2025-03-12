<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actor;

class ActorController extends Controller
{
    public function index()
    {
        $actors = Actor::withCount('films')->get();
        $formattedActors = $actors->map(function ($actor) {
            return [
                'id' => $actor->actor_id,
                'name' => $actor->first_name . ' ' . $actor->last_name,
                'films_count' => $actor->films_count,
            ];
        });
        return response()->json($formattedActors);
    }

    public function store(Request $request)
    {
       
        $request->validate([
            "first_name" => "required|min:3|max:45",
            "last_name" => "required|min:3|max:45",
        ]);
    
       
        $actor = new Actor();
        $actor->first_name = $request->input('first_name');
        $actor->last_name = $request->input('last_name');
        $actor->save();
    
        
        return redirect()->back()->with('success', 'Actor creado exitosamente');
    }
    public function update(Request $request, int $id)
    {
        $actor = Actor::find($id);
        if (!$actor) {
            return response()->json(["msg" => "Actor no encontrado"], 404);
        }
        $request->validate([
            "first_name" => "required|min:3|max:45",
            "last_name" => "required|min:3|max:45",
        ]);
        $actor->first_name = $request->get('first_name');
        $actor->last_name = $request->get('last_name');
        $actor->last_update = now();
        $actor->save();
    }
    public function edit(int $id)
    {
        $actor = Actor::find($id);
        if (!$actor) {
            return response()->json(["msg" => "Actor no encontrado"], 404);
        }
        return response()->json($actor);
    }
    public function destroy(int $id)
    {
        $actor = Actor::find($id);
        if (!$actor) {
            return response()->json(['error' => 'Actor no encontrado'], 404);
        }
    
        
        if ($actor->films()->exists()) {
            $actor->films()->detach(); 
        }
    
       
        $actor->delete();
    
       
        return response()->json(['success' => 'Actor eliminado exitosamente'], 200);
    }
    

}
