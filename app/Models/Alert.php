<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    // Types d'alertes
    public const TYPE_PERMIS_EXPIRE = 'permis_expire';
    public const TYPE_PERMIS_BIENTOT = 'permis_bientot';
    public const TYPE_ASSURANCE_EXPIREE = 'assurance_expiree';
    public const TYPE_ASSURANCE_BIENTOT = 'assurance_bientot';
    public const TYPE_CT_EXPIRE = 'ct_expire';
    public const TYPE_CT_BIENTOT = 'ct_bientot';
    public const TYPE_VIDANGE_KM = 'vidange_km';
    public const TYPE_VIDANGE_DATE = 'vidange_date';
    public const TYPE_VIGNETTE_EXPIREE = 'vignette_expiree';
    public const TYPE_VIGNETTE_BIENTOT = 'vignette_bientot';
    public const TYPE_AUTRE = 'autre';

    public const TYPES = [
        self::TYPE_PERMIS_EXPIRE => 'Permis expiré',
        self::TYPE_PERMIS_BIENTOT => 'Permis bientôt expiré',
        self::TYPE_ASSURANCE_EXPIREE => 'Assurance expirée',
        self::TYPE_ASSURANCE_BIENTOT => 'Assurance bientôt expirée',
        self::TYPE_CT_EXPIRE => 'CT expiré',
        self::TYPE_CT_BIENTOT => 'CT bientôt expiré',
        self::TYPE_VIDANGE_KM => 'Vidange km atteint',
        self::TYPE_VIDANGE_DATE => 'Vidange date atteinte',
        self::TYPE_VIGNETTE_EXPIREE => 'Vignette expirée',
        self::TYPE_VIGNETTE_BIENTOT => 'Vignette bientôt expirée',
        self::TYPE_AUTRE => 'Autre',
    ];

    // Priorités
    public const PRIORITE_BASSE = 'basse';
    public const PRIORITE_MOYENNE = 'moyenne';
    public const PRIORITE_HAUTE = 'haute';
    public const PRIORITE_CRITIQUE = 'critique';

    public const PRIORITES = [
        self::PRIORITE_BASSE => 'Basse',
        self::PRIORITE_MOYENNE => 'Moyenne',
        self::PRIORITE_HAUTE => 'Haute',
        self::PRIORITE_CRITIQUE => 'Critique',
    ];

    // Statuts
    public const STATUT_ACTIVE = 'active';
    public const STATUT_VUE = 'vue';
    public const STATUT_TRAITEE = 'traitee';
    public const STATUT_IGNOREE = 'ignoree';
    public const STATUT_ARCHIVEE = 'archivee';

    public const STATUTS = [
        self::STATUT_ACTIVE => 'Active',
        self::STATUT_VUE => 'Vue',
        self::STATUT_TRAITEE => 'Traitée',
        self::STATUT_IGNOREE => 'Ignorée',
        self::STATUT_ARCHIVEE => 'Archivée',
    ];

    protected $fillable = [
        'type',
        'priorite',
        'alertable_type',
        'alertable_id',
        'titre',
        'message',
        'date_echeance',
        'jours_restants',
        'statut',
        'treated_by',
        'treated_at',
        'notes_traitement',
        'viewed_by',
        'viewed_at',
    ];

    protected $casts = [
        'date_echeance' => 'date',
        'treated_at' => 'datetime',
        'viewed_at' => 'datetime',
    ];

    // Relations
    public function alertable()
    {
        return $this->morphTo();
    }

    public function treatedBy()
    {
        return $this->belongsTo(User::class, 'treated_by');
    }

    public function viewedBy()
    {
        return $this->belongsTo(User::class, 'viewed_by');
    }

    // Accesseurs
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getPrioriteLabelAttribute(): string
    {
        return self::PRIORITES[$this->priorite] ?? $this->priorite;
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('statut', self::STATUT_ACTIVE);
    }

    public function scopeNonTraitee($query)
    {
        return $query->whereIn('statut', [self::STATUT_ACTIVE, self::STATUT_VUE]);
    }

    public function scopeCritique($query)
    {
        return $query->where('priorite', self::PRIORITE_CRITIQUE);
    }

    public function scopeHaute($query)
    {
        return $query->whereIn('priorite', [self::PRIORITE_HAUTE, self::PRIORITE_CRITIQUE]);
    }
public function scopeArchive($query)
{
    return $query->whereIn('statut', [self::STATUT_TRAITEE, self::STATUT_IGNOREE, self::STATUT_ARCHIVEE]);
}

public function scopeNonArchive($query)
{
    return $query->whereIn('statut', [self::STATUT_ACTIVE, self::STATUT_VUE]);
}
}