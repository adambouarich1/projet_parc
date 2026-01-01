<?php
    use Illuminate\Support\Facades\Storage;

    $statusColors = [
        'En service' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
        'En réparation' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'Immobile' => 'bg-sky-100 text-sky-700 border border-sky-200',
        'Hors service' => 'bg-rose-100 text-rose-700 border border-rose-200',
        'Réformé' => 'bg-gray-800 text-gray-100 border border-gray-700',
    ];
?>

<div class="space-y-4 text-gray-100">
    @if (session()->has('status'))
        <div class="rounded-md bg-emerald-900/40 border border-emerald-700 text-emerald-100 px-4 py-3">
            {{ session('status') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-4">
        <div class="lg:w-1/4 space-y-4">
            <div class="bg-gray-900 shadow rounded-lg p-4 space-y-3 border border-gray-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-100">Filtres</h3>
                    <button
                        type="button"
                        wire:click="$set('filters', { service_affecte: '', categorie_vehicule: '', carburant: '', statut_actuel: '' })"
                        class="text-xs text-gray-400 hover:text-gray-200"
                    >
                        Réinitialiser
                    </button>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Service</label>
                    <input type="text" wire:model.debounce.400ms="filters.service_affecte" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Service affecté">
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Catégorie</label>
                    <select wire:model.live="filters.categorie_vehicule" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Toutes</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Carburant</label>
                    <select wire:model.live="filters.carburant" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Tous</option>
                        @foreach ($carburants as $fuel)
                            <option value="{{ $fuel }}">{{ $fuel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Statut</label>
                    <select wire:model.live="filters.statut_actuel" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Tous</option>
                        @foreach ($statuts as $statut)
                            <option value="{{ $statut }}">{{ $statut }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button
                type="button"
                wire:click="openCreate"
                class="w-full inline-flex items-center justify-center px-4 py-3 bg-indigo-500 text-white rounded-md shadow hover:bg-indigo-600 transition"
            >
                Ajouter un véhicule
            </button>
        </div>

        <div class="flex-1 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Module CRUD complet</p>
                    <h3 class="text-lg font-semibold text-gray-100">Gestion du parc</h3>
                </div>
                <div class="hidden lg:block">
                    <button
                        type="button"
                        wire:click="openCreate"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 transition"
                    >
                        + Ajouter un véhicule
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse ($vehicles as $vehicle)
                    <div class="relative bg-gray-900 rounded-xl shadow border border-gray-800 p-4 flex flex-col" wire:key="vehicle-{{ $vehicle->id }}">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-xs uppercase text-gray-400">Parc</p>
                                <h4 class="text-lg font-semibold text-gray-100">{{ $vehicle->marque }} {{ $vehicle->modele }}</h4>
                                <p class="text-sm text-gray-300">Immat: {{ $vehicle->immatriculation }}</p>
                            </div>
                            <button
                                type="button"
                                wire:click="openDetails({{ $vehicle->id }})"
                                class="h-8 w-8 inline-flex items-center justify-center rounded-full border border-gray-700 text-gray-200 hover:bg-gray-800"
                                title="Détails"
                            >
                                i
                            </button>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-sm text-gray-200 font-medium">
                                Statut:
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$vehicle->statut_actuel] ?? 'bg-gray-800 text-gray-200 border border-gray-700' }}">
                                    {{ $vehicle->statut_actuel }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-300">
                                {{ number_format($vehicle->kilometrage_actuel, 0, ',', ' ') }} km
                            </div>
                        </div>

                        <div class="mt-2 text-xs text-gray-400">
                            {{ $vehicle->categorie_vehicule ?: 'Catégorie ?' }} • {{ $vehicle->carburant ?: 'Carburant ?' }}
                        </div>

                        <div class="mt-4 flex items-center justify-end gap-2">
                            <button
                                type="button"
                                wire:click="openEdit({{ $vehicle->id }})"
                                class="px-3 py-1.5 text-xs font-semibold text-indigo-200 bg-indigo-900/40 rounded-md hover:bg-indigo-900/60"
                            >
                                Modifier
                            </button>
                            <button
                                type="button"
                                x-data
                                x-on:click.prevent="if (confirm('Supprimer ce véhicule ?')) { $wire.deleteVehicle({{ $vehicle->id }}) }"
                                class="px-3 py-1.5 text-xs font-semibold text-rose-200 bg-rose-900/40 rounded-md hover:bg-rose-900/60"
                            >
                                Supprimer
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-gray-900 border border-dashed border-gray-800 rounded-lg p-8 text-center text-gray-300">
                        Aucun véhicule pour le moment. Cliquez sur « Ajouter un véhicule » pour démarrer.
                    </div>
                @endforelse
            </div>

            <div>
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>

    @if ($showFormModal)
        <div class="fixed inset-0 bg-black/60 z-40 flex items-start justify-center overflow-y-auto py-10 px-4" wire:key="form-modal">
            <div class="bg-gray-900 rounded-xl shadow-2xl max-w-6xl w-full relative border border-gray-800">
                <button
                    type="button"
                    class="absolute top-3 right-3 h-8 w-8 inline-flex items-center justify-center rounded-full bg-gray-800 text-gray-200 hover:bg-gray-700"
                    wire:click="$set('showFormModal', false)"
                >
                    ✕
                </button>
                <form wire:submit.prevent="save" class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Formulaire véhicule</p>
                            <h3 class="text-xl font-semibold text-gray-100">
                                {{ $editingId ? 'Modifier un véhicule' : 'Ajouter un véhicule' }}
                            </h3>
                        </div>
                        <div class="text-sm text-gray-400">
                            Les champs techniques, administratifs et financiers sont regroupés.
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-1 space-y-3">
                            <h4 class="text-sm font-semibold text-gray-100">Administratif</h4>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-300">Immatriculation *</label>
                                <input type="text" wire:model.live="form.immatriculation" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                @error('form.immatriculation') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-300">VIN</label>
                                <input type="text" wire:model.live="form.vin" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                @error('form.vin') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-300">Type carte grise</label>
                                <input type="text" wire:model.live="form.type_carte_grise" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-300">Propriétaire légal</label>
                                <input type="text" wire:model.live="form.proprietaire_legal" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-300">Service affecté</label>
                                <input type="text" wire:model.live="form.service_affecte" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-300">Responsable du service</label>
                                <input type="text" wire:model.live="form.responsable_service" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-300">Lieu de stationnement</label>
                                <input type="text" wire:model.live="form.lieu_stationnement" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-300">Date mise en circulation</label>
                                <input type="date" wire:model.live="form.date_mise_circulation" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-300">Date d'acquisition</label>
                                <input type="date" wire:model.live="form.date_acquisition" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-300">Mode d'acquisition</label>
                                <input type="text" wire:model.live="form.mode_acquisition" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-300">Puissance fiscale</label>
                                <input type="number" step="0.01" wire:model.live="form.puissance_fiscale" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-300">Catégorie fiscale</label>
                                <input type="text" wire:model.live="form.categorie_fiscale" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                        </div>

                        <div class="lg:col-span-1 space-y-3">
                            <h4 class="text-sm font-semibold text-gray-100">Technique</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Marque *</label>
                                    <input type="text" wire:model.live="form.marque" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                    @error('form.marque') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Modèle *</label>
                                    <input type="text" wire:model.live="form.modele" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                    @error('form.modele') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Version</label>
                                    <input type="text" wire:model.live="form.version" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Catégorie véhicule</label>
                                    <select wire:model.live="form.categorie_vehicule" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                        <option value="">Sélectionner</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat }}">{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Carburant</label>
                                    <select wire:model.live="form.carburant" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                        <option value="">Sélectionner</option>
                                        @foreach ($carburants as $fuel)
                                            <option value="{{ $fuel }}">{{ $fuel }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.carburant') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Couleur</label>
                                    <input type="text" wire:model.live="form.couleur" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Poids à vide (kg)</label>
                                    <input type="number" step="0.01" wire:model.live="form.poids_vide" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">PTAC (kg)</label>
                                    <input type="number" step="0.01" wire:model.live="form.ptac" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Numéro moteur</label>
                                    <input type="text" wire:model.live="form.num_moteur" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Cylindrée</label>
                                    <input type="number" step="0.01" wire:model.live="form.cylindree" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Puissance DIN</label>
                                    <input type="number" step="0.01" wire:model.live="form.puissance_din" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Capacité réservoir</label>
                                    <input type="number" step="0.01" wire:model.live="form.capacite_reservoir" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Capacité de charge</label>
                                    <input type="number" step="0.01" wire:model.live="form.capacite_charge" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Nombre de places</label>
                                    <input type="number" wire:model.live="form.nombre_places" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Kilométrage initial *</label>
                                    <input type="number" wire:model.live="form.kilometrage_initial" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                    @error('form.kilometrage_initial') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Kilométrage actuel *</label>
                                    <input type="number" wire:model.live="form.kilometrage_actuel" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                    @error('form.kilometrage_actuel') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Heures moteur</label>
                                    <input type="number" wire:model.live="form.heures_moteur" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-1 space-y-3">
                            <h4 class="text-sm font-semibold text-gray-100">Financier & médias</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Prix d'achat</label>
                                    <input type="number" step="0.01" wire:model.live="form.prix_achat" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Fournisseur (nom)</label>
                                    <input type="text" wire:model.live="form.fournisseur_nom" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Fournisseur ICE</label>
                                    <input type="text" wire:model.live="form.fournisseur_ice" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Bon de commande</label>
                                    <input type="text" wire:model.live="form.bon_commande_ref" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Article budgétaire</label>
                                    <input type="text" wire:model.live="form.article_budgetaire" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Durée amortissement</label>
                                    <input type="number" wire:model.live="form.duree_amortissement" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Mode amortissement</label>
                                    <input type="text" wire:model.live="form.mode_amortissement" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs text-gray-300">Valeur nette comptable</label>
                                    <input type="number" step="0.01" wire:model.live="form.valeur_nette_comptable" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs text-gray-300">Statut *</label>
                                <select wire:model.live="form.statut_actuel" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                    @foreach ($statuts as $statut)
                                        <option value="{{ $statut }}">{{ $statut }}</option>
                                    @endforeach
                                </select>
                                @error('form.statut_actuel') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs text-gray-300">Image</label>
                                <input type="file" wire:model="image" accept="image/*" class="w-full text-sm">
                                @error('image') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                                <div class="flex items-center gap-3">
                                    @if ($image)
                                        <img src="{{ $image->temporaryUrl() }}" class="h-16 w-16 object-cover rounded border">
                                    @elseif ($currentImagePath)
                                        <img src="{{ Storage::url($currentImagePath) }}" class="h-16 w-16 object-cover rounded border">
                                    @endif
                                    <p class="text-xs text-gray-500">PNG/JPG jusqu'à 2 Mo.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t">
                        <button type="button" wire:click="$set('showFormModal', false)" class="px-4 py-2 rounded-md text-gray-200 bg-gray-800 hover:bg-gray-700 border border-gray-700">
                            Annuler
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-md bg-indigo-500 text-white hover:bg-indigo-600 shadow">
                            {{ $editingId ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showDetailsModal && $detailVehicle)
        <div class="fixed inset-0 bg-black/60 z-40 flex items-start justify-center overflow-y-auto py-10 px-4" wire:key="details-modal">
            <div class="bg-gray-900 rounded-xl shadow-2xl max-w-6xl w-full relative border border-gray-800">
                <button
                    type="button"
                    class="absolute top-3 right-3 h-8 w-8 inline-flex items-center justify-center rounded-full bg-gray-800 text-gray-200 hover:bg-gray-700"
                    wire:click="$set('showDetailsModal', false)"
                >
                    ✕
                </button>
                <div class="p-6 space-y-4 text-gray-100" x-data="{ tab: 'admin' }">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Fiche détaillée</p>
                            <h3 class="text-xl font-semibold text-gray-100">
                                {{ $detailVehicle->marque }} {{ $detailVehicle->modele }} — {{ $detailVehicle->immatriculation }}
                            </h3>
                            <div class="mt-2 inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$detailVehicle->statut_actuel] ?? 'bg-gray-800 text-gray-200 border border-gray-700' }}">
                                {{ $detailVehicle->statut_actuel }}
                            </div>
                        </div>
                        @if ($detailVehicle->image_path)
                            <img src="{{ Storage::url($detailVehicle->image_path) }}" class="h-20 w-20 object-cover rounded-lg border" alt="Photo véhicule">
                        @endif
                    </div>

                    <div class="flex gap-2">
                        <button type="button" class="px-3 py-2 rounded-md text-sm font-semibold"
                            :class="tab === 'admin' ? 'bg-indigo-900/60 text-indigo-200 border border-indigo-800' : 'bg-gray-800 text-gray-200 border border-gray-700'"
                            @click="tab = 'admin'">
                            Administratif
                        </button>
                        <button type="button" class="px-3 py-2 rounded-md text-sm font-semibold"
                            :class="tab === 'tech' ? 'bg-indigo-900/60 text-indigo-200 border border-indigo-800' : 'bg-gray-800 text-gray-200 border border-gray-700'"
                            @click="tab = 'tech'">
                            Technique
                        </button>
                        <button type="button" class="px-3 py-2 rounded-md text-sm font-semibold"
                            :class="tab === 'fin' ? 'bg-indigo-900/60 text-indigo-200 border border-indigo-800' : 'bg-gray-800 text-gray-200 border border-gray-700'"
                            @click="tab = 'fin'">
                            Financier
                        </button>
                    </div>

                    <div class="bg-gray-800 rounded-lg p-4 border border-gray-700" x-show="tab === 'admin'" x-cloak>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                            <div><span class="text-gray-500 text-xs">Immatriculation</span><p class="font-semibold">{{ $detailVehicle->immatriculation }}</p></div>
                            <div><span class="text-gray-500 text-xs">VIN</span><p class="font-semibold">{{ $detailVehicle->vin }}</p></div>
                            <div><span class="text-gray-500 text-xs">Type carte grise</span><p class="font-semibold">{{ $detailVehicle->type_carte_grise }}</p></div>
                            <div><span class="text-gray-500 text-xs">Propriétaire légal</span><p class="font-semibold">{{ $detailVehicle->proprietaire_legal }}</p></div>
                            <div><span class="text-gray-500 text-xs">Service affecté</span><p class="font-semibold">{{ $detailVehicle->service_affecte }}</p></div>
                            <div><span class="text-gray-500 text-xs">Responsable</span><p class="font-semibold">{{ $detailVehicle->responsable_service }}</p></div>
                            <div><span class="text-gray-500 text-xs">Stationnement</span><p class="font-semibold">{{ $detailVehicle->lieu_stationnement }}</p></div>
                            <div><span class="text-gray-500 text-xs">Mise en circulation</span><p class="font-semibold">{{ optional($detailVehicle->date_mise_circulation)->format('d/m/Y') }}</p></div>
                            <div><span class="text-gray-500 text-xs">Acquisition</span><p class="font-semibold">{{ optional($detailVehicle->date_acquisition)->format('d/m/Y') }}</p></div>
                            <div><span class="text-gray-500 text-xs">Mode acquisition</span><p class="font-semibold">{{ $detailVehicle->mode_acquisition }}</p></div>
                            <div><span class="text-gray-500 text-xs">Puissance fiscale</span><p class="font-semibold">{{ $detailVehicle->puissance_fiscale }}</p></div>
                            <div><span class="text-gray-500 text-xs">Catégorie fiscale</span><p class="font-semibold">{{ $detailVehicle->categorie_fiscale }}</p></div>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-lg p-4 border border-gray-700" x-show="tab === 'tech'" x-cloak>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                            <div><span class="text-gray-500 text-xs">Marque</span><p class="font-semibold text-gray-100">{{ $detailVehicle->marque }}</p></div>
                            <div><span class="text-gray-500 text-xs">Modèle</span><p class="font-semibold text-gray-100">{{ $detailVehicle->modele }}</p></div>
                            <div><span class="text-gray-500 text-xs">Version</span><p class="font-semibold text-gray-100">{{ $detailVehicle->version }}</p></div>
                            <div><span class="text-gray-500 text-xs">Catégorie véhicule</span><p class="font-semibold text-gray-100">{{ $detailVehicle->categorie_vehicule }}</p></div>
                            <div><span class="text-gray-500 text-xs">Carburant</span><p class="font-semibold text-gray-100">{{ $detailVehicle->carburant }}</p></div>
                            <div><span class="text-gray-500 text-xs">Couleur</span><p class="font-semibold text-gray-100">{{ $detailVehicle->couleur }}</p></div>
                            <div><span class="text-gray-500 text-xs">Poids à vide</span><p class="font-semibold text-gray-100">{{ $detailVehicle->poids_vide }}</p></div>
                            <div><span class="text-gray-500 text-xs">PTAC</span><p class="font-semibold text-gray-100">{{ $detailVehicle->ptac }}</p></div>
                            <div><span class="text-gray-500 text-xs">Numéro moteur</span><p class="font-semibold text-gray-100">{{ $detailVehicle->num_moteur }}</p></div>
                            <div><span class="text-gray-500 text-xs">Cylindrée</span><p class="font-semibold text-gray-100">{{ $detailVehicle->cylindree }}</p></div>
                            <div><span class="text-gray-500 text-xs">Puissance DIN</span><p class="font-semibold text-gray-100">{{ $detailVehicle->puissance_din }}</p></div>
                            <div><span class="text-gray-500 text-xs">Capacité réservoir</span><p class="font-semibold text-gray-100">{{ $detailVehicle->capacite_reservoir }}</p></div>
                            <div><span class="text-gray-500 text-xs">Capacité charge</span><p class="font-semibold text-gray-100">{{ $detailVehicle->capacite_charge }}</p></div>
                            <div><span class="text-gray-500 text-xs">Places</span><p class="font-semibold text-gray-100">{{ $detailVehicle->nombre_places }}</p></div>
                            <div><span class="text-gray-500 text-xs">Km initial</span><p class="font-semibold text-gray-100">{{ $detailVehicle->kilometrage_initial }}</p></div>
                            <div><span class="text-gray-500 text-xs">Km actuel</span><p class="font-semibold text-gray-100">{{ $detailVehicle->kilometrage_actuel }}</p></div>
                            <div><span class="text-gray-500 text-xs">Heures moteur</span><p class="font-semibold text-gray-100">{{ $detailVehicle->heures_moteur }}</p></div>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-lg p-4 border border-gray-700" x-show="tab === 'fin'" x-cloak>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                            <div><span class="text-gray-500 text-xs">Prix d'achat</span><p class="font-semibold text-gray-100">{{ $detailVehicle->prix_achat }}</p></div>
                            <div><span class="text-gray-500 text-xs">Fournisseur</span><p class="font-semibold text-gray-100">{{ $detailVehicle->fournisseur_nom }}</p></div>
                            <div><span class="text-gray-500 text-xs">ICE</span><p class="font-semibold text-gray-100">{{ $detailVehicle->fournisseur_ice }}</p></div>
                            <div><span class="text-gray-500 text-xs">Bon de commande</span><p class="font-semibold text-gray-100">{{ $detailVehicle->bon_commande_ref }}</p></div>
                            <div><span class="text-gray-500 text-xs">Article budgétaire</span><p class="font-semibold text-gray-100">{{ $detailVehicle->article_budgetaire }}</p></div>
                            <div><span class="text-gray-500 text-xs">Durée amortissement</span><p class="font-semibold text-gray-100">{{ $detailVehicle->duree_amortissement }}</p></div>
                            <div><span class="text-gray-500 text-xs">Mode amortissement</span><p class="font-semibold text-gray-100">{{ $detailVehicle->mode_amortissement }}</p></div>
                            <div><span class="text-gray-500 text-xs">Valeur nette comptable</span><p class="font-semibold text-gray-100">{{ $detailVehicle->valeur_nette_comptable }}</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
