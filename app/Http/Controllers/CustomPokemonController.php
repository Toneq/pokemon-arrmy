<?php

namespace App\Http\Controllers;

use App\Models\CustomPokemon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;

class CustomPokemonController extends Controller
{
    public function index(): JsonResponse
    {
        $pokemons = CustomPokemon::all();

        return response()->json([
            'data' => $pokemons,
            'count' => $pokemons->count()
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:custom_pokemons,name',
                'data' => 'required|array'
            ]);

            $pokeApiResponse = Http::get(env('POKEAPI_URL') . '/pokemon/' . strtolower($validated['name']));
            if ($pokeApiResponse->ok()) {
                return response()->json([
                    'message' => 'Pokemon exists in PokeAPI'
                ], 409);
            }

            $pokemon = CustomPokemon::create($validated);

            return response()->json([
                'message' => 'Custom Pokemon created successfully',
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

    public function show($id): JsonResponse
    {
        try {
            $pokemon = CustomPokemon::findOrFail($id);

            return response()->json([
                'data' => $pokemon
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Custom Pokemon not found',
                'id' => $id
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $pokemon = CustomPokemon::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|unique:custom_pokemons,name,' . $pokemon->id,
                'data' => 'sometimes|required|array'
            ]);

            $pokemon->update($validated);

            return response()->json([
                'message' => 'Custom Pokemon updated successfully',
                'data' => $pokemon
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Custom Pokemon not found',
                'id' => $id
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $pokemon = CustomPokemon::findOrFail($id);
            $pokemon->delete();

            return response()->json([
                'message' => 'Custom Pokemon deleted successfully',
                'deleted' => $id
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Custom Pokemon not found',
                'id' => $id
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
