<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\BannedPokemon;
use App\Models\CustomPokemon;

class InfoController extends Controller
{
    public function fetch(Request $request)
    {
        try {
            // Walidacja
            $validated = $request->validate([
                'names' => 'required|array',
                'names.*' => 'string'
            ]);

            $banned = BannedPokemon::pluck('name')->toArray();
            $allowed = array_diff($validated['names'], $banned);

            $result = [];

            foreach ($allowed as $name) {
                $custom = CustomPokemon::where('name', $name)->first();
                if ($custom) {
                    $result[] = [
                        'name' => $name,
                        'data' => $custom->data,
                        'source' => 'custom'
                    ];
                    continue;
                }

                try {
                    $pokemon = Cache::remember(
                        "pokeapi_$name",
                        now()->tomorrow()->setTime(12, 0),
                        fn () => Http::get(env('POKEAPI_URL') . '/pokemon/' . strtolower($name))->throw()->json()
                    );

                    $result[] = [
                        'name' => $name,
                        'data' => $pokemon,
                        'source' => 'official'
                    ];

                } catch (\Illuminate\Http\Client\RequestException $e) {
                    $result[] = [
                        'name' => $name,
                        'data' => null,
                        'source' => 'not_found',
                        'error' => 'Pokemon not found in PokeAPI'
                    ];
                }
            }

            return response()->json([
                'message' => 'Fetch completed',
                'data' => $result
            ]);

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
}
