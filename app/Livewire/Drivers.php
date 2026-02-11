<?php

namespace App\Livewire;

use App\Http\Requests\DriverRequest;
use App\Models\Driver;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Drivers extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = ''; // Ajouté pour la barre de recherche

    public array $filters = [
        'service_affecte' => '',
        'statut_actuel' => '',
        'categories' => '',
    ];

    public array $form = [];
    public ?int $editingId = null;
    public ?string $currentPhotoPath = null;
    public ?string $currentScanPath = null;
    public $photo;
    public $scan_permis;
    public ?Driver $detailDriver = null;

    public bool $showFormModal = false;
    public bool $showDetailsModal = false;

    // Tableaux associatifs pour corriger la validation et l'affichage
    public array $statuts = [
        'Disponible' => 'Disponible',
        'En congé' => 'En congé',
        'Maladie' => 'Maladie',
        'Non disponible' => 'Non disponible'
    ];

    public array $categoriesPermis = [
        'A' => 'A (Moto)',
        'B' => 'B (Voiture)',
        'C' => 'C (Camion)',
        'D' => 'D (Bus)',
        'E' => 'E (Remorque)',
        'F' => 'F (Spécial)'
    ];

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        $drivers = Driver::query()
            ->when($this->search, fn ($query) => 
                $query->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('prenom', 'like', '%' . $this->search . '%')
                      ->orWhere('matricule', 'like', '%' . $this->search . '%')
            )
            ->when($this->filters['service_affecte'], fn ($query, $value) => $query->where('service_affecte', 'like', '%' . $value . '%'))
            ->when($this->filters['statut_actuel'], fn ($query, $value) => $query->where('statut_actuel', $value))
            ->when($this->filters['categories'], fn ($query, $value) => $query->where('categories', 'like', '%' . $value . '%'))
            ->latest()
            ->paginate(12);

        return view('livewire.drivers', [
            'drivers' => $drivers,
        ]);
    }

    public function updatingFilters(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->form = [
            'nom' => '',
            'prenom' => '',
            'matricule' => '',
            'cin' => '',
            'date_naissance' => '',
            'service_affecte' => '',
            'responsable_hierarchique' => '',
            'poste_occupe' => '',
            'telephone' => '',
            'email_pro' => '',
            'num_permis' => '',
            'date_delivrance' => '',
            'date_expiration' => '',
            'categories' => '', // Sera stocké souvent sous forme "B" ou "B,C"
            'statut_actuel' => 'Disponible',
        ];

        $this->photo = null;
        $this->scan_permis = null;
        $this->editingId = null;
        $this->currentPhotoPath = null;
        $this->currentScanPath = null;
        $this->resetValidation();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEdit(int $driverId): void
    {
        $driver = Driver::findOrFail($driverId);
        $this->editingId = $driver->id;
        $this->currentPhotoPath = $driver->photo_path;
        $this->currentScanPath = $driver->scan_permis_path;

        $this->form = [
            'nom' => $driver->nom,
            'prenom' => $driver->prenom,
            'matricule' => $driver->matricule,
            'cin' => $driver->cin,
            'date_naissance' => optional($driver->date_naissance)->format('Y-m-d'),
            'service_affecte' => $driver->service_affecte,
            'responsable_hierarchique' => $driver->responsable_hierarchique,
            'poste_occupe' => $driver->poste_occupe,
            'telephone' => $driver->telephone,
            'email_pro' => $driver->email_pro,
            'num_permis' => $driver->num_permis,
            'date_delivrance' => optional($driver->date_delivrance)->format('Y-m-d'),
            'date_expiration' => optional($driver->date_expiration)->format('Y-m-d'),
            'categories' => $driver->categories,
            'statut_actuel' => $driver->statut_actuel,
        ];

        $this->photo = null;
        $this->scan_permis = null;
        $this->showFormModal = true;
    }

    public function openDetails(int $driverId): void
    {
        $this->detailDriver = Driver::findOrFail($driverId);
        $this->showDetailsModal = true;
    }

    public function save(): void
    {
        $isEdit = (bool) $this->editingId;
        $this->normalizeForm();
        $validated = $this->validate($this->validationRules());
        $payload = $validated['form'];

        // Gestion Photo
        if ($this->photo) {
            if ($this->currentPhotoPath) {
                Storage::disk('public')->delete($this->currentPhotoPath);
            }
            $payload['photo_path'] = $this->photo->store('drivers/photos', 'public');
        } elseif ($this->currentPhotoPath) {
            $payload['photo_path'] = $this->currentPhotoPath;
        }

        // Gestion Scan Permis
        if ($this->scan_permis) {
            if ($this->currentScanPath) {
                Storage::disk('public')->delete($this->currentScanPath);
            }
            $payload['scan_permis_path'] = $this->scan_permis->store('drivers/permis', 'public');
        } elseif ($this->currentScanPath) {
            $payload['scan_permis_path'] = $this->currentScanPath;
        }

        if ($this->editingId) {
            Driver::findOrFail($this->editingId)->update($payload);
        } else {
            Driver::create($payload);
        }

        $this->resetForm();
        $this->showFormModal = false;
        $this->showDetailsModal = false;
        $this->resetPage();
        session()->flash('status', $isEdit ? 'Conducteur mis à jour.' : 'Conducteur créé.');
    }

    public function deleteDriver(int $driverId): void
    {
        $driver = Driver::findOrFail($driverId);

        if ($driver->photo_path) Storage::disk('public')->delete($driver->photo_path);
        if ($driver->scan_permis_path) Storage::disk('public')->delete($driver->scan_permis_path);

        $driver->delete();
        $this->resetPage();
        session()->flash('status', 'Conducteur supprimé.');
    }

    private function validationRules(): array
    {
        $rules = [];
        // On suppose que DriverRequest existe et fonctionne
        foreach (DriverRequest::baseRules($this->editingId) as $field => $rule) {
            if (in_array($field, ['photo', 'scan_permis'], true)) {
                $rules[$field] = $rule;
                continue;
            }
            $rules['form.' . $field] = $rule;
        }
        return $rules;
    }

    private function normalizeForm(): void
    {
        foreach ($this->form as $key => $value) {
            if ($value === '') {
                $this->form[$key] = null;
            }
        }
    }
    
}