<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissionOrder extends Model
{
    use HasFactory;

    // Les statuts possibles
    public const STATUT_BROUILLON = 'brouillon';
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_VALIDE = 'valide';
    public const STATUT_REJETE = 'rejete';
    public const STATUT_EN_COURS = 'en_cours';
    public const STATUT_DEPART_ANTICIPE = 'depart_anticipe';
    public const STATUT_TERMINE_ATTENTE = 'termine_attente';
    public const STATUT_CLOTURE = 'cloture';
    public const STATUT_ANNULE = 'annule';
    public const STATUT_ARCHIVE = 'archive';

    public const STATUTS = [
        self::STATUT_BROUILLON => 'Brouillon',
        self::STATUT_EN_ATTENTE => 'En attente',
        self::STATUT_VALIDE => 'Validé',
        self::STATUT_REJETE => 'Rejeté',
        self::STATUT_EN_COURS => 'En cours',
        self::STATUT_DEPART_ANTICIPE => 'Départ anticipé',
        self::STATUT_TERMINE_ATTENTE => 'Terminé, attente de clôturation',
        self::STATUT_CLOTURE => 'Clôturé',
        self::STATUT_ANNULE => 'Annulé',
        self::STATUT_ARCHIVE => 'Archivé',
    ];

    protected $fillable = [
        'reference',
        'user_id',
        'validated_by',
        'vehicle_id',
        'driver_id',
        'objet',
        'description',
        'destination',
        'lieu_depart',
        'date_depart',
        'date_retour_prevue',
        'date_retour_effective',
        'km_depart',
        'km_retour',
        'statut',
        'motif_rejet',
        'observations',
        'validated_at',
        'started_at',
        'closed_at',
        'archived_at',
    ];

    protected $casts = [
        'date_depart' => 'datetime',
        'date_retour_prevue' => 'datetime',
        'date_retour_effective' => 'datetime',
        'validated_at' => 'datetime',
        'started_at' => 'datetime',
        'closed_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    // Génère une référence unique (ex: OM-2026-00001)
    public static function generateReference(): string
    {
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('OM-%s-%05d', $year, $last);
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    // Helpers pour le statut
    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    public function isPending(): bool
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

    public function canBeValidated(): bool
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

    public function canBeStarted(): bool
    {
        return $this->statut === self::STATUT_VALIDE;
    }

    public function canBeClosed(): bool
    {
        return in_array($this->statut, [
            self::STATUT_EN_COURS,
            self::STATUT_DEPART_ANTICIPE,
            self::STATUT_TERMINE_ATTENTE,
        ]);
    }

    // Calcul des km parcourus
    public function getKmParcourusAttribute(): ?int
    {
        if ($this->km_depart && $this->km_retour) {
            return $this->km_retour - $this->km_depart;
        }
        return null;
    }
    public function scopeNonArchive($query)
{
    return $query->where('statut', '!=', self::STATUT_ARCHIVE);
}

public function scopeArchive($query)
{
    return $query->where('statut', self::STATUT_ARCHIVE);
}
}