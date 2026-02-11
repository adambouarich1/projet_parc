<?php
    $statutColors = [
        'brouillon' => 'bg-gray-500 text-white',
        'en_attente' => 'bg-amber-500 text-white',
        'valide' => 'bg-blue-500 text-white',
        'rejete' => 'bg-rose-500 text-white',
        'en_cours' => 'bg-indigo-500 text-white',
        'cloture' => 'bg-emerald-500 text-white',
        'annule' => 'bg-gray-700 text-gray-300',
    ];

    $roleBadges = [
        'admin' => 'bg-rose-500 text-white',
        'responsable_parc' => 'bg-indigo-500 text-white',
        'valideur' => 'bg-amber-500 text-white',
        'agent_saisie' => 'bg-emerald-500 text-white',
        'consultation' => 'bg-gray-500 text-white',
    ];
?>

<div class="space-y-4 text-gray-100">
    {{-- Badge du rôle --}}
    <div class="flex justify-end">
        <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $roleBadges[auth()->user()->role] ?? 'bg-gray-500 text-white' }}">
            {{ auth()->user()->role_label }}
        </span>
    </div>

    {{-- Messages flash --}}
    @if (session()->has('status'))
        <div class="rounded-md bg-emerald-900/40 border border-emerald-700 text-emerald-100 px-4 py-3">
            {{ session('status') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="rounded-md bg-rose-900/40 border border-rose-700 text-rose-100 px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-4">
        {{-- Sidebar filtres --}}
        <div class="lg:w-1/4 space-y-4">
            <div class="bg-gray-900 shadow rounded-lg p-4 space-y-3 border border-gray-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-100">Filtres</h3>
                    <button type="button" wire:click="$set('filters', { statut: '', search: '' })" class="text-xs text-gray-400 hover:text-gray-200">
                        Réinitialiser
                    </button>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Recherche</label>
                    <input type="text" wire:model.live.debounce.300ms="filters.search" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Réf, objet, destination...">
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Statut</label>
                    <select wire:model.live="filters.statut" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Tous</option>
                        @foreach ($statuts as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if(auth()->user()->canEdit())
            <button type="button" wire:click="openCreate" class="w-full inline-flex items-center justify-center px-4 py-3 bg-indigo-500 text-white rounded-md shadow hover:bg-indigo-600 transition">
                + Nouvel ordre de mission
            </button>
            @endif
        </div>

        {{-- Liste des missions --}}
        <div class="flex-1 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-100">Ordres de Mission</h3>
                @if(auth()->user()->canEdit())
                <div class="hidden lg:block">
                    <button type="button" wire:click="openCreate" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 transition">
                        + Nouvel OM
                    </button>
                </div>
                @endif
            </div>

            {{-- Tableau --}}
            <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-800 text-gray-300 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">Référence</th>
                                <th class="px-4 py-3 text-left">Objet</th>
                                <th class="px-4 py-3 text-left">Véhicule</th>
                                <th class="px-4 py-3 text-left">Conducteur</th>
                                <th class="px-4 py-3 text-left">Dates</th>
                                <th class="px-4 py-3 text-left">Statut</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse ($missions as $mission)
                                <tr class="hover:bg-gray-800/50" wire:key="mission-{{ $mission->id }}">
                                    <td class="px-4 py-3 font-mono text-indigo-400">{{ $mission->reference }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-100">{{ Str::limit($mission->objet, 30) }}</div>
                                        <div class="text-xs text-gray-400">{{ $mission->destination }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-300">
                                        {{ $mission->vehicle->marque }} {{ $mission->vehicle->modele }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-300">
                                        {{ $mission->driver->prenom }} {{ $mission->driver->nom }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-400">
                                        <div>Dép: {{ $mission->date_depart->format('d/m/Y H:i') }}</div>
                                        <div>Ret: {{ $mission->date_retour_prevue->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $statutColors[$mission->statut] ?? 'bg-gray-500 text-white' }}">
                                            {{ $mission->statut_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            {{-- Bouton détails --}}
                                            <button wire:click="openDetails({{ $mission->id }})" class="p-1.5 text-gray-400 hover:text-gray-200 hover:bg-gray-700 rounded" title="Détails">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                            @if(auth()->user()->canEdit() && in_array($mission->statut, ['cloture', 'rejete', 'annule']))
                                                <button
                                                    @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { 
                                                        detail: { 
                                                            callback: () => $wire.archive({{ $mission->id }}) 
                                                        } 
                                                    }))"
                                                    type="button"
                                                    class="text-gray-400 hover:text-gray-300 text-sm" 
                                                    title="Archiver">
                                                    📦
                                                </button>
                                            @endif

                                            @if(auth()->user()->canEdit())
                                                {{-- Brouillon: Modifier, Soumettre, Supprimer --}}
                                                @if($mission->statut === 'brouillon')
                                                    <button wire:click="openEdit({{ $mission->id }})" class="p-1.5 text-indigo-400 hover:text-indigo-300 hover:bg-indigo-900/40 rounded" title="Modifier">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </button>
                                                    <button wire:click="submit({{ $mission->id }})" class="p-1.5 text-amber-400 hover:text-amber-300 hover:bg-amber-900/40 rounded" title="Soumettre">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                                    <button
                                                        @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { 
                                                            detail: { 
                                                                callback: () => $wire.delete({{ $mission->id }}) 
                                                            } 
                                                        }))"
                                                        type="button"
                                                        class="p-1.5 text-rose-400 hover:text-rose-300 hover:bg-rose-900/40 rounded" 
                                                        title="Supprimer">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                @endif

                                                {{-- En attente: Valider, Rejeter (valideurs seulement) --}}
                                                @if($mission->statut === 'en_attente' && auth()->user()->canValidate())
                                                    <button wire:click="validate_mission({{ $mission->id }})" class="p-1.5 text-emerald-400 hover:text-emerald-300 hover:bg-emerald-900/40 rounded" title="Valider">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    </button>
                                                    <button wire:click="openReject({{ $mission->id }})" class="p-1.5 text-rose-400 hover:text-rose-300 hover:bg-rose-900/40 rounded" title="Rejeter">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                @endif

                                                {{-- Validé: Démarrer --}}
                                                @if($mission->statut === 'valide')
                                                    <button wire:click="start({{ $mission->id }})" class="p-1.5 text-blue-400 hover:text-blue-300 hover:bg-blue-900/40 rounded" title="Démarrer">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </button>
                                                @endif

                                                {{-- En cours: Clôturer --}}
                                                @if($mission->statut === 'en_cours')
                                                    <button wire:click="closeModal({{ $mission->id }})" class="p-1.5 text-emerald-400 hover:text-emerald-300 hover:bg-emerald-900/40 rounded" title="Clôturer">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </button>
                                                @endif

                                                {{-- Annuler (sauf en_cours et cloture) --}}
                                                @if(!in_array($mission->statut, ['en_cours', 'cloture', 'annule']))
                                                <button
                                                    @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { 
                                                        detail: { 
                                                            callback: () => $wire.cancel({{ $mission->id }}) 
                                                        } 
                                                    }))"
                                                    type="button"
                                                    class="p-1.5 text-gray-400 hover:text-gray-300 hover:bg-gray-700 rounded" 
                                                    title="Annuler">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                        Aucun ordre de mission trouvé.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>{{ $missions->links() }}</div>
        </div>
    </div>

    {{-- Modal Formulaire --}}
    @if($showFormModal)
    <div class="fixed inset-0 bg-black/60 z-40 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-xl shadow-2xl max-w-2xl w-full border border-gray-800">
            <div class="flex items-center justify-between p-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold text-gray-100">
                    {{ $editingId ? 'Modifier l\'ordre de mission' : 'Nouvel ordre de mission' }}
                </h3>
                <button wire:click="$set('showFormModal', false)" class="text-gray-400 hover:text-gray-200">✕</button>
            </div>
            <form wire:submit.prevent="save" class="p-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Véhicule *</label>
                        <select wire:model="form.vehicle_id" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            <option value="">Sélectionner un véhicule</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}">{{ $v->immatriculation }} - {{ $v->marque }} {{ $v->modele }}</option>
                            @endforeach
                        </select>
                        @error('form.vehicle_id') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Conducteur *</label>
                        <select wire:model="form.driver_id" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            <option value="">Sélectionner un conducteur</option>
                            @foreach($drivers as $d)
                                <option value="{{ $d->id }}">{{ $d->prenom }} {{ $d->nom }} ({{ $d->categories }})</option>
                            @endforeach
                        </select>
                        @error('form.driver_id') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Objet de la mission *</label>
                    <input type="text" wire:model="form.objet" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Ex: Réunion direction régionale">
                    @error('form.objet') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Description</label>
                    <textarea wire:model="form.description" rows="2" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Détails supplémentaires..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Lieu de départ *</label>
                        <input type="text" wire:model="form.lieu_depart" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        @error('form.lieu_depart') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Destination *</label>
                        <input type="text" wire:model="form.destination" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Ex: Rabat">
                        @error('form.destination') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Date/heure départ *</label>
                        <input type="datetime-local" wire:model="form.date_depart" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        @error('form.date_depart') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Date/heure retour prévue *</label>
                        <input type="datetime-local" wire:model="form.date_retour_prevue" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        @error('form.date_retour_prevue') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-800">
                    <button type="button" wire:click="$set('showFormModal', false)" class="px-4 py-2 rounded-md text-gray-200 bg-gray-800 hover:bg-gray-700 border border-gray-700">
                        Annuler
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-md bg-indigo-500 text-white hover:bg-indigo-600 shadow">
                        {{ $editingId ? 'Mettre à jour' : 'Créer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal Détails --}}
    @if($showDetailsModal && $detailMission)
    <div class="fixed inset-0 bg-black/60 z-40 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-xl shadow-2xl max-w-3xl w-full border border-gray-800 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-4 border-b border-gray-800">
                <div>
                    <h3 class="text-lg font-semibold text-gray-100">{{ $detailMission->reference }}</h3>
                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $statutColors[$detailMission->statut] ?? 'bg-gray-500' }}">
                        {{ $detailMission->statut_label }}
                    </span>
                </div>
                <button wire:click="$set('showDetailsModal', false)" class="text-gray-400 hover:text-gray-200">✕</button>
            </div>
            <div class="p-4 space-y-4">
                {{-- Infos mission --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Objet</p>
                        <p class="font-medium text-gray-100">{{ $detailMission->objet }}</p>
                        @if($detailMission->description)
                            <p class="text-sm text-gray-300 mt-1">{{ $detailMission->description }}</p>
                        @endif
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Trajet</p>
                        <p class="font-medium text-gray-100">{{ $detailMission->lieu_depart }} → {{ $detailMission->destination }}</p>
                    </div>
                </div>

                {{-- Véhicule et conducteur --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Véhicule</p>
                        <p class="font-medium text-gray-100">{{ $detailMission->vehicle->marque }} {{ $detailMission->vehicle->modele }}</p>
                        <p class="text-sm text-gray-300">{{ $detailMission->vehicle->immatriculation }}</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Conducteur</p>
                        <p class="font-medium text-gray-100">{{ $detailMission->driver->prenom }} {{ $detailMission->driver->nom }}</p>
                        <p class="text-sm text-gray-300">{{ $detailMission->driver->telephone }}</p>
                    </div>
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Départ prévu</p>
                        <p class="font-medium text-gray-100">{{ $detailMission->date_depart->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Retour prévu</p>
                        <p class="font-medium text-gray-100">{{ $detailMission->date_retour_prevue->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($detailMission->date_retour_effective)
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Retour effectif</p>
                        <p class="font-medium text-gray-100">{{ $detailMission->date_retour_effective->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>

                {{-- Kilométrage (si mission démarrée) --}}
                @if($detailMission->km_depart)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Km départ</p>
                        <p class="font-medium text-gray-100">{{ number_format($detailMission->km_depart, 0, ',', ' ') }} km</p>
                    </div>
                    @if($detailMission->km_retour)
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Km retour</p>
                        <p class="font-medium text-gray-100">{{ number_format($detailMission->km_retour, 0, ',', ' ') }} km</p>
                    </div>
                    <div class="bg-indigo-900/40 rounded-lg p-3 border border-indigo-700">
                        <p class="text-xs text-indigo-300">Distance parcourue</p>
                        <p class="font-bold text-indigo-100">{{ number_format($detailMission->km_parcourus, 0, ',', ' ') }} km</p>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Demandeur et valideur --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Demandé par</p>
                        <p class="font-medium text-gray-100">{{ $detailMission->user->name }}</p>
                        <p class="text-sm text-gray-300">{{ $detailMission->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($detailMission->validator)
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">{{ $detailMission->statut === 'rejete' ? 'Rejeté par' : 'Validé par' }}</p>
                        <p class="font-medium text-gray-100">{{ $detailMission->validator->name }}</p>
                        <p class="text-sm text-gray-300">{{ $detailMission->validated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>

                {{-- Motif de rejet --}}
                @if($detailMission->statut === 'rejete' && $detailMission->motif_rejet)
                <div class="bg-rose-900/30 rounded-lg p-3 border border-rose-700">
                    <p class="text-xs text-rose-300">Motif du rejet</p>
                    <p class="text-rose-100">{{ $detailMission->motif_rejet }}</p>
                </div>
                @endif

                {{-- Formulaire clôture (si en_cours) --}}
                @if($detailMission->statut === 'en_cours' && auth()->user()->canEdit())
                <div class="bg-emerald-900/30 rounded-lg p-4 border border-emerald-700 space-y-3">
                    <h4 class="text-sm font-semibold text-emerald-100">Clôturer la mission</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-xs text-emerald-300">Kilométrage retour *</label>
                            <input type="number" wire:model="form.km_retour" min="{{ $detailMission->km_depart }}" class="w-full rounded-md border-emerald-700 bg-gray-800 text-gray-100 text-sm focus:border-emerald-400 focus:ring-emerald-400">
                            @error('form.km_retour') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs text-emerald-300">Observations</label>
                            <input type="text" wire:model="form.observations" class="w-full rounded-md border-emerald-700 bg-gray-800 text-gray-100 text-sm focus:border-emerald-400 focus:ring-emerald-400" placeholder="RAS, incident...">
                        </div>
                    </div>
                    <button wire:click="close({{ $detailMission->id }})" class="px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700">
                        Clôturer la mission
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Rejet --}}
    @if($showRejectModal && $detailMission)
    <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-xl shadow-2xl max-w-md w-full border border-gray-800">
            <div class="flex items-center justify-between p-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold text-gray-100">Rejeter la mission</h3>
                <button wire:click="$set('showRejectModal', false)" class="text-gray-400 hover:text-gray-200">✕</button>
            </div>
            <div class="p-4 space-y-4">
                <p class="text-gray-300">Vous êtes sur le point de rejeter la mission <strong class="text-indigo-400">{{ $detailMission->reference }}</strong>.</p>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Motif du rejet * (min. 10 caractères)</label>
                    <textarea wire:model="motifRejet" rows="3" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-rose-400 focus:ring-rose-400" placeholder="Expliquez la raison du rejet..."></textarea>
                    @error('motifRejet') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-2">
                    <button wire:click="$set('showRejectModal', false)" class="px-4 py-2 rounded-md text-gray-200 bg-gray-800 hover:bg-gray-700 border border-gray-700">
                        Annuler
                    </button>
                    <button wire:click="reject" class="px-4 py-2 rounded-md bg-rose-600 text-white hover:bg-rose-700">
                        Confirmer le rejet
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>