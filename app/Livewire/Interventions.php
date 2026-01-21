<?php

namespace App\Livewire;

use App\Models\Intervention;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Interventions extends Component
{
    use WithPagination;

    #[\Livewire\Attributes\Layout('layouts.app')]

    public array $filters = [
        'vehicle_id' => '',
        'type' => '',
        'statut' => '',
    ];

    public array $form = [];
    public ?int $editingId = null;
    public ?Intervention $detailIntervention = null;

    public bool $showFormModal = false;
    public bool $showDetailsModal = false;

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        $interventions = Intervention::query()
            ->with(['vehicle', 'user'])
            ->when($this->filters['vehicle_id'], fn($q, $v) => $q->where('vehicle_id', $v))
            ->when($this->filters['type'], fn($q, $v) => $q->where('type', $v))
            ->when($this->filters['statut'], fn($q, $v) => $q->where('statut', $v))
            ->latest('date_intervention')
            ->paginate(15);

        $stats = [
            'total_cout' => Intervention::where('statut', 'termine')->sum('cout_total'),
            'nb_interventions' => Intervention::count(),
            'planifiees' => Intervention::where('statut', 'planifie')->count(),
        ];

        return view('livewire.interventions', [
            'interventions' => $interventions,
            'vehicles' => Vehicle::orderBy('immatriculation')->get(),
            'types' => Intervention::TYPES,
            'statuts' => Intervention::STATUTS,
            'resultats_ct' => Intervention::RESULTATS_CT,
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
            'type' => 'entretien',
            'titre' => '',
            'description' => '',
            'date_intervention' => date('Y-m-d'),
            'date_prochaine' => '',
            'kilometrage' => '',
            'km_prochaine' => '',
            'cout_pieces' => 0,
            'cout_main_oeuvre' => 0,
            'cout_total' => 0,
            'prestataire' => '',
            'numero_facture' => '',
            'assureur' => '',
            'numero_police' => '',
            'date_expiration_assurance' => '',
            'date_expiration_ct' => '',
            'resultat_ct' => '',
            'statut' => 'termine',
            'observations' => '',
        ];
        $this->editingId = null;
    }

    public function updatedFormCoutPieces(): void
    {
        $this->calculateTotal();
    }

    public function updatedFormCoutMainOeuvre(): void
    {
        $this->calculateTotal();
    }

    private function calculateTotal(): void
    {
        $this->form['cout_total'] = ($this->form['cout_pieces'] ?: 0) + ($this->form['cout_main_oeuvre'] ?: 0);
    }

    public function updatedFormVehicleId($value): void
    {
        if ($value) {
            $vehicle = Vehicle::find($value);
            if ($vehicle) {
                $this->form['kilometrage'] = $vehicle->kilometrage_actuel;
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
        $intervention = Intervention::findOrFail($id);
        $this->editingId = $intervention->id;
        $this->form = [
            'vehicle_id' => $intervention->vehicle_id,
            'type' => $intervention->type,
            'titre' => $intervention->titre,
            'description' => $intervention->description,
            'date_intervention' => $intervention->date_intervention->format('Y-m-d'),
            'date_prochaine' => $intervention->date_prochaine?->format('Y-m-d') ?? '',
            'kilometrage' => $intervention->kilometrage,
            'km_prochaine' => $intervention->km_prochaine,
            'cout_pieces' => $intervention->cout_pieces,
            'cout_main_oeuvre' => $intervention->cout_main_oeuvre,
            'cout_total' => $intervention->cout_total,
            'prestataire' => $intervention->prestataire,
            'numero_facture' => $intervention->numero_facture,
            'assureur' => $intervention->assureur,
            'numero_police' => $intervention->numero_police,
            'date_expiration_assurance' => $intervention->date_expiration_assurance?->format('Y-m-d') ?? '',
            'date_expiration_ct' => $intervention->date_expiration_ct?->format('Y-m-d') ?? '',
            'resultat_ct' => $intervention->resultat_ct ?? '',
            'statut' => $intervention->statut,
            'observations' => $intervention->observations,
        ];
        $this->showFormModal = true;
    }

    public function openDetails(int $id): void
    {
        $this->detailIntervention = Intervention::with(['vehicle', 'user'])->findOrFail($id);
        $this->showDetailsModal = true;
    }

    public function save(): void
    {
        $rules = [
            'form.vehicle_id' => 'required|exists:vehicles,id',
            'form.type' => 'required|in:entretien,reparation,controle_technique,assurance,autre',
            'form.titre' => 'required|string|max:255',
            'form.description' => 'nullable|string',
            'form.date_intervention' => 'required|date',
            'form.date_prochaine' => 'nullable|date',
            'form.kilometrage' => 'nullable|integer|min:0',
            'form.km_prochaine' => 'nullable|integer|min:0',
            'form.cout_pieces' => 'nullable|numeric|min:0',
            'form.cout_main_oeuvre' => 'nullable|numeric|min:0',
            'form.cout_total' => 'nullable|numeric|min:0',
            'form.prestataire' => 'nullable|string|max:255',
            'form.numero_facture' => 'nullable|string|max:100',
            'form.assureur' => 'nullable|string|max:255',
            'form.numero_police' => 'nullable|string|max:100',
            'form.date_expiration_assurance' => 'nullable|date',
            'form.date_expiration_ct' => 'nullable|date',
            'form.resultat_ct' => 'nullable|in:favorable,defavorable,contre_visite',
            'form.statut' => 'required|in:planifie,en_cours,termine,annule',
            'form.observations' => 'nullable|string',
        ];

        $validated = $this->validate($rules);
        $data = $validated['form'];
        $data['user_id'] = auth()->id();

        // Convertir les champs vides en null
        foreach (['date_prochaine', 'date_expiration_assurance', 'date_expiration_ct', 'resultat_ct'] as $field) {
            if (empty($data[$field])) {
                $data[$field] = null;
            }
        }

        if ($this->editingId) {
            Intervention::findOrFail($this->editingId)->update($data);
            session()->flash('status', 'Intervention mise à jour.');
        } else {
            Intervention::create($data);

            // Mettre à jour le km du véhicule si supérieur
            if ($data['kilometrage']) {
                $vehicle = Vehicle::find($data['vehicle_id']);
                if ($vehicle && $data['kilometrage'] > $vehicle->kilometrage_actuel) {
                    $vehicle->update(['kilometrage_actuel' => $data['kilometrage']]);
                }
            }

            session()->flash('status', 'Intervention enregistrée.');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Intervention::findOrFail($id)->delete();
        session()->flash('status', 'Intervention supprimée.');
    }

    public function markAs(int $id, string $statut): void
    {
        $intervention = Intervention::findOrFail($id);
        $intervention->update(['statut' => $statut]);
        session()->flash('status', 'Statut mis à jour.');
    }
}