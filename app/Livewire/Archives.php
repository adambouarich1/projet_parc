<?php

namespace App\Livewire;

use App\Models\MissionOrder;
use App\Models\FuelEntry;
use App\Models\Intervention;
use App\Models\Insurance;
use App\Models\Alert;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Archives extends Component
{
    use WithPagination;

    #[\Livewire\Attributes\Layout('layouts.app')]

    public string $activeTab = 'missions';
    public string $search = '';

    protected $paginationTheme = 'tailwind';

    public function render(): View
    {
        $data = [];

        // Stats par type
        $stats = [
            'missions' => MissionOrder::archive()->count(),
            'carburant' => FuelEntry::archive()->count(),
            'interventions' => Intervention::archive()->count(),
            'assurances' => Insurance::where('statut', 'archivee')->count(),
            'alertes' => Alert::archive()->count(),
        ];

        // Données selon l'onglet actif
        switch ($this->activeTab) {
            case 'missions':
                $data['items'] = MissionOrder::archive()
                    ->with(['vehicle', 'driver', 'user'])
                    ->when($this->search, fn($q, $v) => $q->where(function($query) use ($v) {
                        $query->where('reference', 'like', "%{$v}%")
                              ->orWhere('objet', 'like', "%{$v}%")
                              ->orWhere('destination', 'like', "%{$v}%");
                    }))
                    ->latest()
                    ->paginate(10);
                break;

            case 'carburant':
                $data['items'] = FuelEntry::archive()
                    ->with(['vehicle', 'driver', 'user'])
                    ->when($this->search, fn($q, $v) => $q->where(function($query) use ($v) {
                        $query->where('station', 'like', "%{$v}%")
                              ->orWhere('numero_bon', 'like', "%{$v}%")
                              ->orWhereHas('vehicle', fn($q2) => $q2->where('immatriculation', 'like', "%{$v}%"));
                    }))
                    ->latest()
                    ->paginate(10);
                break;

            case 'interventions':
                $data['items'] = Intervention::archive()
                    ->with(['vehicle', 'user'])
                    ->when($this->search, fn($q, $v) => $q->where(function($query) use ($v) {
                        $query->where('titre', 'like', "%{$v}%")
                              ->orWhere('prestataire', 'like', "%{$v}%")
                              ->orWhereHas('vehicle', fn($q2) => $q2->where('immatriculation', 'like', "%{$v}%"));
                    }))
                    ->latest()
                    ->paginate(10);
                break;

            case 'assurances':
                $data['items'] = Insurance::where('statut', 'archivee')
                    ->with(['vehicle', 'user'])
                    ->when($this->search, fn($q, $v) => $q->where(function($query) use ($v) {
                        $query->where('assureur', 'like', "%{$v}%")
                              ->orWhere('numero_police', 'like', "%{$v}%")
                              ->orWhereHas('vehicle', fn($q2) => $q2->where('immatriculation', 'like', "%{$v}%"));
                    }))
                    ->latest()
                    ->paginate(10);
                break;

            case 'alertes':
                $data['items'] = Alert::archive()
                    ->with(['alertable', 'treatedBy'])
                    ->when($this->search, fn($q, $v) => $q->where(function($query) use ($v) {
                        $query->where('titre', 'like', "%{$v}%")
                              ->orWhere('message', 'like', "%{$v}%");
                    }))
                    ->latest()
                    ->paginate(10);
                break;
        }

        return view('livewire.archives', array_merge($data, [
            'stats' => $stats,
        ]));
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->search = '';
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function restore(string $type, int $id): void
    {
        switch ($type) {
            case 'mission':
                $item = MissionOrder::findOrFail($id);
                $item->update(['statut' => MissionOrder::STATUT_CLOTURE]);
                break;
            case 'carburant':
                $item = FuelEntry::findOrFail($id);
                $item->update(['statut' => FuelEntry::STATUT_VALIDE]);
                break;
            case 'intervention':
                $item = Intervention::findOrFail($id);
                $item->update(['statut' => Intervention::STATUT_TERMINE]);
                break;
            case 'assurance':
                $item = Insurance::findOrFail($id);
                $item->update(['statut' => Insurance::STATUT_EXPIREE]);
                break;
            case 'alerte':
                $item = Alert::findOrFail($id);
                $item->update(['statut' => Alert::STATUT_VUE]);
                break;
        }

        session()->flash('status', 'Élément restauré avec succès.');
    }

    public function deletePermanently(string $type, int $id): void
    {
        switch ($type) {
            case 'mission':
                MissionOrder::findOrFail($id)->delete();
                break;
            case 'carburant':
                FuelEntry::findOrFail($id)->delete();
                break;
            case 'intervention':
                Intervention::findOrFail($id)->delete();
                break;
            case 'assurance':
                Insurance::findOrFail($id)->delete();
                break;
            case 'alerte':
                Alert::findOrFail($id)->delete();
                break;
        }

        session()->flash('status', 'Élément supprimé définitivement.');
    }
}