<?php

namespace App\Livewire;

use App\Models\Alert;
use App\Models\FuelEntry;
use App\Models\Intervention;
use App\Models\MissionOrder;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Archives extends Component
{
    use WithPagination;

    #[\Livewire\Attributes\Layout('layouts.app')]

    public string $activeModule = 'missions';
    public int    $selectedMonth;
    public int    $selectedYear;
    public ?int   $detailId = null;

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->selectedMonth = (int) now()->subMonth()->month;
        $this->selectedYear  = (int) now()->subMonth()->year;
    }

    public function render(): View
    {
        $m = $this->selectedMonth;
        $y = $this->selectedYear;

        $availablePeriods = $this->getAvailablePeriods();
        $items            = $this->getItems($m, $y);
        $kpis             = $this->getKpis($m, $y);
        $detailItem       = $this->detailId ? $this->loadDetail($this->detailId) : null;

        return view('livewire.archives', [
            'items'            => $items,
            'kpis'             => $kpis,
            'availablePeriods' => $availablePeriods,
            'detailItem'       => $detailItem,
        ]);
    }

    public function setModule(string $module): void
    {
        $this->activeModule = $module;
        $this->detailId     = null;
        $this->resetPage();

        $periods = $this->getAvailablePeriods();
        if ($periods->isNotEmpty()) {
            $exists = $periods->contains(fn($p) => $p['month'] === $this->selectedMonth && $p['year'] === $this->selectedYear);
            if (!$exists) {
                $this->selectedMonth = $periods->first()['month'];
                $this->selectedYear  = $periods->first()['year'];
            }
        }
    }

    public function setPeriod(int $month, int $year): void
    {
        $this->selectedMonth = $month;
        $this->selectedYear  = $year;
        $this->detailId      = null;
        $this->resetPage();
    }

    public function openDetail(int $id): void
    {
        $this->detailId = ($this->detailId === $id) ? null : $id;
    }

    private function baseQuery()
    {
        return match ($this->activeModule) {
            'carburant'     => FuelEntry::archive(),
            'interventions' => Intervention::archive(),
            'alertes'       => Alert::archive(),
            default         => MissionOrder::archive(),
        };
    }

    private function getAvailablePeriods(): \Illuminate\Support\Collection
    {
        return $this->baseQuery()
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at) DESC, MONTH(created_at) DESC')
            ->get()
            ->map(fn($r) => ['month' => (int)$r->month, 'year' => (int)$r->year]);
    }

    private function forPeriod(int $m, int $y)
    {
        return $this->baseQuery()->whereMonth('created_at', $m)->whereYear('created_at', $y);
    }

    private function getItems(int $m, int $y)
    {
        return match ($this->activeModule) {
            'carburant' => FuelEntry::archive()
                ->whereMonth('created_at', $m)->whereYear('created_at', $y)
                ->with(['vehicle', 'driver', 'user'])
                ->latest()->paginate(15),

            'interventions' => Intervention::archive()
                ->whereMonth('created_at', $m)->whereYear('created_at', $y)
                ->with(['vehicle', 'user'])
                ->latest('date_intervention')->paginate(15),

            'alertes' => Alert::archive()
                ->whereMonth('created_at', $m)->whereYear('created_at', $y)
                ->with(['alertable', 'treatedBy'])
                ->latest()->paginate(15),

            default => MissionOrder::archive()
                ->whereMonth('created_at', $m)->whereYear('created_at', $y)
                ->with(['vehicle', 'driver', 'user', 'validator'])
                ->latest()->paginate(15),
        };
    }

    private function getKpis(int $m, int $y): array
    {
        return match ($this->activeModule) {
            'carburant' => [
                'nb_pleins'     => FuelEntry::archive()->whereMonth('created_at', $m)->whereYear('created_at', $y)->count(),
                'total_litres'  => (float) FuelEntry::archive()->whereMonth('created_at', $m)->whereYear('created_at', $y)->sum('quantite_litres'),
                'total_montant' => (float) FuelEntry::archive()->whereMonth('created_at', $m)->whereYear('created_at', $y)->sum('montant_total'),
            ],

            'interventions' => [
                'nb_total'   => Intervention::archive()->whereMonth('created_at', $m)->whereYear('created_at', $y)->count(),
                'total_cout' => (float) Intervention::archive()->whereMonth('created_at', $m)->whereYear('created_at', $y)->sum('cout_total'),
            ],

            'alertes' => [
                'nb_total'    => Alert::archive()->whereMonth('created_at', $m)->whereYear('created_at', $y)->count(),
                'nb_critique' => Alert::archive()->whereMonth('created_at', $m)->whereYear('created_at', $y)->where('priorite', 'critique')->count(),
                'nb_haute'    => Alert::archive()->whereMonth('created_at', $m)->whereYear('created_at', $y)->where('priorite', 'haute')->count(),
                'nb_traitees' => Alert::archive()->whereMonth('created_at', $m)->whereYear('created_at', $y)->where('statut', 'traitee')->count(),
            ],

            default => [
                'nb_total'     => MissionOrder::archive()->whereMonth('created_at', $m)->whereYear('created_at', $y)->count(),
                'km_parcourus' => (int) MissionOrder::archive()->whereMonth('created_at', $m)->whereYear('created_at', $y)->whereNotNull('km_retour')->whereNotNull('km_depart')->get()->sum(fn($o) => max(0, $o->km_retour - $o->km_depart)),
            ],
        };
    }

    private function loadDetail(int $id): mixed
    {
        return match ($this->activeModule) {
            'carburant'     => FuelEntry::with(['vehicle', 'driver', 'user'])->find($id),
            'interventions' => Intervention::with(['vehicle', 'user'])->find($id),
            'alertes'       => Alert::with(['alertable', 'treatedBy'])->find($id),
            default         => MissionOrder::with(['vehicle', 'driver', 'user', 'validator'])->find($id),
        };
    }
}
