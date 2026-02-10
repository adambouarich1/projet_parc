<?php
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

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Total Litres</p>
            <p class="text-2xl font-bold text-indigo-400">{{ number_format($stats['total_litres'], 0, ',', ' ') }} L</p>
        </div>
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Total Dépenses</p>
            <p class="text-2xl font-bold text-emerald-400">{{ number_format($stats['total_montant'], 2, ',', ' ') }} DH</p>
        </div>
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Nombre de Pleins</p>
            <p class="text-2xl font-bold text-amber-400">{{ $stats['nb_pleins'] }}</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-4">
        {{-- Sidebar filtres --}}
        <div class="lg:w-1/4 space-y-4">
            <div class="bg-gray-900 shadow rounded-lg p-4 space-y-3 border border-gray-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-100">Filtres</h3>
                    <button type="button" wire:click="$set('filters', { vehicle_id: '', date_from: '', date_to: '' })" class="text-xs text-gray-400 hover:text-gray-200">
                        Réinitialiser
                    </button>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Véhicule</label>
                    <select wire:model.live="filters.vehicle_id" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Tous</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}">{{ $v->immatriculation }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Date du</label>
                    <input type="date" wire:model.live="filters.date_from" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Date au</label>
                    <input type="date" wire:model.live="filters.date_to" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                </div>
            </div>

            @if(auth()->user()->canEdit())
            <button type="button" wire:click="openCreate" class="w-full inline-flex items-center justify-center px-4 py-3 bg-indigo-500 text-white rounded-md shadow hover:bg-indigo-600 transition">
                + Nouveau plein
            </button>
            @endif
        </div>

        {{-- Liste --}}
        <div class="flex-1 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-100">Entrées Carburant</h3>
                @if(auth()->user()->canEdit())
                <div class="hidden lg:block">
                    <button type="button" wire:click="openCreate" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 transition">
                        + Nouveau plein
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
                                <th class="px-4 py-3 text-left">Date</th>
                                <th class="px-4 py-3 text-left">Véhicule</th>
                                <th class="px-4 py-3 text-left">Conducteur</th>
                                <th class="px-4 py-3 text-right">Litres</th>
                                <th class="px-4 py-3 text-right">Montant</th>
                                <th class="px-4 py-3 text-right">Km</th>
                                <th class="px-4 py-3 text-right">L/100km</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse ($entries as $entry)
                                <tr class="hover:bg-gray-800/50" wire:key="entry-{{ $entry->id }}">
                                    <td class="px-4 py-3 text-gray-300">{{ $entry->date_plein->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-100">{{ $entry->vehicle->immatriculation }}</div>
                                        <div class="text-xs text-gray-400">{{ $entry->vehicle->marque }} {{ $entry->vehicle->modele }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-300">
                                        {{ $entry->driver ? $entry->driver->prenom . ' ' . $entry->driver->nom : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-indigo-400">{{ number_format($entry->quantite_litres, 2, ',', ' ') }} L</td>
                                    <td class="px-4 py-3 text-right font-medium text-emerald-400">{{ number_format($entry->montant_total, 2, ',', ' ') }} DH</td>
                                    <td class="px-4 py-3 text-right text-gray-300">{{ number_format($entry->kilometrage, 0, ',', ' ') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        @if($entry->consommation)
                                            <span class="@if($entry->consommation > 12) text-rose-400 @elseif($entry->consommation > 9) text-amber-400 @else text-emerald-400 @endif font-medium">
                                                {{ $entry->consommation }} L
                                            </span>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button wire:click="openDetails({{ $entry->id }})" class="p-1.5 text-gray-400 hover:text-gray-200 hover:bg-gray-700 rounded" title="Détails">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                            @if(auth()->user()->canEdit())
                                            <button wire:click="openEdit({{ $entry->id }})" class="p-1.5 text-indigo-400 hover:text-indigo-300 hover:bg-indigo-900/40 rounded" title="Modifier">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            @if(auth()->user()->canEdit())
                                            <button wire:click="archive({{ $entry->id }})" class="text-gray-400 hover:text-gray-300 text-sm" title="Archiver">
                                                📦
                                            </button>
                                            @endif
                                            <button wire:click="delete({{ $entry->id }})" wire:confirm="Supprimer cette entrée ?" class="p-1.5 text-rose-400 hover:text-rose-300 hover:bg-rose-900/40 rounded" title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                                        Aucune entrée carburant trouvée.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>{{ $entries->links() }}</div>
        </div>
    </div>

    {{-- Modal Formulaire --}}
    @if($showFormModal)
    <div class="fixed inset-0 bg-black/60 z-40 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-xl shadow-2xl max-w-2xl w-full border border-gray-800 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold text-gray-100">
                    {{ $editingId ? 'Modifier l\'entrée' : 'Nouveau plein carburant' }}
                </h3>
                <button wire:click="$set('showFormModal', false)" class="text-gray-400 hover:text-gray-200">✕</button>
            </div>
            <form wire:submit.prevent="save" class="p-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Véhicule *</label>
                        <select wire:model.live="form.vehicle_id" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            <option value="">Sélectionner</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}">{{ $v->immatriculation }} - {{ $v->marque }} {{ $v->modele }}</option>
                            @endforeach
                        </select>
                        @error('form.vehicle_id') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Conducteur</label>
                        <select wire:model="form.driver_id" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            <option value="">Sélectionner</option>
                            @foreach($drivers as $d)
                                <option value="{{ $d->id }}">{{ $d->prenom }} {{ $d->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Date du plein *</label>
                        <input type="date" wire:model="form.date_plein" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        @error('form.date_plein') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Kilométrage actuel *</label>
                        <input type="number" wire:model="form.kilometrage" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        @error('form.kilometrage') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Quantité (L) *</label>
                        <input type="number" step="0.01" wire:model.live="form.quantite_litres" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        @error('form.quantite_litres') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Prix unitaire (DH/L) *</label>
                        <input type="number" step="0.01" wire:model.live="form.prix_unitaire" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        @error('form.prix_unitaire') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Montant total (DH)</label>
                        <input type="number" step="0.01" wire:model="form.montant_total" readonly class="w-full rounded-md border-gray-700 bg-gray-700 text-gray-100 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Station</label>
                        <input type="text" wire:model="form.station" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Ex: Afriquia Hay Riad">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Type carburant</label>
                        <input type="text" wire:model="form.type_carburant" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Ex: Diesel">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">N° Bon</label>
                        <input type="text" wire:model="form.numero_bon" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Ordre de mission lié</label>
                    <select wire:model="form.mission_order_id" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Aucun</option>
                        @foreach($missions as $m)
                            <option value="{{ $m->id }}">{{ $m->reference }} - {{ $m->objet }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Observations</label>
                    <textarea wire:model="form.observations" rows="2" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-800">
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

    {{-- Modal Détails --}}
    @if($showDetailsModal && $detailEntry)
    <div class="fixed inset-0 bg-black/60 z-40 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-xl shadow-2xl max-w-2xl w-full border border-gray-800">
            <div class="flex items-center justify-between p-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold text-gray-100">Détails du plein</h3>
                <button wire:click="$set('showDetailsModal', false)" class="text-gray-400 hover:text-gray-200">✕</button>
            </div>
            <div class="p-4 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Date</p>
                        <p class="font-medium text-gray-100">{{ $detailEntry->date_plein->format('d/m/Y') }}</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Kilométrage</p>
                        <p class="font-medium text-gray-100">{{ number_format($detailEntry->kilometrage, 0, ',', ' ') }} km</p>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Véhicule</p>
                    <p class="font-medium text-gray-100">{{ $detailEntry->vehicle->marque }} {{ $detailEntry->vehicle->modele }}</p>
                    <p class="text-sm text-gray-300">{{ $detailEntry->vehicle->immatriculation }}</p>
                </div>

                @if($detailEntry->driver)
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Conducteur</p>
                    <p class="font-medium text-gray-100">{{ $detailEntry->driver->prenom }} {{ $detailEntry->driver->nom }}</p>
                </div>
                @endif

                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-indigo-900/40 rounded-lg p-3 border border-indigo-700">
                        <p class="text-xs text-indigo-300">Quantité</p>
                        <p class="font-bold text-indigo-100">{{ number_format($detailEntry->quantite_litres, 2, ',', ' ') }} L</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Prix unitaire</p>
                        <p class="font-medium text-gray-100">{{ number_format($detailEntry->prix_unitaire, 2, ',', ' ') }} DH/L</p>
                    </div>
                    <div class="bg-emerald-900/40 rounded-lg p-3 border border-emerald-700">
                        <p class="text-xs text-emerald-300">Montant total</p>
                        <p class="font-bold text-emerald-100">{{ number_format($detailEntry->montant_total, 2, ',', ' ') }} DH</p>
                    </div>
                </div>

                @if($detailEntry->consommation)
                <div class="bg-amber-900/40 rounded-lg p-3 border border-amber-700">
                    <p class="text-xs text-amber-300">Consommation calculée</p>
                    <p class="font-bold text-amber-100">{{ $detailEntry->consommation }} L/100km</p>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    @if($detailEntry->station)
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Station</p>
                        <p class="font-medium text-gray-100">{{ $detailEntry->station }}</p>
                    </div>
                    @endif
                    @if($detailEntry->numero_bon)
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">N° Bon</p>
                        <p class="font-medium text-gray-100">{{ $detailEntry->numero_bon }}</p>
                    </div>
                    @endif
                </div>

                @if($detailEntry->missionOrder)
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Ordre de mission</p>
                    <p class="font-medium text-indigo-400">{{ $detailEntry->missionOrder->reference }}</p>
                    <p class="text-sm text-gray-300">{{ $detailEntry->missionOrder->objet }}</p>
                </div>
                @endif

                @if($detailEntry->observations)
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Observations</p>
                    <p class="text-gray-100">{{ $detailEntry->observations }}</p>
                </div>
                @endif

                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Saisi par</p>
                    <p class="font-medium text-gray-100">{{ $detailEntry->user->name }}</p>
                    <p class="text-sm text-gray-300">{{ $detailEntry->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>