<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Film;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    /**
     * Display a listing of languages.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $languages = DB::table('language')
            ->leftJoin('film', 'language.language_id', '=', 'film.language_id')
            ->select('language.*', DB::raw('count(film.film_id) as film_count'))
            ->groupBy('language.language_id', 'language.name', 'language.last_update')
            ->get();

        return response()->json($languages);
    }

    /**
     * Store a newly created language in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:20|unique:language,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $language = new Language();
        $language->name = $request->name;
        $language->save();

        return response()->json([
            'message' => 'Idioma agregado correctamente',
            'language' => $language
        ], 201);
    }

    /**
     * Get language for editing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $language = Language::find($id);

        if (!$language) {
            return response()->json(['error' => 'Idioma no encontrado'], 404);
        }

        return response()->json($language);
    }

    /**
     * Update the specified language in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:20|unique:language,name,' . $id . ',language_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $language = Language::find($id);

        if (!$language) {
            return response()->json(['error' => 'Idioma no encontrado'], 404);
        }

        $language->name = $request->name;
        $language->save();

        return response()->json([
            'message' => 'Idioma actualizado correctamente',
            'language' => $language
        ]);
    }

    /**
     * Remove the specified language from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $language = Language::find($id);

        if (!$language) {
            return response()->json(['error' => 'Idioma no encontrado'], 404);
        }

        // Check if language is in use
        $filmsCount = Film::where('language_id', $id)->count();

        if ($filmsCount > 0) {
            return response()->json([
                'error' => 'No se puede eliminar este idioma porque está siendo utilizado por ' . $filmsCount . ' películas.',
                'details' => [
                    'film_count' => $filmsCount
                ]
            ], 422);
        }

        $language->delete();

        return response()->json(['message' => 'Idioma eliminado correctamente']);
    }
}
