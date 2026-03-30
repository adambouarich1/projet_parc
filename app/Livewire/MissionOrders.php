<?php

namespace App\Livewire;

use App\Models\MissionOrder;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class MissionOrders extends Component
{
    use WithPagination;
    
    #[\Livewire\Attributes\Layout('layouts.app')]

    public array $filters = [
        'statut' => '',
        'search' => '',
    ];

    public array $form = [];
    public ?int $editingId = null;
    public ?MissionOrder $detailMission = null;

    public bool $showFormModal = false;
    public bool $showDetailsModal = false;
    public bool $showRejectModal = false;

    public string $motifRejet = '';

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        $missions = MissionOrder::query()
            ->nonArchive()
            ->with(['user', 'vehicle', 'driver', 'validator'])
            ->when($this->filters['statut'] === '', fn($q) => $q->where('statut', '!=', MissionOrder::STATUT_CLOTURE))
            ->when($this->filters['statut'] !== '' && $this->filters['statut'] !== 'tout_voir', fn($q) => $q->where('statut', $this->filters['statut']))
            ->when($this->filters['search'], fn($q, $v) => $q->where(function($query) use ($v) {
                $query->where('reference', 'like', "%{$v}%")
                    ->orWhere('objet', 'like', "%{$v}%")
                    ->orWhere('destination', 'like', "%{$v}%")
                    ->orWhereHas('driver', fn($q) => $q
                        ->where('nom', 'like', "%{$v}%")
                        ->orWhere('prenom', 'like', "%{$v}%")
                    )
                    ->orWhereHas('vehicle', fn($q) => $q
                        ->where('marque', 'like', "%{$v}%")
                    );
            }))
            ->latest()
            ->paginate(10);

        return view('livewire.mission-orders', [
            'missions' => $missions,
            'vehicles' => Vehicle::where('statut_actuel', 'En service')->get(),
            'drivers' => Driver::where('statut_actuel', 'Disponible')->get(),
            'statuts' => MissionOrder::STATUTS,
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
            'driver_id' => '',
            'objet' => '',
            'description' => '',
            'destination' => '',
            'lieu_depart' => 'Siège',
            'date_depart' => '',
            'date_retour_prevue' => '',
        ];
        $this->editingId = null;
        $this->motifRejet = '';
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $mission = MissionOrder::findOrFail($id);
        
        // On ne peut modifier que les brouillons
        if ($mission->statut !== MissionOrder::STATUT_BROUILLON) {
            session()->flash('error', 'Seuls les brouillons peuvent être modifiés.');
            return;
        }

        $this->editingId = $mission->id;
        $this->form = [
            'vehicle_id' => $mission->vehicle_id,
            'driver_id' => $mission->driver_id,
            'objet' => $mission->objet,
            'description' => $mission->description,
            'destination' => $mission->destination,
            'lieu_depart' => $mission->lieu_depart,
            'date_depart' => $mission->date_depart->format('Y-m-d\TH:i'),
            'date_retour_prevue' => $mission->date_retour_prevue->format('Y-m-d\TH:i'),
        ];
        $this->showFormModal = true;
    }

    public function openDetails(int $id): void
    {
        $this->detailMission = MissionOrder::with(['user', 'vehicle', 'driver', 'validator'])->findOrFail($id);
        $this->showDetailsModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'form.vehicle_id' => 'required|exists:vehicles,id',
            'form.driver_id' => 'required|exists:drivers,id',
            'form.objet' => 'required|string|max:255',
            'form.description' => 'nullable|string',
            'form.destination' => 'required|string|max:255',
            'form.lieu_depart' => 'required|string|max:255',
            'form.date_depart' => 'required|date',
            'form.date_retour_prevue' => 'required|date|after:form.date_depart',
        ]);

        $data = $validated['form'];
        $data['user_id'] = auth()->id();

        if ($this->editingId) {
            $mission = MissionOrder::findOrFail($this->editingId);
            $mission->update($data);
            session()->flash('status', 'Ordre de mission mis à jour.');
        } else {
            $data['reference'] = MissionOrder::generateReference();
            $data['statut'] = MissionOrder::STATUT_BROUILLON;
            MissionOrder::create($data);
            session()->flash('status', 'Ordre de mission créé.');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    // Soumettre pour validation
    public function submit(int $id): void
    {
        $mission = MissionOrder::findOrFail($id);
        
        if ($mission->statut !== MissionOrder::STATUT_BROUILLON) {
            session()->flash('error', 'Cette mission ne peut pas être soumise.');
            return;
        }

        $mission->update(['statut' => MissionOrder::STATUT_EN_ATTENTE]);
        session()->flash('status', 'Mission soumise pour validation.');
    }

    // Valider (pour les valideurs)
    public function validate_mission(int $id): void
    {
        if (!auth()->user()->canValidate()) {
            session()->flash('error', 'Vous n\'avez pas les droits pour valider.');
            return;
        }

        $mission = MissionOrder::findOrFail($id);
        
        if (!$mission->canBeValidated()) {
            session()->flash('error', 'Cette mission ne peut pas être validée.');
            return;
        }

        $mission->update([
            'statut' => MissionOrder::STATUT_VALIDE,
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        $mission->vehicle?->update(['statut_actuel' => 'Assigné pour mission']);

        session()->flash('status', 'Mission validée avec succès.');
    }

    // Ouvrir modal de rejet
    public function openReject(int $id): void
    {
        $this->detailMission = MissionOrder::findOrFail($id);
        $this->motifRejet = '';
        $this->showRejectModal = true;
    }

    // Rejeter (pour les valideurs)
    public function reject(): void
    {
        if (!auth()->user()->canValidate()) {
            session()->flash('error', 'Vous n\'avez pas les droits pour rejeter.');
            return;
        }

        $this->validate(['motifRejet' => 'required|string|min:10']);

        $this->detailMission->update([
            'statut' => MissionOrder::STATUT_REJETE,
            'validated_by' => auth()->id(),
            'validated_at' => now(),
            'motif_rejet' => $this->motifRejet,
        ]);

        $this->showRejectModal = false;
        session()->flash('status', 'Mission rejetée.');
    }

    // Démarrer la mission manuellement (départ anticipé avant la date prévue)
    public function start(int $id): void
    {
        $mission = MissionOrder::findOrFail($id);

        if (!$mission->canBeStarted()) {
            session()->flash('error', 'Cette mission ne peut pas être démarrée.');
            return;
        }

        $mission->update([
            'statut'     => MissionOrder::STATUT_DEPART_ANTICIPE,
            'started_at' => now(),
            'km_depart'  => $mission->vehicle->kilometrage_actuel,
        ]);

        $mission->vehicle->update(['statut_actuel' => 'En mission']);
        $mission->driver->update(['statut_actuel' => 'En mission']);

        session()->flash('status', 'Mission démarrée en départ anticipé.');
    }

    // Clôturer la mission
    public function closeModal(int $id): void
    {
        $this->detailMission = MissionOrder::with('vehicle')->findOrFail($id);
        $this->form['km_retour'] = $this->detailMission->vehicle->kilometrage_actuel;
        $this->form['observations'] = '';
        $this->showDetailsModal = true;
    }

    public function close(int $id): void
    {
        $mission = MissionOrder::findOrFail($id);
        
        if (!$mission->canBeClosed()) {
            session()->flash('error', 'Cette mission ne peut pas être clôturée.');
            return;
        }

        $this->validate([
            'form.km_retour' => 'required|integer|min:' . $mission->km_depart,
        ]);

        $archiveNow = $mission->created_at->startOfMonth()->lt(now()->startOfMonth());

        $mission->update([
            'statut'               => $archiveNow ? MissionOrder::STATUT_ARCHIVE : MissionOrder::STATUT_CLOTURE,
            'closed_at'            => now(),
            'date_retour_effective' => now(),
            'km_retour'            => $this->form['km_retour'],
            'observations'         => $this->form['observations'] ?? null,
            'archived_at'          => $archiveNow ? now() : null,
        ]);

        // Mettre à jour le km du véhicule et le remettre en service
        $mission->vehicle->update([
            'kilometrage_actuel' => $this->form['km_retour'],
            'statut_actuel' => 'En service',
        ]);

        // Remettre le conducteur disponible
        $mission->driver->update(['statut_actuel' => 'Disponible']);

        $this->showDetailsModal = false;
        session()->flash('status', 'Mission clôturée avec succès.');
    }

    // Annuler
    public function cancel(int $id): void
    {
        $mission = MissionOrder::with('vehicle')->findOrFail($id);

        if (in_array($mission->statut, [
            MissionOrder::STATUT_EN_COURS,
            MissionOrder::STATUT_DEPART_ANTICIPE,
            MissionOrder::STATUT_TERMINE_ATTENTE,
            MissionOrder::STATUT_CLOTURE,
        ])) {
            session()->flash('error', 'Cette mission ne peut pas être annulée.');
            return;
        }

        // Si la mission était validée, libérer le véhicule
        if ($mission->statut === MissionOrder::STATUT_VALIDE) {
            $mission->vehicle?->update(['statut_actuel' => 'En service']);
        }

        $mission->update(['statut' => MissionOrder::STATUT_ANNULE]);
        session()->flash('status', 'Mission annulée.');
    }

    // Supprimer (brouillons uniquement)
    public function delete(int $id): void
    {
        $mission = MissionOrder::findOrFail($id);
        
        if ($mission->statut !== MissionOrder::STATUT_BROUILLON) {
            session()->flash('error', 'Seuls les brouillons peuvent être supprimés.');
            return;
        }

        $mission->delete();
        session()->flash('status', 'Mission supprimée.');
    }
    public function archive(int $id): void
{
    $mission = MissionOrder::findOrFail($id);
    $mission->update(['statut' => MissionOrder::STATUT_ARCHIVE]);
    session()->flash('status', 'Ordre de mission archivé.');
}
}