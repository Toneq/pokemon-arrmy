<?php

namespace App\Http\Controllers;

use App\Models\CustomPokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CustomPokemonController extends Controller
{
    public function index()
    {
        return CustomPokemon::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:custom_pokemons',
            'data' => 'required|array'
        ]);

        if (Http::get(env('POKEAPI_URL') . '/pokemon/' . $request->name)->ok()) {
            return response()->json(['error' => 'Pokemon exists in PokeAPI'], 409);
        }

        return CustomPokemon::create($request->only('name', 'data'));
    }

    public function show($id)
    {
        return CustomPokemon::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $pokemon = CustomPokemon::findOrFail($id);
        $pokemon->update($request->only('name', 'data'));

        return $pokemon;
    }

    public function destroy($id)
    {
        CustomPokemon::destroy($id);

        return response()->json([
            'deleted' => $id
        ]);
    }
}
