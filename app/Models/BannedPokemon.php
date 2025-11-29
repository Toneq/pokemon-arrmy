<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannedPokemon extends Model
{
    protected $fillable = ['name'];
    protected $table = 'banned_pokemons';
}
