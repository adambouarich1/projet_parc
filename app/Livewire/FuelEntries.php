<?php

namespace App\Livewire;

use App\Models\FuelEntry;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\MissionOrder;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class FuelEntries extends Component
{
    use WithPagination;

    #[\Livewire\Attributes\Layout('layouts.app')]

    public array $filters = [
        'vehicle_id' => '',
        'date_from' => '',
        'date_to' => '',
    ];

    public array $form = [];
    public ?int $editingId = null;
    public ?FuelEntry $detailEntry = null;

    public bool $showFormModal = false;
    public bool $showDetailsModal = false;

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        $entries = FuelEntry::query()
            ->with(['vehicle', 'driver', 'user', 'missionOrder'])
            ->when($this->filters['vehicle_id'], fn($q, $v) => $q->where('vehicle_id', $v))
            ->when($this->filters['date_from'], fn($q, $v) => $q->whereDate('date_plein', '>=', $v))
            ->when($this->filters['date_to'], fn($q, $v) => $q->whereDate('date_plein', '<=', $v))
            ->latest('date_plein')
            ->paginate(15);

        // Stats globales
        $stats = [
            'total_litres' => FuelEntry::sum('quantite_litres'),
            'total_montant' => FuelEntry::sum('montant_total'),
            'nb_pleins' => FuelEntry::count(),
        ];

        return view('livewire.fuel-entries', [
            'entries' => $entries,
            'vehicles' => Vehicle::orderBy('immatriculation')->get(),
            'drivers' => Driver::orderBy('nom')->get(),
            'missions' => MissionOrder::where('statut', 'en_cours')->get(),
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
            'driver_id' => '',
            'mission_order_id' => '',
            'date_plein' => date('Y-m-d'),
            'quantite_litres' => '',
            'prix_unitaire' => '',
            'montant_total' => '',
            'kilometrage' => '',
            'station' => '',
            'type_carburant' => '',
            'numero_bon' => '',
            'observations' => '',
        ];
        $this->editingId = null;
    }

    public function updatedFormQuantiteLitres(): void
    {
        $this->calculateTotal();
    }

    public function updatedFormPrixUnitaire(): void
    {
        $this->calculateTotal();
    }

    private function calculateTotal(): void
    {
        if ($this->form['quantite_litres'] && $this->form['prix_unitaire']) {
            $this->form['montant_total'] = round($this->form['quantite_litres'] * $this->form['prix_unitaire'], 2);
        }
    }

    public function updatedFormVehicleId($value): void
    {
        if ($value) {
            $vehicle = Vehicle::find($value);
            if ($vehicle) {
                $this->form['kilometrage'] = $vehicle->kilometrage_actuel;
                $this->form['type_carburant'] = $vehicle->carburant;
            }
        }
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $entry = FuelEntry::findOrFail($id);
        $this->editingId = $entry->id;
        $this->form = [
            'vehicle_id' => $entry->vehicle_id,
            'driver_id' => $entry->driver_id,
            'mission_order_id' => $entry->mission_order_id,
            'date_plein' => $entry->date_plein->format('Y-m-d'),
            'quantite_litres' => $entry->quantite_litres,
            'prix_unitaire' => $entry->prix_unitaire,
            'montant_total' => $entry->montant_total,
            'kilometrage' => $entry->kilometrage,
            'station' => $entry->station,
            'type_carburant' => $entry->type_carburant,
            'numero_bon' => $entry->numero_bon,
            'observations' => $entry->observations,
        ];
        $this->showFormModal = true;
    }

    public function openDetails(int $id): void
    {
        $this->detailEntry = FuelEntry::with(['vehicle', 'driver', 'user', 'missionOrder'])->findOrFail($id);
        $this->showDetailsModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'form.vehicle_id' => 'required|exists:vehicles,id',
            'form.driver_id' => 'nullable|exists:drivers,id',
            'form.mission_order_id' => 'nullable|exists:mission_orders,id',
            'form.date_plein' => 'required|date',
            'form.quantite_litres' => 'required|numeric|min:1',
            'form.prix_unitaire' => 'required|numeric|min:0.01',
            'form.montant_total' => 'required|numeric|min:0.01',
            'form.kilometrage' => 'required|integer|min:0',
            'form.station' => 'nullable|string|max:255',
            'form.type_carburant' => 'nullable|string|max:50',
            'form.numero_bon' => 'nullable|string|max:100',
            'form.observations' => 'nullable|string',
        ]);

        $data = $validated['form'];
        $data['user_id'] = auth()->id();

        // Convertir les champs vides en null
        $data['driver_id'] = $data['driver_id'] ?: null;
        $data['mission_order_id'] = $data['mission_order_id'] ?: null;

        if ($this->editingId) {
            FuelEntry::findOrFail($this->editingId)->update($data);
            session()->flash('status', 'Entrée carburant mise à jour.');
        } else {
            FuelEntry::create($data);
            
            // Mettre à jour le km du véhicule si supérieur
            $vehicle = Vehicle::find($data['vehicle_id']);
            if ($vehicle && $data['kilometrage'] > $vehicle->kilometrage_actuel) {
                $vehicle->update(['kilometrage_actuel' => $data['kilometrage']]);
            }
            
            session()->flash('status', 'Entrée carburant enregistrée.');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        FuelEntry::findOrFail($id)->delete();
        session()->flash('status', 'Entrée supprimée.');
    }
}