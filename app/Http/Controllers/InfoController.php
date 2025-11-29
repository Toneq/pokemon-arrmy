<?php

namespace App\Http\Controllers;

use App\Models\BannedPokemon;
use App\Models\CustomPokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class InfoController extends Controller
{
    public function fetch(Request $request)
    {
        $request->validate([
            'names' => 'required|array',
            'names.*' => 'string'
        ]);

        $banned = BannedPokemon::pluck('name')->toArray();
        $allowed = array_diff($request->names, $banned);

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

            $pokemon = Cache::remember(
                "pokeapi_$name",
                now()->tomorrow()->setTime(12, 0),
                fn () => Http::get(env('POKEAPI_URL') . '/pokemon/' . $name)->json()
            );

            if ($pokemon) {
                $result[] = [
                    'name' => $name,
                    'data' => $pokemon,
                    'source' => 'official'
                ];
            }
        }

        return $result;
    }
}
