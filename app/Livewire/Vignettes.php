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
        'vehicle_id' => '',
        'annee' => '',
        'statut' => '',
        'search' => '',
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
        $vignettes = Vignette::query()
            ->with(['vehicle', 'user'])
            ->nonArchivee()
            ->when($this->filters['vehicle_id'], fn($q, $v) => $q->where('vehicle_id', $v))
            ->when($this->filters['annee'], fn($q, $v) => $q->where('annee', $v))
            ->when($this->filters['statut'], fn($q, $v) => $q->where('statut', $v))
            ->when($this->filters['search'], fn($q, $v) => $q->where(function($query) use ($v) {
                $query->where('reference_paiement', 'like', "%{$v}%")
                      ->orWhereHas('vehicle', fn($q2) => $q2->where('immatriculation', 'like', "%{$v}%"));
            }))
            ->latest('date_expiration')
            ->paginate(15);

        // Stats
        $stats = [
            'total' => Vignette::nonArchivee()->count(),
            'actives' => Vignette::active()->count(),
            'expirees' => Vignette::nonArchivee()->expiree()->count(),
            'expire_bientot' => Vignette::active()->expireBientot()->count(),
            'total_montant' => Vignette::nonArchivee()->where('annee', date('Y'))->sum('montant'),
        ];

        // Années disponibles pour le filtre
        $annees = Vignette::selectRaw('DISTINCT annee')->orderBy('annee', 'desc')->pluck('annee');
        if ($annees->isEmpty()) {
            $annees = collect([date('Y')]);
        }

        return view('livewire.vignettes', [
            'vignettes' => $vignettes,
            'vehicles' => Vehicle::orderBy('immatriculation')->get(),
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

    public function openCreate(): void
    {
        $this->resetForm();
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