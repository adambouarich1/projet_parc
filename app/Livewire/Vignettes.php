<?php
 
namespace App\Livewire;
 
use App\Models\Vignette;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
 
class Vignettes extends Component
{
    use WithPagination;
 
    #[\Livewire\Attributes\Layout('layouts.app')]
 
    public array $filters = [
        'search' => '',
        'annee' => '',
        'section' => '',
    ];
 
    public array $form = [];
    public ?int $editingId = null;
    public ?Vignette $detailVignette = null;
 
    public bool $showFormModal = false;
    public bool $showDetailsModal = false;
 
    protected $paginationTheme = 'tailwind';
 
    public function mount(): void
    {
        $this->filters['annee'] = date('Y');
        $this->resetForm();
    }
 
    public function render(): View
    {
        $allVehicles = Vehicle::orderBy('immatriculation')->get();
        $anneeFiltre = $this->filters['annee'] ?: date('Y');
 
        // IDs des véhicules qui ont une vignette active non expirée pour l'année filtrée
        $vehiculesAvecVignetteIds = Vignette::active()
            ->where('date_expiration', '>=', now()->toDateString())
            ->where('annee', $anneeFiltre)
            ->pluck('vehicle_id')
            ->unique()
            ->toArray();
 
        $vehiculesAvecVignette = collect();
        $vehiculesSansVignette = collect();
 
        foreach ($allVehicles as $vehicle) {
            // Filtre recherche
            if (!empty($this->filters['search'])) {
                $search = strtolower($this->filters['search']);
                $match = str_contains(strtolower($vehicle->immatriculation), $search)
                    || str_contains(strtolower($vehicle->marque ?? ''), $search)
                    || str_contains(strtolower($vehicle->modele ?? ''), $search);
 
                $vignetteActive = Vignette::where('vehicle_id', $vehicle->id)
                    ->active()
                    ->where('date_expiration', '>=', now()->toDateString())
                    ->where('annee', $anneeFiltre)
                    ->first();
 
                if ($vignetteActive) {
                    $match = $match || str_contains(strtolower($vignetteActive->reference_paiement ?? ''), $search);
                }
 
                if (!$match) continue;
            }
 
            if (in_array($vehicle->id, $vehiculesAvecVignetteIds)) {
                $vignette = Vignette::where('vehicle_id', $vehicle->id)
                    ->active()
                    ->where('date_expiration', '>=', now()->toDateString())
                    ->where('annee', $anneeFiltre)
                    ->orderByDesc('date_expiration')
                    ->first();
 
                $vehicle->vignette_active = $vignette;
                $vehiculesAvecVignette->push($vehicle);
            } else {
                $derniereVignette = Vignette::where('vehicle_id', $vehicle->id)
                    ->nonArchivee()
                    ->orderByDesc('date_expiration')
                    ->first();
 
                $vehicle->derniere_vignette = $derniereVignette;
                $vehiculesSansVignette->push($vehicle);
            }
        }
 
        // Trier : ceux qui expirent bientôt en premier
        $vehiculesAvecVignette = $vehiculesAvecVignette->sortBy(function ($v) {
            return $v->vignette_active->jours_restants;
        });
 
        // Stats
        $stats = [
            'total_vehicules' => $allVehicles->count(),
            'avec_vignette' => count($vehiculesAvecVignetteIds),
            'sans_vignette' => $allVehicles->count() - count($vehiculesAvecVignetteIds),
            'expire_bientot' => Vignette::active()->expireBientot()->where('annee', $anneeFiltre)->count(),
            'total_montant' => Vignette::active()
                ->where('date_expiration', '>=', now()->toDateString())
                ->where('annee', $anneeFiltre)
                ->sum('montant'),
        ];
 
        // Années disponibles
        $annees = Vignette::selectRaw('DISTINCT annee')->orderBy('annee', 'desc')->pluck('annee');
        if ($annees->isEmpty() || !$annees->contains(date('Y'))) {
            $annees = $annees->push((int) date('Y'))->sortDesc()->values();
        }
 
        // Véhicules pour le formulaire : en création, exclure ceux qui ont déjà une vignette active pour l'année
        $vehiclesForForm = $this->editingId
            ? $allVehicles
            : $allVehicles->filter(fn($v) => !in_array($v->id, $vehiculesAvecVignetteIds));
 
        return view('livewire.vignettes', [
            'vehiculesAvecVignette' => $vehiculesAvecVignette,
            'vehiculesSansVignette' => $vehiculesSansVignette,
            'vehicles' => $allVehicles,
            'vehiclesForForm' => $vehiclesForForm,
            'statuts' => Vignette::STATUTS,
            'stats' => $stats,
            'annees' => $annees,
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
            'annee' => date('Y'),
            'date_debut' => date('Y') . '-01-01',
            'date_expiration' => date('Y') . '-12-31',
            'montant' => '',
            'reference_paiement' => '',
            'date_paiement' => date('Y-m-d'),
            'statut' => 'active',
            'observations' => '',
        ];
        $this->editingId = null;
    }
 
    public function updatedFormAnnee($value): void
    {
        if ($value) {
            $this->form['date_debut'] = $value . '-01-01';
            $this->form['date_expiration'] = $value . '-12-31';
        }
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
        $vignette = Vignette::findOrFail($id);
        $this->editingId = $id;
        $this->form = [
            'vehicle_id' => $vignette->vehicle_id,
            'annee' => $vignette->annee,
            'date_debut' => $vignette->date_debut->format('Y-m-d'),
            'date_expiration' => $vignette->date_expiration->format('Y-m-d'),
            'montant' => $vignette->montant,
            'reference_paiement' => $vignette->reference_paiement ?? '',
            'date_paiement' => $vignette->date_paiement?->format('Y-m-d') ?? '',
            'statut' => $vignette->statut,
            'observations' => $vignette->observations ?? '',
        ];
        $this->showFormModal = true;
    }
 
    public function openDetails(int $id): void
    {
        $this->detailVignette = Vignette::with(['vehicle', 'user'])->findOrFail($id);
        $this->showDetailsModal = true;
    }
 
    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDetailsModal = false;
        $this->detailVignette = null;
        $this->resetForm();
    }
 
