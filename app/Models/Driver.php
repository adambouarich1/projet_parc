<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'nom',
        'prenom',
        'matricule',
        'cin',
        'date_naissance',
        'photo_path',
        'service_affecte',
        'responsable_hierarchique',
        'poste_occupe',
        'telephone',
        'email_pro',
        'num_permis',
        'date_delivrance',
        'date_expiration',
        'categories',
        'scan_permis_path',
        'statut_actuel',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_delivrance' => 'date',
        'date_expiration' => 'date',
    ];
}
