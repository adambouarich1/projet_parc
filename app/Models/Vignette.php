<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Vignette extends Model
{
    use HasFactory;

    public const STATUT_ACTIVE = 'active';
    public const STATUT_EXPIREE = 'expiree';
    public const STATUT_ARCHIVEE = 'archivee';

    public const STATUTS = [
        self::STATUT_ACTIVE => 'Active',
        self::STATUT_EXPIREE => 'Expirée',
        self::STATUT_ARCHIVEE => 'Archivée',
    ];

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'annee',
        'date_debut',
        'date_expiration',
        'montant',
        'reference_paiement',
        'date_paiement',
        'statut',
        'observations',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_expiration' => 'date',
        'date_paiement' => 'date',
        'montant' => 'decimal:2',
    ];

    // Relations
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
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

    public function getJoursRestantsAttribute(): int
    {
        return Carbon::today()->diffInDays($this->date_expiration, false);
    }

    public function getIsExpireeAttribute(): bool
    {
        return $this->date_expiration < Carbon::today();
    }

    public function getIsExpireBientotAttribute(): bool
    {
        $jours = $this->jours_restants;
        return $jours >= 0 && $jours <= 30;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('statut', self::STATUT_ACTIVE);
    }

    public function scopeNonArchivee($query)
    {
        return $query->where('statut', '!=', self::STATUT_ARCHIVEE);
    }

    public function scopeArchive($query)
    {
        return $query->where('statut', self::STATUT_ARCHIVEE);
    }

    public function scopeExpiree($query)
    {
        return $query->where('date_expiration', '<', Carbon::today());
    }

    public function scopeExpireBientot($query, int $jours = 30)
    {
        return $query->whereBetween('date_expiration', [Carbon::today(), Carbon::today()->addDays($jours)]);
    }
}