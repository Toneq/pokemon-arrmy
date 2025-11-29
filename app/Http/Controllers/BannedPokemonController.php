<?php

namespace App\Http\Controllers;

use App\Models\BannedPokemon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BannedPokemonController extends Controller
{
    public function index()
    {
        $pokemons = BannedPokemon::all();

        return response()->json([
            'data' => $pokemons,
            'count' => $pokemons->count()
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:banned_pokemons,name'
            ]);

            $pokemon = BannedPokemon::create($validated);

            return response()->json([
                'message' => 'Pokemon banned successfully',
                'data' => $pokemon
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $name)
    {
        try {
            $pokemon = BannedPokemon::where('name', $name)->firstOrFail();
            $pokemon->delete();

            return response()->json([
                'message' => 'Pokemon removed from banned list',
                'deleted' => $name
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Pokemon not found',
                'name' => $name
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
