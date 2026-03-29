<?php
 
namespace App\Livewire;
 
use App\Models\Insurance;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
 
class Insurances extends Component
{
    use WithPagination;
 
    #[\Livewire\Attributes\Layout('layouts.app')]
 
    public array $filters = [
        'search' => '',
        'section' => '', // 'assures', 'non_assures', '' = tous
    ];
 
    public array $form = [];
    public ?int $editingId = null;
    public ?Insurance $detailInsurance = null;
 
    public bool $showFormModal = false;
    public bool $showDetailsModal = false;
 
    protected $paginationTheme = 'tailwind';
 
    public function mount(): void
    {
        $this->resetForm();
    }
 
    public function render(): View
    {
        // Récupérer tous les véhicules
        $allVehicles = Vehicle::orderBy('immatriculation')->get();
 
        // IDs des véhicules qui ont au moins une assurance active (non expirée)
        $vehiculesAssuresIds = Insurance::active()
            ->where('date_expiration', '>=', now()->toDateString())
            ->pluck('vehicle_id')
            ->unique()
            ->toArray();
 
        // Véhicules assurés avec leur assurance active
        $vehiculesAssures = collect();
        $vehiculesNonAssures = collect();
 
        foreach ($allVehicles as $vehicle) {
            // Appliquer filtre recherche
            if (!empty($this->filters['search'])) {
                $search = strtolower($this->filters['search']);
                $match = str_contains(strtolower($vehicle->immatriculation), $search)
                    || str_contains(strtolower($vehicle->marque ?? ''), $search)
                    || str_contains(strtolower($vehicle->modele ?? ''), $search);
 
                // Chercher aussi dans l'assureur
                $assuranceActive = Insurance::where('vehicle_id', $vehicle->id)
                    ->active()
                    ->where('date_expiration', '>=', now()->toDateString())
                    ->first();
 
                if ($assuranceActive) {
                    $match = $match || str_contains(strtolower($assuranceActive->assureur), $search)
                        || str_contains(strtolower($assuranceActive->numero_police ?? ''), $search);
                }
 
                if (!$match) continue;
            }
 
            if (in_array($vehicle->id, $vehiculesAssuresIds)) {
                // Véhicule assuré : récupérer l'assurance active la plus récente
                $assurance = Insurance::where('vehicle_id', $vehicle->id)
                    ->active()
                    ->where('date_expiration', '>=', now()->toDateString())
                    ->orderByDesc('date_expiration')
                    ->first();
 
                $vehicle->assurance_active = $assurance;
                $vehiculesAssures->push($vehicle);
            } else {
                // Véhicule non assuré : récupérer la dernière assurance (expirée) si elle existe
                $derniereAssurance = Insurance::where('vehicle_id', $vehicle->id)
                    ->nonArchivee()
                    ->orderByDesc('date_expiration')
                    ->first();
 
                $vehicle->derniere_assurance = $derniereAssurance;
                $vehiculesNonAssures->push($vehicle);
            }
        }
 
        // Trier les assurés : ceux qui expirent bientôt en premier
        $vehiculesAssures = $vehiculesAssures->sortBy(function ($v) {
            return $v->assurance_active->jours_restants;
        });
 
        // Stats
        $stats = [
            'total_vehicules' => $allVehicles->count(),
            'assures' => count($vehiculesAssuresIds),
            'non_assures' => $allVehicles->count() - count($vehiculesAssuresIds),
            'expire_bientot' => Insurance::active()->expireBientot()->count(),
            'total_montant' => Insurance::active()
                ->where('date_expiration', '>=', now()->toDateString())
                ->sum('montant'),
        ];
 
        // Véhicules disponibles pour le formulaire :
        // En création → uniquement les non assurés
        // En édition → tous (car on modifie une assurance existante)
        $vehiclesForForm = $this->editingId
            ? $allVehicles
            : $allVehicles->filter(fn($v) => !in_array($v->id, $vehiculesAssuresIds));
 
        return view('livewire.insurances', [
            'vehiculesAssures' => $vehiculesAssures,
            'vehiculesNonAssures' => $vehiculesNonAssures,
            'vehicles' => $allVehicles,
            'vehiclesForForm' => $vehiclesForForm,
            'statuts' => Insurance::STATUTS,
            'durees' => Insurance::DUREES,
            'stats' => $stats,
        ]);
    }
 
