<?php
    $roleBadges = [
        'admin' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
        'responsable_parc' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
        'valideur' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
        'agent_saisie' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
        'consultation' => 'bg-gray-700/50 text-gray-300 border-gray-600/50',
    ];
?>
 
<div class="space-y-5 text-gray-100 font-sans">
 
    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Carburant</h2>
            <p class="text-sm text-gray-400 mt-0.5">Suivez les pleins et la consommation de votre flotte.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full border {{ $roleBadges[auth()->user()->role] ?? 'bg-gray-800 text-gray-300 border-gray-700' }}">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ auth()->user()->role_label }}
            </span>
            @if(auth()->user()->canEdit())
                <button type="button" wire:click="openCreate"
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-base font-semibold rounded-lg shadow-lg shadow-indigo-500/20 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nouveau plein
                </button>
            @endif
        </div>
    </div>
 
    {{-- Flash --}}
    @if (session()->has('status'))
        <div class="flex items-center gap-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 px-4 py-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium">{{ session('status') }}</p>
        </div>
    @endif
 
    {{-- ── Stats ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-gray-800/60 rounded-2xl border border-gray-700/50 px-5 py-4 flex items-center gap-4">
            <div class="p-2.5 rounded-xl bg-indigo-500/15 border border-indigo-500/20 text-indigo-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Total Litres</p>
                <p class="font-bold text-indigo-300 mt-0.5" style="font-size:23px;">{{ number_format($stats['total_litres'], 0, ',', ' ') }} <span class="text-base font-semibold text-indigo-400/70">L</span></p>
            </div>
        </div>
        <div class="bg-gray-800/60 rounded-2xl border border-gray-700/50 px-5 py-4 flex items-center gap-4">
            <div class="p-2.5 rounded-xl bg-emerald-500/15 border border-emerald-500/20 text-emerald-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Total Dépenses</p>
                <p class="font-bold text-emerald-300 mt-0.5" style="font-size:23px;">{{ number_format($stats['total_montant'], 2, ',', ' ') }} <span class="text-base font-semibold text-emerald-400/70">DH</span></p>
            </div>
        </div>
        <div class="bg-gray-800/60 rounded-2xl border border-gray-700/50 px-5 py-4 flex items-center gap-4">
            <div class="p-2.5 rounded-xl bg-amber-500/15 border border-amber-500/20 text-amber-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Nombre de Pleins</p>
                <p class="font-bold text-amber-300 mt-0.5" style="font-size:23px;">{{ $stats['nb_pleins'] }}</p>
            </div>
        </div>
    </div>
 
    <div class="flex flex-col lg:flex-row gap-5 items-start">
 
        {{-- ── Sidebar filtres ── --}}
        <div class="lg:w-64 shrink-0 w-full">
            <div class="bg-gray-800/60 rounded-xl p-5 border border-gray-700/50 shadow-lg sticky top-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-gray-300 uppercase tracking-widest">Filtres</h3>
                    <button type="button" wire:click="$set('filters', { vehicle_id: '', date_from: '', date_to: '' })"
                        class="text-xs font-medium text-indigo-400 hover:text-indigo-300 transition-colors">Réinit.</button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-400 block mb-1.5">Véhicule</label>
                        <select wire:model.live="filters.vehicle_id"
                            class="w-full px-3 py-2 rounded-lg border border-gray-600/60 bg-gray-900/60 text-gray-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all appearance-none">
                            <option value="">Tous</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}">{{ $v->immatriculation }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 block mb-1.5">Du</label>
                        <input type="date" wire:model.live="filters.date_from"
                            class="w-full px-3 py-2 rounded-lg border border-gray-600/60 bg-gray-900/60 text-gray-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all [&::-webkit-calendar-picker-indicator]:filter-invert">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 block mb-1.5">Au</label>
                        <input type="date" wire:model.live="filters.date_to"
                            class="w-full px-3 py-2 rounded-lg border border-gray-600/60 bg-gray-900/60 text-gray-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all [&::-webkit-calendar-picker-indicator]:filter-invert">
                    </div>
                </div>
            </div>
        </div>
 
        {{-- ── Liste des entrées ── --}}
        <div class="flex-1 min-w-0 space-y-3">
 
            @forelse ($entries as $entry)
            <div wire:key="entry-{{ $entry->id }}"
                 class="group relative bg-gray-800/60 hover:bg-gray-800/80 border border-gray-700/50 hover:border-gray-600/70 rounded-2xl shadow-md hover:shadow-xl transition-all duration-200 overflow-hidden">
 
                {{-- Bande colorée gauche selon consommation --}}
                <div class="absolute left-0 top-0 bottom-0 w-1
                    @if($entry->consommation && $entry->consommation > 12) bg-rose-500
                    @elseif($entry->consommation && $entry->consommation > 9) bg-amber-500
                    @else bg-emerald-500
                    @endif"></div>
 
                <div class="pl-5 pr-5 py-5">
 
                    {{-- ── Ligne 1 : date + véhicule + immat ── --}}
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        {{-- Date — info primaire 23px --}}
                        <span class="font-bold text-white leading-tight" style="font-size:23px;">
                            {{ $entry->date_plein->format('d/m/Y') }}
                        </span>
                        {{-- Immat badge --}}
                        <span class="font-mono font-bold text-emerald-300 bg-emerald-500/15 px-3 py-1 rounded-md border border-emerald-500/25 shrink-0 leading-tight" style="font-size:23px;">
                            {{ $entry->vehicle->immatriculation }}
                        </span>
                        {{-- Marque modèle --}}
                        <span class="text-gray-400 font-medium" style="font-size:18px;">
                            {{ $entry->vehicle->marque }} {{ $entry->vehicle->modele }}
                        </span>
                    </div>
 
                    {{-- ── Ligne 2 : station + N° bon ── --}}
                    @if($entry->station || $entry->numero_bon)
                    <div class="flex items-center gap-3 mb-4" style="font-size:18px;">
                        @if($entry->station)
                        <div class="flex items-center gap-1.5 text-gray-400">
                            <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-gray-300">{{ $entry->station }}</span>
                        </div>
                        @endif
                        @if($entry->numero_bon)
                        <span class="text-gray-500">·</span>
                        <span class="text-gray-400">Bon <span class="text-gray-300 font-medium">{{ $entry->numero_bon }}</span></span>
                        @endif
                    </div>
                    @else
                    <div class="mb-4"></div>
                    @endif
 
                    {{-- Séparateur --}}
                    <div class="border-t border-gray-700/40 mb-4"></div>
 
                    {{-- ── Ligne 3 : blocs infos + actions ── --}}
                    <div class="flex flex-col sm:flex-row items-stretch gap-3">
 
                        <div class="flex flex-1 gap-3">
 
                            {{-- Bloc Carburant --}}
                            <div class="flex-1 flex items-center gap-3 bg-gray-900/50 rounded-xl px-4 py-3 border border-gray-700/40">
                                <div class="p-2 rounded-lg bg-indigo-500/15 text-indigo-400 shrink-0 border border-indigo-500/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-indigo-400 uppercase tracking-widest leading-none mb-1.5" style="font-size:18px;">Carburant</p>
                                    <p class="font-bold text-white leading-tight" style="font-size:23px;">
                                        {{ number_format($entry->quantite_litres, 2, ',', ' ') }} <span class="text-indigo-300" style="font-size:18px;">L</span>
                                    </p>
                                    <p class="text-gray-400 mt-0.5" style="font-size:18px;">{{ $entry->type_carburant ?? 'N/A' }}</p>
                                </div>
                            </div>
 
                            {{-- Bloc Coût --}}
                            <div class="flex-1 flex items-center gap-3 bg-gray-900/50 rounded-xl px-4 py-3 border border-gray-700/40">
                                <div class="p-2 rounded-lg bg-emerald-500/15 text-emerald-400 shrink-0 border border-emerald-500/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-emerald-400 uppercase tracking-widest leading-none mb-1.5" style="font-size:18px;">Coût</p>
                                    <p class="font-bold text-white leading-tight" style="font-size:23px;">
                                        {{ number_format($entry->montant_total, 2, ',', ' ') }} <span class="text-emerald-300" style="font-size:18px;">DH</span>
                                    </p>
                                    <p class="text-gray-400 mt-0.5" style="font-size:18px;">{{ number_format($entry->prix_unitaire, 2, ',', ' ') }} DH/L</p>
                                </div>
                            </div>
 
                        </div>
 
                        {{-- Boutons d'action --}}
                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 shrink-0 self-center">
 
                            <button wire:click="openDetails({{ $entry->id }})"
                                class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-gray-300 bg-gray-700/60 hover:bg-gray-600/70 border border-gray-600/40 transition-all whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Voir
                            </button>
 
                            @if(auth()->user()->canEdit())
                                <button wire:click="openEdit({{ $entry->id }})"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-indigo-300 bg-indigo-500/15 hover:bg-indigo-500/25 border border-indigo-500/30 transition-all whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Modifier
                                </button>
                                <button wire:click="archive({{ $entry->id }})"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-gray-400 bg-gray-700/40 hover:bg-gray-600/60 border border-gray-600/30 transition-all whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    Archiver
                                </button>
                            <button @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { detail: { callback: () => $wire.delete({{ $entry->id }}) } }))" type="button"
                                class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-rose-300 bg-rose-500/15 hover:bg-rose-500/25 border border-rose-500/30 transition-all whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Supprimer
                            </button>
                            @endif
 
                        </div>
                    </div>
 
                    {{-- Conducteur lié (si présent) --}}
                    @if($entry->driver)
                    <div class="mt-3 flex items-center gap-2 text-gray-500" style="font-size:18px;">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="text-gray-400">{{ $entry->driver->prenom }} {{ $entry->driver->nom }}</span>
                        @if($entry->missionOrder)
                            <span class="text-gray-600 mx-1">·</span>
                            <span class="font-mono text-indigo-400/70 text-sm">{{ $entry->missionOrder->reference }}</span>
                        @endif
                    </div>
                    @endif
 
                </div>
            </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 bg-gray-800/40 rounded-2xl border border-gray-700/50">
                    <svg class="w-14 h-14 mb-4 opacity-20 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    <p class="text-base font-semibold text-gray-400">Aucune entrée carburant trouvée.</p>
                    <p class="text-sm mt-1 text-gray-600">Modifiez vos filtres ou enregistrez un nouveau plein.</p>
                </div>
            @endforelse
 
            <div class="pt-2">{{ $entries->links() }}</div>
        </div>
    </div>
 
    {{-- ── Modal Formulaire ── --}}
    @if($showFormModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" x-data x-transition.opacity>
        <div class="fixed inset-0 bg-gray-950/75 backdrop-blur-sm" wire:click="$set('showFormModal', false)"></div>
        <div class="bg-gray-900 rounded-2xl shadow-2xl max-w-2xl w-full border border-gray-700/80 relative z-10 flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800/60 shrink-0">
                <h3 class="text-lg font-bold text-white">{{ $editingId ? 'Modifier l\'entrée' : 'Nouveau plein carburant' }}</h3>
                <button wire:click="$set('showFormModal', false)" class="text-gray-500 hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="overflow-y-auto p-6 custom-scrollbar">
                <form wire:submit.prevent="save" class="space-y-5" id="fuel-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Véhicule <span class="text-rose-500">*</span></label>
                            <select wire:model.live="form.vehicle_id" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                                <option value="">Sélectionner</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->immatriculation }} — {{ $v->marque }} {{ $v->modele }}</option>
                                @endforeach
                            </select>
                            @error('form.vehicle_id') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Conducteur</label>
                            <select wire:model="form.driver_id" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                                <option value="">Sélectionner</option>
                                @foreach($drivers as $d)
                                    <option value="{{ $d->id }}">{{ $d->prenom }} {{ $d->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
 
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Date du plein <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model="form.date_plein" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all [&::-webkit-calendar-picker-indicator]:filter-invert">
                            @error('form.date_plein') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
 
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Quantité (L) <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.01" wire:model.live="form.quantite_litres" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                            @error('form.quantite_litres') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Prix unitaire (DH/L) <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.01" wire:model.live="form.prix_unitaire" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                            @error('form.prix_unitaire') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-400">Montant total (DH)</label>
                            <input type="number" step="0.01" wire:model="form.montant_total" readonly class="w-full rounded-lg border border-gray-700/40 bg-gray-700/40 text-gray-300 text-sm cursor-not-allowed">
                        </div>
                    </div>
 
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Station</label>
                            <input type="text" wire:model="form.station" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all placeholder-gray-600" placeholder="Ex: Afriquia">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Type carburant</label>
                            <input type="text" wire:model="form.type_carburant" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all placeholder-gray-600" placeholder="Ex: Diesel">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">N° Bon</label>
                            <input type="text" wire:model="form.numero_bon" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>
 
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-300">Ordre de mission lié</label>
                        <select wire:model="form.mission_order_id" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                            <option value="">Aucun</option>
                            @foreach($missions as $m)
                                <option value="{{ $m->id }}">{{ $m->reference }} — {{ $m->objet }}</option>
                            @endforeach
                        </select>
                    </div>
 
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-300">Observations</label>
                        <textarea wire:model="form.observations" rows="2" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all placeholder-gray-600" placeholder="Remarques éventuelles..."></textarea>
                    </div>
                </form>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-800/60 bg-gray-900/50 shrink-0 rounded-b-2xl">
                <button type="button" wire:click="$set('showFormModal', false)" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 border border-gray-700/50 transition-colors">Annuler</button>
                <button type="submit" form="fuel-form" class="px-5 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 shadow-lg shadow-indigo-500/20 transition-all">
                    {{ $editingId ? 'Mettre à jour' : 'Enregistrer' }}
                </button>
            </div>
        </div>
    </div>
    @endif
 
    {{-- ── Modal Détails ── --}}
    @if($showDetailsModal && $detailEntry)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="fixed inset-0 bg-gray-950/75 backdrop-blur-sm" wire:click="$set('showDetailsModal', false)"></div>
        <div class="bg-gray-900 rounded-2xl shadow-2xl max-w-2xl w-full border border-gray-700/80 relative z-10 flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800/60 shrink-0">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-bold text-white">Plein du {{ $detailEntry->date_plein->format('d/m/Y') }}</h3>
                    <span class="font-mono text-sm font-bold text-emerald-300 bg-emerald-500/15 px-2.5 py-0.5 rounded-md border border-emerald-500/25">
                        {{ $detailEntry->vehicle->immatriculation }}
                    </span>
                </div>
                <button wire:click="$set('showDetailsModal', false)" class="text-gray-500 hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="overflow-y-auto p-6 space-y-4 custom-scrollbar">
 
                {{-- Véhicule + conducteur --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50 flex items-start gap-3">
                        <div class="p-2 bg-emerald-500/15 rounded-lg text-emerald-400 shrink-0 border border-emerald-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Véhicule</p>
                            <p class="font-semibold text-gray-100">{{ $detailEntry->vehicle->marque }} {{ $detailEntry->vehicle->modele }}</p>
                            <p class="text-sm text-gray-400">{{ $detailEntry->vehicle->immatriculation }}</p>
                        </div>
                    </div>
                    @if($detailEntry->driver)
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50 flex items-start gap-3">
                        <div class="p-2 bg-indigo-500/15 rounded-lg text-indigo-400 shrink-0 border border-indigo-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Conducteur</p>
                            <p class="font-semibold text-gray-100">{{ $detailEntry->driver->prenom }} {{ $detailEntry->driver->nom }}</p>
                        </div>
                    </div>
                    @endif
                </div>
 
                {{-- Chiffres clés --}}
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-indigo-500/10 rounded-xl p-4 border border-indigo-500/20 text-center">
                        <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-1">Quantité</p>
                        <p class="font-bold text-indigo-300 text-xl">{{ number_format($detailEntry->quantite_litres, 2, ',', ' ') }}</p>
                        <p class="text-xs text-indigo-400/70 mt-0.5">litres</p>
                    </div>
                    <div class="bg-emerald-500/10 rounded-xl p-4 border border-emerald-500/20 text-center">
                        <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-1">Montant</p>
                        <p class="font-bold text-emerald-300 text-xl">{{ number_format($detailEntry->montant_total, 2, ',', ' ') }}</p>
                        <p class="text-xs text-emerald-400/70 mt-0.5">dirhams</p>
                    </div>
                    <div class="rounded-xl p-4 border text-center
                        @if($detailEntry->consommation && $detailEntry->consommation > 12) bg-rose-500/10 border-rose-500/20
                        @elseif($detailEntry->consommation && $detailEntry->consommation > 9) bg-amber-500/10 border-amber-500/20
                        @else bg-amber-500/10 border-amber-500/20
                        @endif">
                        <p class="text-[10px] font-bold uppercase tracking-widest mb-1
                            @if($detailEntry->consommation && $detailEntry->consommation > 12) text-rose-400
                            @else text-amber-400
                            @endif">Conso.</p>
                        <p class="font-bold text-xl
                            @if($detailEntry->consommation && $detailEntry->consommation > 12) text-rose-300
                            @elseif($detailEntry->consommation && $detailEntry->consommation > 9) text-amber-300
                            @else text-emerald-300
                            @endif">
                            {{ $detailEntry->consommation ?? '—' }}
                        </p>
                        <p class="text-xs text-amber-400/70 mt-0.5">L/100km</p>
                    </div>
                </div>
 
                {{-- Détails --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Prix unitaire</p>
                        <p class="font-semibold text-gray-200">{{ number_format($detailEntry->prix_unitaire, 2, ',', ' ') }} DH/L</p>
                    </div>
                </div>
 
                @if($detailEntry->station || $detailEntry->numero_bon || $detailEntry->type_carburant)
                <div class="grid grid-cols-3 gap-4">
                    @if($detailEntry->station)
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Station</p>
                        <p class="font-semibold text-gray-200">{{ $detailEntry->station }}</p>
                    </div>
                    @endif
                    @if($detailEntry->type_carburant)
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Type</p>
                        <p class="font-semibold text-gray-200">{{ $detailEntry->type_carburant }}</p>
                    </div>
                    @endif
                    @if($detailEntry->numero_bon)
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">N° Bon</p>
                        <p class="font-semibold text-gray-200">{{ $detailEntry->numero_bon }}</p>
                    </div>
                    @endif
                </div>
                @endif
 
                @if($detailEntry->missionOrder)
                <div class="bg-indigo-500/10 rounded-xl p-4 border border-indigo-500/20">
                    <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-1">Ordre de mission lié</p>
                    <p class="font-semibold text-indigo-300 font-mono">{{ $detailEntry->missionOrder->reference }}</p>
                    <p class="text-sm text-gray-300 mt-0.5">{{ $detailEntry->missionOrder->objet }}</p>
                </div>
                @endif
 
                @if($detailEntry->observations)
                <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Observations</p>
                    <p class="text-gray-300 text-sm">{{ $detailEntry->observations }}</p>
                </div>
                @endif
 
                <div class="flex items-center gap-3 pt-1">
                    <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 border border-gray-600/50 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Saisi par</p>
                        <p class="text-sm font-semibold text-gray-200">{{ $detailEntry->user->name }} <span class="text-gray-500 font-normal">le {{ $detailEntry->created_at->format('d/m/Y H:i') }}</span></p>
                    </div>
                </div>
 
            </div>
        </div>
    </div>
    @endif
 
</div>