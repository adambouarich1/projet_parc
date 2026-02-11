<?php

namespace App\Livewire;

use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Vehicles extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';

    public array $filters = [
        'service_affecte' => '',
        'categorie_vehicule' => '',
        'carburant' => '',
        'statut_actuel' => '',
    ];

    public array $form = [];

    public ?int $editingId = null;
    public ?string $currentImagePath = null;
    public $image;
    public ?Vehicle $detailVehicle = null;

    public bool $showFormModal = false;
    public bool $showDetailsModal = false;

    // CORRECTION ICI : Utilisation de tableaux associatifs [Clé => Valeur]
    // Cela garantit que la valeur envoyée au serveur est bien le texte (ex: "Essence") 
    // et non l'index numérique (0, 1, 2...), ce qui corrige l'erreur de validation.
    public array $carburants = [
        'Essence' => 'Essence', 
        'Diesel' => 'Diesel', 
        'Electrique' => 'Electrique', 
        'Hybride' => 'Hybride'
    ];

    public array $categories = [
        'Léger' => 'Léger', 
        'Utilitaire' => 'Utilitaire', 
        'Camion' => 'Camion', 
        'Engin' => 'Engin', 
        'Autre' => 'Autre'
    ];

    public array $statuts = [
        'En service' => 'En service', 
        'En réparation' => 'En réparation', 
        'Immobile' => 'Immobile', 
        'Hors service' => 'Hors service', 
        'Réformé' => 'Réformé'
    ];

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        $vehicles = Vehicle::query()
            ->when($this->search, fn ($query) => 
                $query->where('immatriculation', 'like', '%' . $this->search . '%')
                      ->orWhere('marque', 'like', '%' . $this->search . '%')
            )
            ->when($this->filters['service_affecte'], fn ($query, $value) => $query->where('service_affecte', 'like', '%' . $value . '%'))
            ->when($this->filters['categorie_vehicule'], fn ($query, $value) => $query->where('categorie_vehicule', $value))
            ->when($this->filters['carburant'], fn ($query, $value) => $query->where('carburant', $value))
            ->when($this->filters['statut_actuel'], fn ($query, $value) => $query->where('statut_actuel', $value))
            ->latest()
            ->paginate(12);

        return view('livewire.vehicles', [
            'vehicles' => $vehicles,
        ]);
    }

    public function updatingFilters(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->form = [
            'immatriculation' => '',
            'vin' => '',
            'type_carte_grise' => '',
            'proprietaire_legal' => '',
            'service_affecte' => '',
            'responsable_service' => '',
            'lieu_stationnement' => '',
            'date_mise_circulation' => '',
            'date_acquisition' => '',
            'mode_acquisition' => '',
            'puissance_fiscale' => null,
            'categorie_fiscale' => '',
            'marque' => '',
            'modele' => '',
            'version' => '',
            'categorie_vehicule' => '', // Sera 'Engin', 'Léger', etc.
            'carburant' => '',          // Sera 'Essence', 'Diesel', etc.
            'couleur' => '',
            'poids_vide' => null,
            'ptac' => null,
            'num_moteur' => '',
            'cylindree' => null,
            'puissance_din' => null,
            'capacite_reservoir' => null,
            'capacite_charge' => null,
            'nombre_places' => null,
            'kilometrage_initial' => 0,
            'kilometrage_actuel' => 0,
            'heures_moteur' => null,
            'prix_achat' => null,
            'fournisseur_nom' => '',
            'fournisseur_ice' => '',
            'bon_commande_ref' => '',
            'article_budgetaire' => '',
            'duree_amortissement' => null,
            'mode_amortissement' => '',
            'valeur_nette_comptable' => null,
            'statut_actuel' => 'En service',
        ];

        $this->image = null;
        $this->editingId = null;
        $this->currentImagePath = null;
        $this->resetValidation(); // Important pour effacer les erreurs rouges précédentes
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEdit(int $vehicleId): void
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $this->editingId = $vehicle->id;
        $this->currentImagePath = $vehicle->image_path;
        
        // Remplissage du formulaire avec les données existantes
        $this->form = [
            'immatriculation' => $vehicle->immatriculation,
            'vin' => $vehicle->vin,
            'type_carte_grise' => $vehicle->type_carte_grise,
            'proprietaire_legal' => $vehicle->proprietaire_legal,
            'service_affecte' => $vehicle->service_affecte,
            'responsable_service' => $vehicle->responsable_service,
            'lieu_stationnement' => $vehicle->lieu_stationnement,
            'date_mise_circulation' => optional($vehicle->date_mise_circulation)->format('Y-m-d'),
            'date_acquisition' => optional($vehicle->date_acquisition)->format('Y-m-d'),
            'mode_acquisition' => $vehicle->mode_acquisition,
            'puissance_fiscale' => $vehicle->puissance_fiscale,
            'categorie_fiscale' => $vehicle->categorie_fiscale,
            'marque' => $vehicle->marque,
            'modele' => $vehicle->modele,
            'version' => $vehicle->version,
            'categorie_vehicule' => $vehicle->categorie_vehicule,
            'carburant' => $vehicle->carburant,
            'couleur' => $vehicle->couleur,
            'poids_vide' => $vehicle->poids_vide,
            'ptac' => $vehicle->ptac,
            'num_moteur' => $vehicle->num_moteur,
            'cylindree' => $vehicle->cylindree,
            'puissance_din' => $vehicle->puissance_din,
            'capacite_reservoir' => $vehicle->capacite_reservoir,
            'capacite_charge' => $vehicle->capacite_charge,
            'nombre_places' => $vehicle->nombre_places,
            'kilometrage_initial' => $vehicle->kilometrage_initial,
            'kilometrage_actuel' => $vehicle->kilometrage_actuel,
            'heures_moteur' => $vehicle->heures_moteur,
            'prix_achat' => $vehicle->prix_achat,
            'fournisseur_nom' => $vehicle->fournisseur_nom,
            'fournisseur_ice' => $vehicle->fournisseur_ice,
            'bon_commande_ref' => $vehicle->bon_commande_ref,
            'article_budgetaire' => $vehicle->article_budgetaire,
            'duree_amortissement' => $vehicle->duree_amortissement,
            'mode_amortissement' => $vehicle->mode_amortissement,
            'valeur_nette_comptable' => $vehicle->valeur_nette_comptable,
            'statut_actuel' => $vehicle->statut_actuel,
        ];

        $this->image = null;
        $this->showFormModal = true;
    }

    public function openDetails(int $vehicleId): void
    {
        $this->detailVehicle = Vehicle::findOrFail($vehicleId);
        $this->showDetailsModal = true;
    }

    public function save(): void
    {
        $this->normalizeForm();
        
        // Validation des données
        $validated = $this->validate($this->validationRules());
        $payload = $validated['form'];

        // Gestion de l'upload d'image
        if ($this->image) {
            if ($this->currentImagePath) {
                Storage::disk('public')->delete($this->currentImagePath);
            }
            $payload['image_path'] = $this->image->store('vehicles', 'public');
        } elseif ($this->currentImagePath) {
            $payload['image_path'] = $this->currentImagePath;
        }

        // Création ou Mise à jour
        if ($this->editingId) {
            Vehicle::findOrFail($this->editingId)->update($payload);
            session()->flash('status', 'Véhicule mis à jour avec succès.');
        } else {
            Vehicle::create($payload);
            session()->flash('status', 'Véhicule créé avec succès.');
        }

        // Nettoyage et fermeture
        $this->resetForm();
        $this->showFormModal = false;
        $this->showDetailsModal = false;
        $this->resetPage(); // Revenir à la page 1 pour voir le nouvel élément
    }

    public function deleteVehicle(int $vehicleId): void
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        if ($vehicle->image_path) {
            Storage::disk('public')->delete($vehicle->image_path);
        }

        $vehicle->delete();

        $this->resetPage();
        session()->flash('status', 'Véhicule supprimé.');
    }

    private function validationRules(): array
    {
        $rules = [];

        // On suppose que VehicleRequest::baseRules renvoie un tableau ['immatriculation' => 'required', ...]
        foreach (VehicleRequest::baseRules($this->editingId) as $field => $rule) {
            if ($field === 'image') {
                $rules['image'] = $rule;
                continue;
            }
            // On préfixe les champs par 'form.' car ils sont dans le tableau public $form
            $rules['form.' . $field] = $rule;
        }

        return $rules;
    }

    private function normalizeForm(): void
    {
        // Convertit les chaînes vides en null pour éviter les erreurs SQL sur les champs nullables
        foreach ($this->form as $key => $value) {
            if ($value === '') {
                $this->form[$key] = null;
            }
        }
    }
}