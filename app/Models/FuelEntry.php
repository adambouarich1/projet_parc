<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'mission_order_id',
        'user_id',
        'date_plein',
        'quantite_litres',
        'prix_unitaire',
        'montant_total',
        'kilometrage',
        'station',
        'type_carburant',
        'numero_bon',
        'observations',
    ];

    protected $casts = [
        'date_plein' => 'date',
        'quantite_litres' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
        'montant_total' => 'decimal:2',
    ];

    // Relations
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function missionOrder()
    {
        return $this->belongsTo(MissionOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Calcul consommation L/100km (par rapport au plein précédent)
    public function getConsommationAttribute(): ?float
    {
        $previous = self::where('vehicle_id', $this->vehicle_id)
            ->where('id', '<', $this->id)
            ->orderBy('id', 'desc')
            ->first();

        if ($previous && $this->kilometrage > $previous->kilometrage) {
            $kmParcourus = $this->kilometrage - $previous->kilometrage;
            return round(($this->quantite_litres / $kmParcourus) * 100, 2);
        }

        return null;
    }
}