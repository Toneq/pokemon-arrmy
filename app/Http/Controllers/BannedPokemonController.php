<?php

namespace App\Http\Controllers;

use App\Models\BannedPokemon;
use Illuminate\Http\Request;

class BannedPokemonController extends Controller
{
    public function index()
    {
        return BannedPokemon::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:banned_pokemons'
        ]);

        return BannedPokemon::create($request->only('name'));
    }

    public function destroy(string $name)
    {
        BannedPokemon::where('name', $name)->delete();

        return response()->json([
            'deleted' => $name
        ]);
    }
}
