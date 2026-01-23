<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
    use HasFactory;

    public const TYPE_ENTRETIEN = 'entretien';
    public const TYPE_REPARATION = 'reparation';
    public const TYPE_CONTROLE_TECHNIQUE = 'controle_technique';
    public const TYPE_AUTRE = 'autre';

    public const TYPES = [
        self::TYPE_ENTRETIEN => 'Entretien',
        self::TYPE_REPARATION => 'Réparation',
        self::TYPE_CONTROLE_TECHNIQUE => 'Contrôle technique',
        self::TYPE_AUTRE => 'Autre',
    ];

    public const STATUT_PLANIFIE = 'planifie';
    public const STATUT_EN_COURS = 'en_cours';
    public const STATUT_TERMINE = 'termine';
    public const STATUT_ANNULE = 'annule';
    public const STATUT_ARCHIVE = 'archive';

    public const STATUTS = [
        self::STATUT_PLANIFIE => 'Planifié',
        self::STATUT_EN_COURS => 'En cours',
        self::STATUT_TERMINE => 'Terminé',
        self::STATUT_ANNULE => 'Annulé',
        self::STATUT_ARCHIVE => 'Archivé',
    ];

    public const RESULTATS_CT = [
        'favorable' => 'Favorable',
        'defavorable' => 'Défavorable',
        'contre_visite' => 'Contre-visite',
    ];

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'type',
        'titre',
        'description',
        'date_intervention',
        'date_prochaine',
        'kilometrage',
        'km_prochaine',
        'cout_pieces',
        'cout_main_oeuvre',
        'cout_total',
        'prestataire',
        'numero_facture',
        'assureur',
        'numero_police',
        'date_expiration_assurance',
        'date_expiration_ct',
        'resultat_ct',
        'statut',
        'observations',
    ];

    protected $casts = [
        'date_intervention' => 'date',
        'date_prochaine' => 'date',
        'date_expiration_assurance' => 'date',
        'date_expiration_ct' => 'date',
        'cout_pieces' => 'decimal:2',
        'cout_main_oeuvre' => 'decimal:2',
        'cout_total' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    public function getResultatCtLabelAttribute(): ?string
    {
        return $this->resultat_ct ? self::RESULTATS_CT[$this->resultat_ct] : null;
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