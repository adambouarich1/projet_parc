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
        'vehicle_id' => '',
        'statut' => '',
        'search' => '',
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
        $insurances = Insurance::query()
            ->with(['vehicle', 'user'])
            ->nonArchivee()
            ->when($this->filters['vehicle_id'], fn($q, $v) => $q->where('vehicle_id', $v))
            ->when($this->filters['statut'], fn($q, $v) => $q->where('statut', $v))
            ->when($this->filters['search'], fn($q, $v) => $q->where(function($query) use ($v) {
                $query->where('assureur', 'like', "%{$v}%")
                    ->orWhere('numero_police', 'like', "%{$v}%")
                    ->orWhereHas('vehicle', fn($q2) => $q2->where('immatriculation', 'like', "%{$v}%"));
            }))
            ->latest('date_expiration')
            ->paginate(15);

        // Stats
        $stats = [
            'total' => Insurance::nonArchivee()->count(),
            'actives' => Insurance::active()->count(),
            'expirees' => Insurance::nonArchivee()->expiree()->count(),
            'expire_bientot' => Insurance::active()->expireBientot()->count(),
        ];

        return view('livewire.insurances', [
            'insurances' => $insurances,
            'vehicles' => Vehicle::orderBy('immatriculation')->get(),
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

    public function openCreate(): void
    {
        $this->resetForm();
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

        // Calcul automatique de la date d'expiration
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

    public function markAsExpired(int $id): void
    {
        $insurance = Insurance::findOrFail($id);
        $insurance->update(['statut' => Insurance::STATUT_EXPIREE]);
        session()->flash('status', 'Assurance marquée comme expirée.');
    }
}