<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return self::baseRules($this->route('vehicle')?->id);
    }

    /**
     * Centralize validation so Livewire and the controller share the same rules.
     */
    public static function baseRules(?int $vehicleId = null): array
    {
        $immatriculationRule = Rule::unique('vehicles', 'immatriculation')->ignore($vehicleId);

        return [
            // Administratif
            'immatriculation' => ['required', 'string', 'max:50', $immatriculationRule],
            'vin' => ['nullable', 'string', 'max:100'],
            'type_carte_grise' => ['nullable', 'string', 'max:100'],
            'proprietaire_legal' => ['nullable', 'string', 'max:150'],
            'service_affecte' => ['nullable', 'string', 'max:120'],
            'responsable_service' => ['nullable', 'string', 'max:120'],
            'lieu_stationnement' => ['nullable', 'string', 'max:150'],
            'date_mise_circulation' => ['nullable', 'date'],
            'date_acquisition' => ['nullable', 'date'],
            'mode_acquisition' => ['nullable', 'string', 'max:120'],
            'puissance_fiscale' => ['nullable', 'numeric', 'min:0'],
            'categorie_fiscale' => ['nullable', 'string', 'max:120'],

            // Technique
            'marque' => ['required', 'string', 'max:120'],
            'modele' => ['required', 'string', 'max:120'],
            'version' => ['nullable', 'string', 'max:120'],
            'categorie_vehicule' => ['nullable', 'string', 'max:120'],
            'carburant' => ['nullable', 'string', Rule::in(['Essence', 'Diesel', 'Electrique', 'Hybride'])],
            'couleur' => ['nullable', 'string', 'max:60'],
            'poids_vide' => ['nullable', 'numeric', 'min:0'],
            'ptac' => ['nullable', 'numeric', 'min:0'],
            'num_moteur' => ['nullable', 'string', 'max:120'],
            'cylindree' => ['nullable', 'numeric', 'min:0'],
            'puissance_din' => ['nullable', 'numeric', 'min:0'],
            'capacite_reservoir' => ['nullable', 'numeric', 'min:0'],
            'capacite_charge' => ['nullable', 'numeric', 'min:0'],
            'nombre_places' => ['nullable', 'integer', 'min:1'],
            'kilometrage_initial' => ['required', 'integer', 'min:0'],
            'kilometrage_actuel' => ['required', 'integer', 'min:0'],
            'heures_moteur' => ['nullable', 'integer', 'min:0'],

            // Financier
            'prix_achat' => ['nullable', 'numeric', 'min:0'],
            'fournisseur_nom' => ['nullable', 'string', 'max:150'],
            'fournisseur_ice' => ['nullable', 'string', 'max:100'],
            'bon_commande_ref' => ['nullable', 'string', 'max:100'],
            'article_budgetaire' => ['nullable', 'string', 'max:120'],
            'duree_amortissement' => ['nullable', 'integer', 'min:0'],
            'mode_amortissement' => ['nullable', 'string', 'max:100'],
            'valeur_nette_comptable' => ['nullable', 'numeric', 'min:0'],

            // Statut & média
            'statut_actuel' => ['required', 'string', Rule::in(['En service', 'En réparation', 'Immobile', 'Hors service', 'Réformé'])],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
