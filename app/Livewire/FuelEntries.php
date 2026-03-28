<?php
 
namespace App\Livewire;
 
use App\Models\FuelEntry;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\MissionOrder;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PrixCarburantUnitaire;
 
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
    public bool $showPrixModal = false;
    public string $editingPrixType = '';
    public float $editingPrixValue = 0;
 
    protected $paginationTheme = 'tailwind';
 
    public function mount(): void
    {
        $this->resetForm();
    }
 
    public function render(): View
    {
        $entries = FuelEntry::query()
            ->nonArchive()
            ->with(['vehicle', 'driver', 'user', 'missionOrder'])
            ->when($this->filters['vehicle_id'], fn($q, $v) => $q->where('vehicle_id', $v))
            ->when($this->filters['date_from'], fn($q, $v) => $q->whereDate('date_plein', '>=', $v))
            ->when($this->filters['date_to'], fn($q, $v) => $q->whereDate('date_plein', '<=', $v))
            ->latest('date_plein')
            ->paginate(15);
 
        $stats = [
            'total_litres' => FuelEntry::sum('quantite_litres'),
            'total_montant' => FuelEntry::sum('montant_total'),
            'nb_pleins' => FuelEntry::count(),
        ];
 
        $prixEssence = PrixCarburantUnitaire::getPrix('essence');
        $prixDiesel = PrixCarburantUnitaire::getPrix('diesel');
 
        return view('livewire.fuel-entries', [
            'entries' => $entries,
            'vehicles' => Vehicle::orderBy('immatriculation')->get(),
            'drivers' => Driver::orderBy('nom')->get(),
            'missions' => MissionOrder::where('statut', 'validé')->get(),
            'stats' => $stats,
            'prixEssence' => $prixEssence,
            'prixDiesel' => $prixDiesel,
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
            'station' => '',
            'type_carburant' => '',
            'numero_bon' => '',
            'observations' => '',
        ];
        $this->editingId = null;
    }
 
    /**
     * Quand le montant change, calculer la quantité
     */
    public function updatedFormMontantTotal(): void
    {
        $this->recalculerQuantite();
    }
 
    /**
     * Quand le prix unitaire change, recalculer la quantité
     */
    public function updatedFormPrixUnitaire(): void
    {
        $this->recalculerQuantite();
    }
 
    /**
     * Recalcule la quantité à partir du montant et du prix unitaire
     */
    private function recalculerQuantite(): void
    {
        if (!empty($this->form['montant_total']) && !empty($this->form['prix_unitaire']) && $this->form['prix_unitaire'] > 0) {
            $this->form['quantite_litres'] = round($this->form['montant_total'] / $this->form['prix_unitaire'], 2);
        } else {
            $this->form['quantite_litres'] = '';
        }
    }
 
    /**
     * Quand le véhicule change, auto-remplir type carburant et prix unitaire
     */
    public function updatedFormVehicleId($value): void
    {
        if ($value) {
            $vehicle = Vehicle::find($value);
 
            if ($vehicle && $vehicle->carburant) {
                $this->form['type_carburant'] = $vehicle->carburant;
 
                $typeCarburant = strtolower(trim($vehicle->carburant));
                $prixRecord = PrixCarburantUnitaire::whereRaw('LOWER(type_carburant) = ?', [$typeCarburant])->first();
 
                if ($prixRecord) {
                    $this->form['prix_unitaire'] = (float) $prixRecord->prix;
                } else {
                    if (in_array($typeCarburant, ['essence', 'diesel'])) {
                        $this->form['prix_unitaire'] = $typeCarburant === 'essence' ? 13.50 : 11.20;
                    }
                }
 
                $this->recalculerQuantite();
            }
        } else {
            $this->form['type_carburant'] = '';
            $this->form['prix_unitaire'] = '';
            $this->form['quantite_litres'] = '';
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
            'form.quantite_litres' => 'required|numeric|min:0.01',
            'form.prix_unitaire' => 'required|numeric|min:0.01',
            'form.montant_total' => 'required|numeric|min:0.01',
            'form.station' => 'nullable|string|max:255',
            'form.type_carburant' => 'nullable|string|max:50',
            'form.numero_bon' => 'nullable|string|max:100',
            'form.observations' => 'nullable|string',
        ]);
 
        $data = $validated['form'];
        $data['user_id'] = auth()->id();
        $data['driver_id'] = $data['driver_id'] ?: null;
        $data['mission_order_id'] = $data['mission_order_id'] ?: null;
 
        if ($this->editingId) {
            FuelEntry::findOrFail($this->editingId)->update($data);
            session()->flash('status', 'Entrée carburant mise à jour.');
        } else {
            FuelEntry::create($data);
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
 
    public function archive(int $id): void
    {
        $entry = FuelEntry::findOrFail($id);
        $entry->update(['statut' => FuelEntry::STATUT_ARCHIVE]);
        session()->flash('status', 'Entrée carburant archivée.');
    }
 
    public function openEditPrix(string $type): void
    {
        $this->editingPrixType = $type;
        $this->editingPrixValue = PrixCarburantUnitaire::getPrix($type);
        $this->showPrixModal = true;
    }
 
    public function savePrix(): void
    {
        $this->validate([
            'editingPrixValue' => 'required|numeric|min:0.01|max:999.99'
        ]);
 
        PrixCarburantUnitaire::setPrix($this->editingPrixType, $this->editingPrixValue);
 
        session()->flash('status', 'Prix du ' . $this->editingPrixType . ' mis à jour.');
        $this->showPrixModal = false;
    }
}
 






