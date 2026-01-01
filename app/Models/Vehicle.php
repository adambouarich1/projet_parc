<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'immatriculation',
        'vin',
        'type_carte_grise',
        'proprietaire_legal',
        'service_affecte',
        'responsable_service',
        'lieu_stationnement',
        'date_mise_circulation',
        'date_acquisition',
        'mode_acquisition',
        'puissance_fiscale',
        'categorie_fiscale',
        'marque',
        'modele',
        'version',
        'categorie_vehicule',
        'carburant',
        'couleur',
        'poids_vide',
        'ptac',
        'num_moteur',
        'cylindree',
        'puissance_din',
        'capacite_reservoir',
        'capacite_charge',
        'nombre_places',
        'kilometrage_initial',
        'kilometrage_actuel',
        'heures_moteur',
        'prix_achat',
        'fournisseur_nom',
        'fournisseur_ice',
        'bon_commande_ref',
        'article_budgetaire',
        'duree_amortissement',
        'mode_amortissement',
        'valeur_nette_comptable',
        'statut_actuel',
        'image_path',
    ];

    protected $casts = [
        'date_mise_circulation' => 'date',
        'date_acquisition' => 'date',
        'puissance_fiscale' => 'decimal:2',
        'poids_vide' => 'decimal:2',
        'ptac' => 'decimal:2',
        'cylindree' => 'decimal:2',
        'puissance_din' => 'decimal:2',
        'capacite_reservoir' => 'decimal:2',
        'capacite_charge' => 'decimal:2',
        'prix_achat' => 'decimal:2',
        'valeur_nette_comptable' => 'decimal:2',
    ];
}
