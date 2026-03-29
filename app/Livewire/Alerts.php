<?php

namespace App\Livewire;

use App\Models\Alert;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Alerts extends Component
{
    use WithPagination;

    #[\Livewire\Attributes\Layout('layouts.app')]

    public array $filters = [
        'type' => '',
        'priorite' => '',
        'statut' => '',
        'entity_type' => '',
    ];

    public ?Alert $detailAlert = null;
    public bool $showDetailsModal = false;
    public bool $showTraitementModal = false;
    public string $notesTraitement = '';
    public ?int $alertToTreat = null;
    public string $viewMode = 'pending'; // pending | treated | all
    public string $verificationError = '';

    protected $paginationTheme = 'tailwind';

    public function render(): View
    {
        $query = Alert::query()
            ->with(['alertable', 'treatedBy'])
            ->when($this->viewMode === 'pending', fn($q) => $q->nonArchive())
            ->when($this->viewMode === 'treated', fn($q) => $q->where('statut', 'traitee'))
            ->when($this->viewMode === 'all', fn($q) => $q->whereIn('statut', ['active', 'vue', 'traitee']))
            ->when($this->filters['type'], fn($q, $v) => $q->where('type', $v))
            ->when($this->filters['priorite'], fn($q, $v) => $q->where('priorite', $v))
            ->when($this->filters['statut'], fn($q, $v) => $q->where('statut', $v))
            ->when($this->filters['entity_type'], function($q, $v) {
                if ($v === 'vehicle') {
                    return $q->where('alertable_type', Vehicle::class);
                } elseif ($v === 'driver') {
                    return $q->where('alertable_type', Driver::class);
                }
            })
            ->orderByRaw("FIELD(priorite, 'critique', 'haute', 'moyenne', 'basse')")
            ->orderBy('created_at', 'desc');

        $allAlerts = $query->get();

        $grouped = [
            'critique' => $allAlerts->where('priorite', 'critique')->values(),
            'haute'    => $allAlerts->where('priorite', 'haute')->values(),
            'moyenne'  => $allAlerts->where('priorite', 'moyenne')->values(),
            'basse'    => $allAlerts->where('priorite', 'basse')->values(),
        ];

        $stats = [
            'total'    => Alert::nonArchive()->whereIn('statut', ['active', 'vue'])->count(),
            'critiques' => Alert::where('priorite', 'critique')->whereIn('statut', ['active', 'vue'])->count(),
            'hautes'   => Alert::where('priorite', 'haute')->whereIn('statut', ['active', 'vue'])->count(),
            'moyennes' => Alert::where('priorite', 'moyenne')->whereIn('statut', ['active', 'vue'])->count(),
            'basses'   => Alert::where('priorite', 'basse')->whereIn('statut', ['active', 'vue'])->count(),
        ];

        return view('livewire.alerts', [
            'grouped'  => $grouped,
            'types'    => Alert::TYPES,
            'priorites' => Alert::PRIORITES,
            'statuts'  => Alert::STATUTS,
            'stats'    => $stats,
        ]);
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = in_array($mode, ['pending', 'treated', 'all']) ? $mode : 'pending';
    }

    public function updatingFilters(): void
    {
        $this->resetPage();
    }

    public function refreshAlerts(): void
    {
        $service = new AlertService();
        $result = $service->generateAllAlerts();

        $total = $result['drivers'] + $result['vehicles'] + $result['interventions'];

        if ($total > 0) {
            session()->flash('status', "{$total} nouvelle(s) alerte(s) générée(s).");
        } else {
            session()->flash('status', "Alertes à jour. Aucune nouvelle alerte.");
        }
    }

    public function openDetails(int $id): void
    {
        $this->detailAlert = Alert::with(['alertable', 'treatedBy', 'viewedBy'])->findOrFail($id);
        $this->showDetailsModal = true;

        if ($this->detailAlert->statut === Alert::STATUT_ACTIVE) {
            $service = new AlertService();
            $service->markAsViewed($this->detailAlert, auth()->id());
            $this->detailAlert->refresh()->load(['alertable', 'treatedBy', 'viewedBy']);
        }
    }

    public function closeDetails(): void
    {
        $this->showDetailsModal = false;
        $this->detailAlert = null;
    }

    public function openTraitement(int $id): void
    {
        $alert = Alert::with('alertable')->findOrFail($id);
        $service = new AlertService();
        $result = $service->verifyTreatment($alert);

        if (!$result['ok']) {
            $this->verificationError = $result['message'];
            return;
        }

        $this->verificationError = '';
        $this->alertToTreat = $id;
        $this->notesTraitement = '';
        $this->showTraitementModal = true;
    }

    public function closeTraitement(): void
    {
        $this->showTraitementModal = false;
        $this->alertToTreat = null;
        $this->notesTraitement = '';
        $this->verificationError = '';
    }

    public function markAsTreated(): void
    {
        if (!$this->alertToTreat) return;

        $alert = Alert::findOrFail($this->alertToTreat);
        $service = new AlertService();
        $service->markAsTreated($alert, auth()->id(), $this->notesTraitement ?: null);

        session()->flash('status', 'Alerte marquée comme traitée.');
        $this->closeTraitement();
    }

    public function markAsViewedById(int $id): void
    {
        $alert = Alert::findOrFail($id);
        $service = new AlertService();
        $service->markAsViewed($alert, auth()->id());

        session()->flash('status', 'Alerte marquée comme vue.');
    }

    public function ignoreAlert(int $id): void
    {
        $alert = Alert::findOrFail($id);
        $service = new AlertService();
        $service->ignore($alert, auth()->id());

        session()->flash('status', 'Alerte ignorée.');
    }

    public function archiveAlert(int $id): void
    {
        $alert = Alert::findOrFail($id);
        $alert->update(['statut' => Alert::STATUT_ARCHIVEE]);
        session()->flash('status', 'Alerte archivée.');
    }

    public function getEntityName(?object $alertable): string
    {
        if (!$alertable) return 'N/A';

        if ($alertable instanceof Vehicle) {
            return $alertable->immatriculation;
        }

        if ($alertable instanceof Driver) {
            return "{$alertable->nom} {$alertable->prenom}";
        }

        return 'N/A';
    }

    public function getEntityMarque(?object $alertable): string
    {
        if ($alertable instanceof Vehicle) {
            return $alertable->marque ?? '';
        }
        return '';
    }

    public function getEntityType(?string $type): string
    {
        if ($type === Vehicle::class) return 'Véhicule';
        if ($type === Driver::class) return 'Conducteur';
        return 'N/A';
    }
}
