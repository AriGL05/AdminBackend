<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actor;

class ActorController extends Controller
{
    public function index()
    {
        $actors = Actor::withCount('films')->get();
        return response()->json($actors);
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
            return response()->json(["msg" => "Actor no encontrado"], 404);
        }
        $actor->delete();
    }

}
