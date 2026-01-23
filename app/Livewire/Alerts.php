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

    protected $paginationTheme = 'tailwind';

    public function render(): View
    {
        $alerts = Alert::query()
            ->with(['alertable', 'treatedBy'])
            ->nonArchive()
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
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => Alert::count(),
            'actives' => Alert::active()->count(),
            'critiques' => Alert::critique()->active()->count(),
            'hautes' => Alert::where('priorite', Alert::PRIORITE_HAUTE)->active()->count(),
        ];

        return view('livewire.alerts', [
            'alerts' => $alerts,
            'types' => Alert::TYPES,
            'priorites' => Alert::PRIORITES,
            'statuts' => Alert::STATUTS,
            'stats' => $stats,
        ]);
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
        $this->detailAlert = Alert::with(['alertable', 'treatedBy'])->findOrFail($id);
        $this->showDetailsModal = true;

        if ($this->detailAlert->statut === Alert::STATUT_ACTIVE) {
            $service = new AlertService();
            $service->markAsViewed($this->detailAlert);
        }
    }

    public function closeDetails(): void
    {
        $this->showDetailsModal = false;
        $this->detailAlert = null;
    }

    public function openTraitement(int $id): void
    {
        $this->alertToTreat = $id;
        $this->notesTraitement = '';
        $this->showTraitementModal = true;
    }

    public function closeTraitement(): void
    {
        $this->showTraitementModal = false;
        $this->alertToTreat = null;
        $this->notesTraitement = '';
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

    public function ignoreAlert(int $id): void
    {
        $alert = Alert::findOrFail($id);
        $service = new AlertService();
        $service->ignore($alert, auth()->id());

        session()->flash('status', 'Alerte ignorée.');
    }

    public function markAllAsViewed(): void
    {
        Alert::where('statut', Alert::STATUT_ACTIVE)->update(['statut' => Alert::STATUT_VUE]);
        session()->flash('status', 'Toutes les alertes marquées comme vues.');
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

    public function getEntityType(?string $type): string
    {
        if ($type === Vehicle::class) return 'Véhicule';
        if ($type === Driver::class) return 'Conducteur';
        return 'N/A';
    }
}