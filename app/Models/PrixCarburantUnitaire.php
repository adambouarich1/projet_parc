<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrixCarburantUnitaire extends Model
{
    protected $table = 'prix_carburant_unitaire';
    
    protected $fillable = ['type_carburant', 'prix'];

    // Helper pour récupérer le prix
    public static function getPrix(string $type): float
    {
        $record = static::where('type_carburant', $type)->first();
        return $record ? (float) $record->prix : 0.00;
    }

    // Helper pour mettre à jour le prix
    public static function setPrix(string $type, float $prix): void
    {
        static::updateOrCreate(
            ['type_carburant' => $type],
            ['prix' => $prix]
        );
    }
}