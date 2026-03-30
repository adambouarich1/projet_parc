<div class="p-6 text-gray-900">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des Chauffeurs</h1>
            <p class="text-gray-700 text-sm">{{ $drivers->total() }} chauffeur(s) au total</p>
        </div>
            @if(auth()->user()->canEdit())
                <button wire:click="openCreate" class="mt-4 md:mt-0 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Ajouter un chauffeur
                </button>
            @endif
    </div>

    {{-- Messages Flash --}}
    @if (session()->has('status'))
        <div class="bg-green-50 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('status') }}
        </div>
    @endif

    {{-- Filtres et Recherche --}}
    <div class="bg-gray-100 p-4 rounded-xl mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Barre de recherche --}}
        <div class="relative md:col-span-2">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Rechercher par nom, prénom, matricule..."
                class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg pl-10 py-2 focus:ring-2 focus:ring-green-500"
            >
            <svg class="w-5 h-5 text-gray-700 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>

        {{-- Filtre Statut --}}
        <select wire:model.live="filters.statut_actuel" class="bg-white text-gray-900 border border-gray-300 rounded-lg py-2 focus:ring-2 focus:ring-green-500">
            <option value="">Tous statuts</option>
            @foreach($statuts as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>

        {{-- Filtre Service --}}
        <input
            type="text"
            wire:model.live.debounce.300ms="filters.service_affecte"
            placeholder="Filtrer par service..."
            class="bg-white text-gray-900 border border-gray-300 rounded-lg py-2 px-3 focus:ring-2 focus:ring-green-500"
        >
    </div>

    {{-- Grille des chauffeurs --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($drivers as $driver)
            @php
                $statutColors = [
                    'Disponible'     => 'bg-green-50 text-green-700 border-green-200',
                    'En mission'     => 'bg-blue-50 text-blue-700 border-blue-200',
                    'En congé'       => 'bg-sky-50 text-sky-700 border-sky-200',
                    'Maladie'        => 'bg-red-50 text-red-700 border-red-200',
                    'Non disponible' => 'bg-gray-100 text-gray-800 border-gray-300',
                ];
                $statutClass = $statutColors[$driver->statut_actuel] ?? 'bg-gray-100 text-gray-800 border-gray-300';
            @endphp

            <div wire:key="driver-{{ $driver->id }}" class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-200 hover:border-green-400 transition group flex flex-col">
                {{-- Header Carte : Avatar & Statut --}}
                <div class="p-6 flex flex-col items-center border-b border-gray-200 bg-gradient-to-b from-gray-50 to-white">
                    <div class="relative mb-3">
                        @if($driver->photo_path)
                            <img src="{{ Storage::url($driver->photo_path) }}" alt="{{ $driver->nom }}" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
                        @else
                            <div class="w-24 h-24 rounded-full border-4 border-white flex items-center justify-center text-white text-2xl font-bold shadow-md" style="background-color: #006233;">
                                {{ substr($driver->prenom, 0, 1) }}{{ substr($driver->nom, 0, 1) }}
                            </div>
                        @endif
                        <div class="absolute bottom-0 right-0">
                            <span class="block w-4 h-4 rounded-full border-2 border-white {{ $driver->statut_actuel === 'Disponible' ? 'bg-green-500' : ($driver->statut_actuel === 'Maladie' ? 'bg-red-500' : 'bg-gray-400') }}"></span>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold text-gray-900 text-center">{{ $driver->prenom }} {{ $driver->nom }}</h3>
                    <p class="text-green-700 text-sm font-mono mt-1">{{ $driver->matricule }}</p>

                    <div class="mt-3">
                        <span class="px-3 py-1 text-xs rounded-full border {{ $statutClass }}">
                            {{ $driver->statut_actuel }}
                        </span>
                    </div>
                </div>

                {{-- Corps Carte : Infos --}}
                <div class="p-4 flex-1 space-y-3">
                    <div class="flex items-center text-sm text-gray-700">
                        <svg class="w-4 h-4 mr-2 text-green-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <span class="truncate">{{ $driver->telephone ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-700">
                        <svg class="w-4 h-4 mr-2 text-green-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span class="truncate">{{ $driver->service_affecte ?? 'Aucun service' }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-700">
                        <svg class="w-4 h-4 mr-2 text-green-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                        <span>Permis: <span class="text-gray-900 font-medium">{{ $driver->categories ?? 'N/A' }}</span></span>
                    </div>
                </div>

                {{-- Footer Carte : Actions --}}
                <div class="px-4 py-3 border-t border-gray-200 flex justify-between items-center bg-gray-50">
                    <button wire:click="openDetails({{ $driver->id }})" class="text-xs text-green-700 hover:text-green-800 font-medium uppercase tracking-wide">Fiche complète</button>
                        @if(auth()->user()->canEdit())
                            <div class="flex space-x-2">
                                <button wire:click="openEdit({{ $driver->id }})" class="p-1 text-gray-800 hover:text-gray-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                    <button
                                        @click="window.dispatchEvent(new CustomEvent('delete-confirmation', {
                                            detail: {
                                                callback: () => $wire.deleteDriver({{ $driver->id }})
                                            }
                                        }))"
                                        type="button"
                                        class="p-1 text-gray-800 hover:text-red-600 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                            </div>
                        @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-800 bg-white rounded-xl border border-gray-200 border-dashed">
                <svg class="w-12 h-12 mx-auto text-gray-800 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
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

            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-200">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-6 pb-4 border-b-2" style="border-bottom-color: #006233;">
                        <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                            {{ $editingId ? 'Modifier le chauffeur' : 'Nouveau chauffeur' }}
                        </h3>
                        <button wire:click="$set('showFormModal', false)" class="text-gray-800 hover:text-gray-800">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save">
                        <h4 class="text-green-700 text-sm font-semibold uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Identité</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Prénom *</label>
                                <input type="text" wire:model="form.prenom" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                                @error('form.prenom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Nom *</label>
                                <input type="text" wire:model="form.nom" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                                @error('form.nom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Matricule *</label>
                                <input type="text" wire:model="form.matricule" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                                @error('form.matricule') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">CIN</label>
                                <input type="text" wire:model="form.cin" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                                @error('form.cin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Date Naissance</label>
                                <input type="date" wire:model="form.date_naissance" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Statut *</label>
                                <select wire:model="form.statut_actuel" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                                    @foreach($statuts as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <h4 class="text-green-700 text-sm font-semibold uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Infos Professionnelles</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Téléphone</label>
                                <input type="text" wire:model="form.telephone" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Email Pro</label>
                                <input type="email" wire:model="form.email_pro" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Service Affecté</label>
                                <input type="text" wire:model="form.service_affecte" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Poste Occupé</label>
                                <input type="text" wire:model="form.poste_occupe" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>

                        <h4 class="text-green-700 text-sm font-semibold uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Permis de conduire</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Numéro Permis</label>
                                <input type="text" wire:model="form.num_permis" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Catégorie(s)</label>
                                <select wire:model="form.categories" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                                    <option value="">Sélectionner</option>
                                    @foreach($categoriesPermis as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-800 mt-1">Actuel : {{ $form['categories'] }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-800 mb-1">Délivrance</label>
                                    <input type="date" wire:model="form.date_delivrance" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-800 mb-1">Expiration</label>
                                    <input type="date" wire:model="form.date_expiration" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Photo Profil</label>
                                <input type="file" wire:model="photo" class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:text-white hover:file:bg-green-800" style="--tw-file-bg: #006233;" onchange="">
                                <style>.file\:bg-green-700::file-selector-button { background-color: #006233; }</style>
                                @error('photo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Scan Permis</label>
                                <input type="file" wire:model="scan_permis" class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-700 file:text-white hover:file:bg-green-800">
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" wire:click="$set('showFormModal', false)" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">Annuler</button>
                            <button type="submit" class="text-white px-4 py-2 rounded-lg hover:bg-green-800 transition" style="background-color: #006233;">
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

            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex items-center gap-4 mb-6 border-b border-gray-200 pb-4">
                        @if($detailDriver->photo_path)
                            <img src="{{ Storage::url($detailDriver->photo_path) }}" class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                        @else
                            <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-xl font-bold" style="background-color: #006233;">
                                {{ substr($detailDriver->prenom, 0, 1) }}{{ substr($detailDriver->nom, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $detailDriver->prenom }} {{ $detailDriver->nom }}</h3>
                            <p class="text-green-700">{{ $detailDriver->poste_occupe }} - {{ $detailDriver->matricule }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-y-4 gap-x-8 text-sm">
                        <div><span class="text-gray-700 block">Téléphone</span> <span class="text-gray-900 font-medium">{{ $detailDriver->telephone }}</span></div>
                        <div><span class="text-gray-700 block">Email</span> <span class="text-gray-900 font-medium">{{ $detailDriver->email_pro }}</span></div>
                        <div><span class="text-gray-700 block">CIN</span> <span class="text-gray-900 font-medium">{{ $detailDriver->cin }}</span></div>
                        <div><span class="text-gray-700 block">Service</span> <span class="text-gray-900 font-medium">{{ $detailDriver->service_affecte }}</span></div>

                        <div class="col-span-2 border-t border-gray-200 pt-3 mt-1">
                            <span class="text-green-700 font-bold block mb-2">Permis de conduire</span>
                        </div>
                        <div><span class="text-gray-700 block">Numéro</span> <span class="text-gray-900 font-medium">{{ $detailDriver->num_permis }}</span></div>
                        <div><span class="text-gray-700 block">Catégories</span> <span class="text-gray-900 bg-gray-100 px-2 py-0.5 rounded font-medium">{{ $detailDriver->categories }}</span></div>
                        <div><span class="text-gray-700 block">Expiration</span> <span class="text-gray-900 font-medium">{{ optional($detailDriver->date_expiration)->format('d/m/Y') }}</span></div>

                        @if($detailDriver->scan_permis_path)
                        <div class="col-span-2 mt-2">
                             <a href="{{ Storage::url($detailDriver->scan_permis_path) }}" target="_blank" class="text-green-700 hover:underline flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Voir le scan du permis
                             </a>
                        </div>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="button" wire:click="$set('showDetailsModal', false)" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
