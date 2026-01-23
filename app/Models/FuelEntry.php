<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelEntry extends Model
{
    use HasFactory;

    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_VALIDE = 'valide';
    public const STATUT_REFUSE = 'refuse';
    public const STATUT_ARCHIVE = 'archive';

    public const STATUTS = [
        self::STATUT_EN_ATTENTE => 'En attente',
        self::STATUT_VALIDE => 'Validé',
        self::STATUT_REFUSE => 'Refusé',
        self::STATUT_ARCHIVE => 'Archivé',
    ];

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
        'statut',
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

    // Accesseurs
    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    // Calcul consommation L/100km
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

    // Scopes
    public function scopeNonArchive($query)
    {
        return $query->where(function($q) {
            $q->where('statut', '!=', self::STATUT_ARCHIVE)
              ->orWhereNull('statut');
        });
    }

    public function scopeArchive($query)
    {
        return $query->where('statut', self::STATUT_ARCHIVE);
    }
}