    public function updatingFilters(): void
    {
        $this->resetPage();
    }
 
    public function resetForm(): void
    {
        $this->form = [
            'vehicle_id' => '',
            'assureur' => '',
            'numero_police' => '',
            'date_debut' => date('Y-m-d'),
            'duree_mois' => 12,
            'montant' => '',
            'statut' => 'active',
            'observations' => '',
        ];
        $this->editingId = null;
    }
 
    public function openCreate(?int $vehicleId = null): void
    {
        $this->resetForm();
        if ($vehicleId) {
            $this->form['vehicle_id'] = $vehicleId;
        }
        $this->showFormModal = true;
    }
 
    public function openEdit(int $id): void
    {
        $insurance = Insurance::findOrFail($id);
        $this->editingId = $id;
        $this->form = [
            'vehicle_id' => $insurance->vehicle_id,
            'assureur' => $insurance->assureur,
            'numero_police' => $insurance->numero_police ?? '',
            'date_debut' => $insurance->date_debut->format('Y-m-d'),
            'duree_mois' => $insurance->duree_mois,
            'montant' => $insurance->montant,
            'statut' => $insurance->statut,
            'observations' => $insurance->observations ?? '',
        ];
        $this->showFormModal = true;
    }
 
    public function openDetails(int $id): void
    {
        $this->detailInsurance = Insurance::with(['vehicle', 'user'])->findOrFail($id);
        $this->showDetailsModal = true;
    }
 
    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDetailsModal = false;
        $this->detailInsurance = null;
        $this->resetForm();
    }
 
    public function save(): void
    {
        $this->validate([
            'form.vehicle_id' => 'required|exists:vehicles,id',
            'form.assureur' => 'required|string|max:255',
            'form.date_debut' => 'required|date',
            'form.duree_mois' => 'required|integer|min:1',
        ], [
            'form.vehicle_id.required' => 'Le véhicule est obligatoire.',
            'form.assureur.required' => 'L\'assureur est obligatoire.',
            'form.date_debut.required' => 'La date de début est obligatoire.',
            'form.duree_mois.required' => 'La durée est obligatoire.',
        ]);
 
        $dateExpiration = Insurance::calculateExpiration(
            $this->form['date_debut'],
            (int) $this->form['duree_mois']
        );
 
        $data = [
            'vehicle_id' => $this->form['vehicle_id'],
            'user_id' => auth()->id(),
            'assureur' => $this->form['assureur'],
            'numero_police' => $this->form['numero_police'] ?: null,
            'date_debut' => $this->form['date_debut'],
            'duree_mois' => (int) $this->form['duree_mois'],
            'date_expiration' => $dateExpiration,
            'montant' => $this->form['montant'] !== '' ? (float) $this->form['montant'] : 0,
            'statut' => $this->form['statut'],
            'observations' => $this->form['observations'] ?: null,
        ];
 
        if ($this->editingId) {
            Insurance::findOrFail($this->editingId)->update($data);
            session()->flash('status', 'Assurance mise à jour.');
        } else {
            Insurance::create($data);
            session()->flash('status', 'Assurance enregistrée.');
        }
 
        $this->closeModals();
    }
 
    public function archive(int $id): void
    {
        $insurance = Insurance::findOrFail($id);
        $insurance->update(['statut' => Insurance::STATUT_ARCHIVEE]);
        session()->flash('status', 'Assurance archivée.');
    }
 
    public function delete(int $id): void
    {
        Insurance::findOrFail($id)->delete();
        session()->flash('status', 'Assurance supprimée.');
    }
}