<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Marque extends Model
{
    protected $fillable = ['nom'];

    public function modeles(): HasMany
    {
        return $this->hasMany(Modele::class);
    }
}