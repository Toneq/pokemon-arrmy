<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BannedPokemonController;
use App\Http\Controllers\CustomPokemonController;
use App\Http\Controllers\InfoController;
use App\Http\Middleware\SuperSecretKey;

Route::middleware([SuperSecretKey::class])->group(function () {
    Route::get('/banned', [BannedPokemonController::class, 'index']);
    Route::post('/banned', [BannedPokemonController::class, 'store']);
    Route::delete('/banned/{name}', [BannedPokemonController::class, 'destroy']);

    Route::apiResource('/custom', CustomPokemonController::class);
});

Route::post('/info', [InfoController::class, 'fetch']);
