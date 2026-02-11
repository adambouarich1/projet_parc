<div class="p-6 text-gray-100">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Gestion des Véhicules</h1>
            <p class="text-gray-400 text-sm">{{ $vehicles->total() }} véhicule(s) au total</p>
        </div>
            @if(auth()->user()->canEdit())
                <button wire:click="openCreate" class="mt-4 md:mt-0 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Ajouter un véhicule
                </button>
            @endif
    </div>

    {{-- Messages Flash --}}
    @if (session()->has('status'))
        <div class="bg-green-500/10 border border-green-500 text-green-500 px-4 py-2 rounded mb-4">
            {{ session('status') }}
        </div>
    @endif

    {{-- Filtres et Recherche --}}
    <div class="bg-gray-800 p-4 rounded-xl mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Barre de recherche --}}
        <div class="relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Rechercher par immatriculation, marque..."
                class="w-full bg-gray-700 text-white border-none rounded-lg pl-10 py-2 focus:ring-2 focus:ring-purple-500"
            >
            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>

        {{-- Filtre Catégorie --}}
        <select wire:model.live="filters.categorie_vehicule" class="bg-gray-700 text-white border-none rounded-lg py-2 focus:ring-2 focus:ring-purple-500">
            <option value="">Toutes catégories</option>
            @foreach($categories as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>

        {{-- Filtre Carburant --}}
        <select wire:model.live="filters.carburant" class="bg-gray-700 text-white border-none rounded-lg py-2 focus:ring-2 focus:ring-purple-500">
            <option value="">Tous carburants</option>
            @foreach($carburants as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>

        {{-- Filtre Statut --}}
        <select wire:model.live="filters.statut_actuel" class="bg-gray-700 text-white border-none rounded-lg py-2 focus:ring-2 focus:ring-purple-500">
            <option value="">Tous statuts</option>
            @foreach($statuts as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- Grille des véhicules --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($vehicles as $vehicle)
            @php
                $statutColors = [
                    'En service' => 'bg-green-900/80 text-green-200 border-green-700',
                    'En réparation' => 'bg-red-900/80 text-red-200 border-red-700',
                    'Immobile' => 'bg-yellow-900/80 text-yellow-200 border-yellow-700',
                    'Hors service' => 'bg-gray-900/80 text-gray-200 border-gray-700',
                    'Réformé' => 'bg-purple-900/80 text-purple-200 border-purple-700',
                ];
                $statutClass = $statutColors[$vehicle->statut_actuel] ?? 'bg-gray-900/80 text-white border-gray-600';
            @endphp
            <div wire:key="vehicle-{{ $vehicle->id }}" class="bg-gray-800 rounded-xl overflow-hidden shadow-lg border border-gray-700 hover:border-purple-500 transition group">
                {{-- Image --}}
                <div class="h-40 bg-gray-700 relative">
                    @if($vehicle->image_path)
                        <img src="{{ Storage::url($vehicle->image_path) }}" alt="{{ $vehicle->marque }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex flex-col items-center justify-center h-full bg-gradient-to-br from-gray-700 to-gray-800 p-4">
                            <h3 class="text-2xl font-bold text-white mb-1">{{ $vehicle->marque }}</h3>
                            <span class="text-purple-300 font-mono text-sm bg-purple-900/30 px-2 py-0.5 rounded">{{ $vehicle->immatriculation }}</span>
                        </div>
                    @endif
                    <div class="absolute top-2 right-2">
                        <span class="px-2 py-1 text-xs rounded-full border {{ $statutClass }}">
                            {{ $vehicle->statut_actuel }}
                        </span>
                    </div>
                </div>

                {{-- Contenu Carte --}}
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="font-bold text-lg text-white">{{ $vehicle->marque }} {{ $vehicle->modele }}</h3>
                            <p class="text-purple-400 text-sm font-mono">{{ $vehicle->immatriculation }}</p>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm text-gray-400 mt-4">
                        <div class="flex justify-between">
                            <span>Kilométrage</span>
                            <span class="text-white">{{ number_format($vehicle->kilometrage_actuel, 0, ',', ' ') }} km</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Carburant</span>
                            <span class="text-white">{{ $vehicle->carburant }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Catégorie</span>
                            <span class="text-white">{{ $vehicle->categorie_vehicule }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 pt-4 border-t border-gray-700 flex justify-between items-center">
                        <button wire:click="openDetails({{ $vehicle->id }})" class="text-sm text-purple-400 hover:text-purple-300">Voir détails</button>
                            @if(auth()->user()->canEdit())
                                <div class="flex space-x-2">
                                    <button wire:click="openEdit({{ $vehicle->id }})" class="p-1 text-gray-400 hover:text-white transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                        <button
                                            @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { 
                                                detail: { 
                                                    callback: () => $wire.deleteVehicle({{ $vehicle->id }}) 
                                                } 
                                            }))"
                                            type="button"
                                            class="p-1 text-gray-400 hover:text-red-500 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                </div>
                            @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                Aucun véhicule trouvé.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $vehicles->links() }}
    </div>

    {{-- MODALE : Création / Edition --}}
    @if($showFormModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showFormModal', false)"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-700">
                <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg leading-6 font-medium text-white" id="modal-title">
                            {{ $editingId ? 'Modifier le véhicule' : 'Ajouter un véhicule' }}
                        </h3>
                        <button wire:click="$set('showFormModal', false)" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Immatriculation --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Immatriculation *</label>
                                <input type="text" wire:model="form.immatriculation" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                @error('form.immatriculation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Marque --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Marque *</label>
                                <input type="text" wire:model="form.marque" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                @error('form.marque') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Modèle --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Modèle *</label>
                                <input type="text" wire:model="form.modele" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                @error('form.modele') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Catégorie --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Catégorie *</label>
                                <select wire:model="form.categorie_vehicule" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach($categories as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('form.categorie_vehicule') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Carburant (CORRIGÉ) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Carburant *</label>
                                <select wire:model="form.carburant" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach($carburants as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('form.carburant') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Kilométrage --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Kilométrage Actuel</label>
                                <input type="number" wire:model="form.kilometrage_actuel" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                @error('form.kilometrage_actuel') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Date Mise en Circulation --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Date mise en circulation</label>
                                <input type="date" wire:model="form.date_mise_circulation" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                @error('form.date_mise_circulation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Statut --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Statut</label>
                                <select wire:model="form.statut_actuel" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                    @foreach($statuts as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('form.statut_actuel') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Image --}}
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-400 mb-1">Photo du véhicule</label>
                                <input type="file" wire:model="image" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                                <div wire:loading wire:target="image" class="text-xs text-purple-400 mt-1">Chargement de l'image...</div>
                                @error('image') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" wire:click="$set('showFormModal', false)" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Annuler</button>
                            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                                {{ $editingId ? 'Mettre à jour' : 'Enregistrer' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODALE : Détails (Simplifiée) --}}
    @if($showDetailsModal && $detailVehicle)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" wire:click="$set('showDetailsModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-700">
                <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                    <h3 class="text-xl font-bold text-white mb-4">{{ $detailVehicle->marque }} {{ $detailVehicle->modele }}</h3>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="text-gray-400">Immatriculation:</div>
                        <div class="text-white">{{ $detailVehicle->immatriculation }}</div>

                        <div class="text-gray-400">VIN:</div>
                        <div class="text-white">{{ $detailVehicle->vin ?? 'N/A' }}</div>

                        <div class="text-gray-400">Statut:</div>
                        <div class="text-white">
                             <span class="px-2 py-0.5 rounded-full bg-purple-900 text-purple-200 border border-purple-700">
                                {{ $detailVehicle->statut_actuel }}
                             </span>
                        </div>

                        {{-- Ajoutez d'autres champs ici selon besoin --}}
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="button" wire:click="$set('showDetailsModal', false)" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>