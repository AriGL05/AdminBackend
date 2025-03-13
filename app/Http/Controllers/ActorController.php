<?php

namespace App\Http\Controllers;

use App\Models\Film_Actor;
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
        $actor->first_name = $request->get('first_name');
        $actor->last_name = $request->get('last_name');
        $actor->save();
        return response()->json(['success' => true, 'message' => 'Actore added successfully']);
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
        return response()->json(['success' => true, 'message' => 'Actor updated successfully']);
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
            return response()->json(["msg" => "Actor no encontrado"], 404);
        }
        Film_Actor::where('film_id', $id)->delete();
        $actor->delete();
    }

    public function about(int $id)
    {
        $actor = Actor::find($id);
        if (!$actor) {
            abort(404, 'Actor not found');
        }
        return view('actors/about_actor', ['actor' => $actor]);
    }

}