    public function save(): void
    {
        $this->validate([
            'form.vehicle_id' => 'required|exists:vehicles,id',
            'form.annee' => 'required|integer|min:2000|max:2100',
            'form.date_debut' => 'required|date',
            'form.date_expiration' => 'required|date|after:form.date_debut',
            'form.montant' => 'required|numeric|min:0',
        ], [
            'form.vehicle_id.required' => 'Le véhicule est obligatoire.',
            'form.annee.required' => 'L\'année est obligatoire.',
            'form.date_debut.required' => 'La date de début est obligatoire.',
            'form.date_expiration.required' => 'La date d\'expiration est obligatoire.',
            'form.montant.required' => 'Le montant est obligatoire.',
        ]);
 
        $data = [
            'vehicle_id' => $this->form['vehicle_id'],
            'user_id' => auth()->id(),
            'annee' => (int) $this->form['annee'],
            'date_debut' => $this->form['date_debut'],
            'date_expiration' => $this->form['date_expiration'],
            'montant' => (float) $this->form['montant'],
            'reference_paiement' => $this->form['reference_paiement'] ?: null,
            'date_paiement' => $this->form['date_paiement'] ?: null,
            'statut' => $this->form['statut'],
            'observations' => $this->form['observations'] ?: null,
        ];
 
        if ($this->editingId) {
            Vignette::findOrFail($this->editingId)->update($data);
            session()->flash('status', 'Vignette mise à jour.');
        } else {
            Vignette::create($data);
            session()->flash('status', 'Vignette enregistrée.');
        }
 
        $this->closeModals();
    }
 
    public function archive(int $id): void
    {
        $vignette = Vignette::findOrFail($id);
        $vignette->update(['statut' => Vignette::STATUT_ARCHIVEE]);
        session()->flash('status', 'Vignette archivée.');
    }
 
    public function delete(int $id): void
    {
        Vignette::findOrFail($id)->delete();
        session()->flash('status', 'Vignette supprimée.');
    }
}