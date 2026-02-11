<div class="p-6 text-gray-100">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Gestion des Chauffeurs</h1>
            <p class="text-gray-400 text-sm">{{ $drivers->total() }} chauffeur(s) au total</p>
        </div>
            @if(auth()->user()->canEdit())
                <button wire:click="openCreate" class="mt-4 md:mt-0 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Ajouter un chauffeur
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
        <div class="relative md:col-span-2">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Rechercher par nom, prénom, matricule..." 
                class="w-full bg-gray-700 text-white border-none rounded-lg pl-10 py-2 focus:ring-2 focus:ring-purple-500"
            >
            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>

        {{-- Filtre Statut --}}
        <select wire:model.live="filters.statut_actuel" class="bg-gray-700 text-white border-none rounded-lg py-2 focus:ring-2 focus:ring-purple-500">
            <option value="">Tous statuts</option>
            @foreach($statuts as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>

        {{-- Filtre Service (Input simple car pas de liste fixe fournie) --}}
        <input 
            type="text" 
            wire:model.live.debounce.300ms="filters.service_affecte"
            placeholder="Filtrer par service..."
            class="bg-gray-700 text-white border-none rounded-lg py-2 focus:ring-2 focus:ring-purple-500"
        >
    </div>

    {{-- Grille des chauffeurs --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($drivers as $driver)
            @php
                $statutColors = [
                    'Disponible' => 'bg-green-900/80 text-green-200 border-green-700',
                    'En congé' => 'bg-blue-900/80 text-blue-200 border-blue-700',
                    'Maladie' => 'bg-red-900/80 text-red-200 border-red-700',
                    'Non disponible' => 'bg-gray-900/80 text-gray-200 border-gray-700',
                ];
                $statutClass = $statutColors[$driver->statut_actuel] ?? 'bg-gray-900/80 text-white border-gray-600';
            @endphp
            
            <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg border border-gray-700 hover:border-purple-500 transition group flex flex-col">
                {{-- Header Carte : Avatar & Statut --}}
                <div class="p-6 flex flex-col items-center border-b border-gray-700 bg-gradient-to-b from-gray-800 to-gray-800/50">
                    <div class="relative mb-3">
                        @if($driver->photo_path)
                            <img src="{{ Storage::url($driver->photo_path) }}" alt="{{ $driver->nom }}" class="w-24 h-24 rounded-full object-cover border-4 border-gray-700 shadow-lg">
                        @else
                            <div class="w-24 h-24 rounded-full bg-purple-900/50 border-4 border-gray-700 flex items-center justify-center text-purple-200 text-2xl font-bold shadow-lg">
                                {{ substr($driver->prenom, 0, 1) }}{{ substr($driver->nom, 0, 1) }}
                            </div>
                        @endif
                        <div class="absolute bottom-0 right-0">
                            <span class="block w-4 h-4 rounded-full border-2 border-gray-800 {{ $driver->statut_actuel === 'Disponible' ? 'bg-green-500' : ($driver->statut_actuel === 'Maladie' ? 'bg-red-500' : 'bg-gray-500') }}"></span>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-bold text-white text-center">{{ $driver->prenom }} {{ $driver->nom }}</h3>
                    <p class="text-purple-400 text-sm font-mono mt-1">{{ $driver->matricule }}</p>
                    
                    <div class="mt-3">
                        <span class="px-3 py-1 text-xs rounded-full border {{ $statutClass }}">
                            {{ $driver->statut_actuel }}
                        </span>
                    </div>
                </div>

                {{-- Corps Carte : Infos --}}
                <div class="p-4 flex-1 space-y-3">
                    <div class="flex items-center text-sm text-gray-400">
                        <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <span class="truncate">{{ $driver->telephone ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-400">
                        <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span class="truncate">{{ $driver->service_affecte ?? 'Aucun service' }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-400">
                        <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                        <span>Permis: <span class="text-white font-medium">{{ $driver->categories ?? 'N/A' }}</span></span>
                    </div>
                </div>

                {{-- Footer Carte : Actions --}}
                <div class="px-4 py-3 border-t border-gray-700 flex justify-between items-center bg-gray-900/30">
                    <button wire:click="openDetails({{ $driver->id }})" class="text-xs text-purple-400 hover:text-purple-300 font-medium uppercase tracking-wide">Fiche complète</button>
                        @if(auth()->user()->canEdit())
                            <div class="flex space-x-2">
                                <button wire:click="openEdit({{ $driver->id }})" class="p-1 text-gray-400 hover:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <button 
                                    wire:click="deleteDriver({{ $driver->id }})"
                                    wire:confirm="Êtes-vous sûr de vouloir supprimer ce chauffeur ?"
                                    class="p-1 text-gray-400 hover:text-red-500 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500 bg-gray-800 rounded-xl border border-gray-700 border-dashed">
                <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Aucun chauffeur trouvé.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $drivers->links() }}
    </div>

    {{-- MODALE : Création / Edition --}}
    @if($showFormModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" wire:click="$set('showFormModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-700">
                <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl leading-6 font-bold text-white" id="modal-title">
                            {{ $editingId ? 'Modifier le chauffeur' : 'Nouveau chauffeur' }}
                        </h3>
                        <button wire:click="$set('showFormModal', false)" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="save">
                        <h4 class="text-purple-400 text-sm font-semibold uppercase tracking-wider mb-4 border-b border-gray-700 pb-2">Identité</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Prénom *</label>
                                <input type="text" wire:model="form.prenom" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                @error('form.prenom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Nom *</label>
                                <input type="text" wire:model="form.nom" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                @error('form.nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Matricule *</label>
                                <input type="text" wire:model="form.matricule" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                @error('form.matricule') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">CIN</label>
                                <input type="text" wire:model="form.cin" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                @error('form.cin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Date Naissance</label>
                                <input type="date" wire:model="form.date_naissance" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Statut *</label>
                                <select wire:model="form.statut_actuel" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                    @foreach($statuts as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <h4 class="text-purple-400 text-sm font-semibold uppercase tracking-wider mb-4 border-b border-gray-700 pb-2">Infos Professionnelles</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Téléphone</label>
                                <input type="text" wire:model="form.telephone" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Email Pro</label>
                                <input type="email" wire:model="form.email_pro" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Service Affecté</label>
                                <input type="text" wire:model="form.service_affecte" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Poste Occupé</label>
                                <input type="text" wire:model="form.poste_occupe" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>

                        <h4 class="text-purple-400 text-sm font-semibold uppercase tracking-wider mb-4 border-b border-gray-700 pb-2">Permis de conduire</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Numéro Permis</label>
                                <input type="text" wire:model="form.num_permis" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Catégorie(s)</label>
                                <select wire:model="form.categories" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">Sélectionner</option>
                                    @foreach($categoriesPermis as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Actuel : {{ $form['categories'] }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1">Délivrance</label>
                                    <input type="date" wire:model="form.date_delivrance" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1">Expiration</label>
                                    <input type="date" wire:model="form.date_expiration" class="w-full bg-gray-700 text-white border-gray-600 rounded-lg text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-900/50 p-4 rounded-lg">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Photo Profil</label>
                                <input type="file" wire:model="photo" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                                @error('photo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Scan Permis</label>
                                <input type="file" wire:model="scan_permis" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
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

    {{-- MODALE : Détails --}}
    @if($showDetailsModal && $detailDriver)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" wire:click="$set('showDetailsModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-700">
                <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex items-center gap-4 mb-6 border-b border-gray-700 pb-4">
                        @if($detailDriver->photo_path)
                            <img src="{{ Storage::url($detailDriver->photo_path) }}" class="w-16 h-16 rounded-full object-cover">
                        @else
                            <div class="w-16 h-16 rounded-full bg-purple-600 flex items-center justify-center text-white text-xl font-bold">
                                {{ substr($detailDriver->prenom, 0, 1) }}{{ substr($detailDriver->nom, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="text-2xl font-bold text-white">{{ $detailDriver->prenom }} {{ $detailDriver->nom }}</h3>
                            <p class="text-purple-400">{{ $detailDriver->poste_occupe }} - {{ $detailDriver->matricule }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-y-4 gap-x-8 text-sm">
                        <div><span class="text-gray-400 block">Téléphone</span> <span class="text-white">{{ $detailDriver->telephone }}</span></div>
                        <div><span class="text-gray-400 block">Email</span> <span class="text-white">{{ $detailDriver->email_pro }}</span></div>
                        <div><span class="text-gray-400 block">CIN</span> <span class="text-white">{{ $detailDriver->cin }}</span></div>
                        <div><span class="text-gray-400 block">Service</span> <span class="text-white">{{ $detailDriver->service_affecte }}</span></div>
                        
                        <div class="col-span-2 border-t border-gray-700 pt-3 mt-1">
                            <span class="text-purple-400 font-bold block mb-2">Permis de conduire</span>
                        </div>
                        <div><span class="text-gray-400 block">Numéro</span> <span class="text-white">{{ $detailDriver->num_permis }}</span></div>
                        <div><span class="text-gray-400 block">Catégories</span> <span class="text-white bg-gray-700 px-2 py-0.5 rounded">{{ $detailDriver->categories }}</span></div>
                        <div><span class="text-gray-400 block">Expiration</span> <span class="text-white">{{ optional($detailDriver->date_expiration)->format('d/m/Y') }}</span></div>
                        
                        @if($detailDriver->scan_permis_path)
                        <div class="col-span-2 mt-2">
                             <a href="{{ Storage::url($detailDriver->scan_permis_path) }}" target="_blank" class="text-purple-400 hover:underline flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Voir le scan du permis
                             </a>
                        </div>
                        @endif
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