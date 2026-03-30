<?php
    $statutColors = [
        'brouillon'        => 'bg-gray-500/10 text-gray-300 border-gray-500/20',
        'en_attente'       => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
        'valide'           => 'bg-blue-500/15 text-blue-300 border-blue-500/30',
        'rejete'           => 'bg-rose-500/15 text-rose-300 border-rose-500/30',
        'en_cours'         => 'bg-indigo-500/15 text-indigo-300 border-indigo-500/30',
        'depart_anticipe'  => 'bg-orange-500/15 text-orange-300 border-orange-500/30',
        'termine_attente'  => 'bg-yellow-500/15 text-yellow-300 border-yellow-500/30',
        'cloture'          => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
        'annule'           => 'bg-gray-700/50 text-gray-400 border-gray-600/50',
    ];

    $statutBand = [
        'brouillon'        => 'bg-gray-500',
        'en_attente'       => 'bg-amber-500',
        'valide'           => 'bg-blue-500',
        'rejete'           => 'bg-rose-500',
        'en_cours'         => 'bg-indigo-500',
        'depart_anticipe'  => 'bg-orange-500',
        'termine_attente'  => 'bg-yellow-500',
        'cloture'          => 'bg-emerald-500',
        'annule'           => 'bg-gray-600',
    ];
 
    $roleBadges = [
        'admin' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
        'responsable_parc' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
        'valideur' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
        'agent_saisie' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
        'consultation' => 'bg-gray-700/50 text-gray-300 border-gray-600/50',
    ];
?>
 
<div class="space-y-5 text-gray-100 font-sans">
 
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h2 class="text-2xl font-bold text-indigo-400 tracking-tight">Ordres de Mission</h2>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full border {{ $roleBadges[auth()->user()->role] ?? 'bg-gray-800 text-gray-300 border-gray-700' }}">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ auth()->user()->role_label }}
            </span>
            @if(auth()->user()->canEdit())
                <button type="button" wire:click="openCreate" class="inline-flex items-center px-9 py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-lg shadow-lg shadow-indigo-500/20 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nouvel OM
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
    @if (session()->has('error'))
        <div class="flex items-center gap-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 px-4 py-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif
 
    <div class="flex flex-col lg:flex-row gap-5 items-start">
 
        {{-- ── Sidebar filtres ── --}}
        <div class="lg:w-64 shrink-0 w-full">
            <div class="bg-gray-800/60 rounded-xl p-5 border border-gray-700/50 shadow-lg sticky top-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-gray-300 uppercase tracking-widest">Filtres</h3>
                    <button type="button" wire:click="$set('filters', { search: '', statut: '' })" class="text-xs font-medium text-indigo-400 hover:text-indigo-300 transition-colors">Réinit.</button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-400 block mb-1.5">Recherche</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="filters.search"
                                class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-600/60 bg-gray-900/60 text-gray-200 placeholder-gray-500 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                                placeholder="Réf, objet, conducteur…">
                        </div>
                        <p class="text-[10px] text-gray-600 mt-1.5 leading-tight">Réf. mission · Objet · Conducteur · Marque véhicule</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 block mb-2">Statut</label>
                        <div class="flex flex-col gap-2">

                            {{-- Tout voir --}}
                            <button type="button" wire:click="$set('filters.statut', 'tout_voir')"
                                class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold border transition-all
                                    {{ $filters['statut'] === 'tout_voir'
                                        ? 'bg-white/10 text-white border-white/30'
                                        : 'bg-gray-900/40 text-gray-400 border-gray-700/40 hover:bg-gray-700/40 hover:text-gray-200' }}">
                                Tout voir
                            </button>

                            {{-- Statuts principaux --}}
                            <div class="flex flex-col gap-1.5 pt-1">
                                @foreach ([
                                    'en_attente' => ['label' => 'En attente',  'active' => 'bg-amber-500/20 text-amber-200 border-amber-500/40',    'inactive' => 'hover:bg-amber-500/10 hover:text-amber-300'],
                                    'valide'     => ['label' => 'Validé',      'active' => 'bg-blue-500/20 text-blue-200 border-blue-500/40',       'inactive' => 'hover:bg-blue-500/10 hover:text-blue-300'],
                                    'en_cours'   => ['label' => 'En cours',    'active' => 'bg-indigo-500/20 text-indigo-200 border-indigo-500/40', 'inactive' => 'hover:bg-indigo-500/10 hover:text-indigo-300'],
                                ] as $key => $cfg)
                                <button type="button" wire:click="$set('filters.statut', '{{ $key }}')"
                                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-bold border transition-all
                                        {{ $filters['statut'] === $key
                                            ? $cfg['active']
                                            : 'bg-gray-900/40 text-gray-300 border-gray-700/40 ' . $cfg['inactive'] }}">
                                    {{ $cfg['label'] }}
                                </button>
                                @endforeach
                            </div>

                            {{-- Autres statuts (accordéon) --}}
                            <div class="pt-1" x-data="{ open: {{ in_array($filters['statut'], ['brouillon','rejete','depart_anticipe','termine_attente','cloture','annule']) ? 'true' : 'false' }} }">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg border transition-all bg-gray-900/40 border-gray-700/40 hover:bg-gray-700/40">
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Autres statuts</span>
                                    <svg class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" x-transition class="flex flex-col gap-1 mt-1.5">
                                    @foreach ([
                                        'brouillon'       => ['label' => 'Brouillon',                      'active' => 'bg-gray-500/20 text-gray-200 border-gray-500/40',        'inactive' => 'hover:bg-gray-500/10 hover:text-gray-300'],
                                        'rejete'          => ['label' => 'Rejeté',                         'active' => 'bg-rose-500/20 text-rose-200 border-rose-500/40',         'inactive' => 'hover:bg-rose-500/10 hover:text-rose-300'],
                                        'depart_anticipe' => ['label' => 'Départ anticipé',                'active' => 'bg-orange-500/20 text-orange-200 border-orange-500/40',   'inactive' => 'hover:bg-orange-500/10 hover:text-orange-300'],
                                        'termine_attente' => ['label' => 'Terminé, attente clôturation',   'active' => 'bg-yellow-500/20 text-yellow-200 border-yellow-500/40',   'inactive' => 'hover:bg-yellow-500/10 hover:text-yellow-300'],
                                        'cloture'         => ['label' => 'Clôturé',                        'active' => 'bg-emerald-500/20 text-emerald-200 border-emerald-500/40','inactive' => 'hover:bg-emerald-500/10 hover:text-emerald-300'],
                                        'annule'          => ['label' => 'Annulé',                         'active' => 'bg-gray-700/60 text-gray-300 border-gray-600/60',         'inactive' => 'hover:bg-gray-700/30 hover:text-gray-400'],
                                    ] as $key => $cfg)
                                    <button type="button" wire:click="$set('filters.statut', '{{ $key }}')"
                                        class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all
                                            {{ $filters['statut'] === $key
                                                ? $cfg['active']
                                                : 'bg-gray-900/40 text-gray-400 border-gray-700/40 ' . $cfg['inactive'] }}">
                                        {{ $cfg['label'] }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
 
        {{-- ── Liste des missions ── --}}
        <div class="flex-1 min-w-0 space-y-3">
 
            @forelse ($missions as $mission)
            <div wire:key="mission-{{ $mission->id }}"
                 class="group relative bg-gray-800/60 hover:bg-gray-800/80 border border-gray-700/50 hover:border-gray-600/70 rounded-2xl shadow-md hover:shadow-xl transition-all duration-200 overflow-hidden">
 
                {{-- Bande colorée gauche --}}
                <div class="absolute left-0 top-0 bottom-0 w-1 {{ $statutBand[$mission->statut] ?? 'bg-gray-500' }}"></div>
 
                <div class="pl-5 pr-5 py-5">
 
                    {{-- ── Ligne 1 : référence (primaire 23px) + badge + objet ── --}}
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        {{-- Référence — info primaire, 23px --}}
                        <span class="font-mono font-bold text-indigo-300 bg-indigo-500/15 px-3 py-1 rounded-md border border-indigo-500/25 shrink-0 leading-tight"
                              style="font-size:23px;">
                            {{ $mission->reference }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 font-semibold rounded-full border shrink-0 {{ $statutColors[$mission->statut] ?? '' }}"
                              style="font-size:18px;">
                            {{ $mission->statut_label }}
                        </span>
                        {{-- Objet — info primaire, 23px --}}
                        <span class="font-bold text-white truncate leading-tight" style="font-size:23px;">
                            {{ $mission->objet }}
                        </span>
                    </div>
 
                    {{-- ── Ligne 2 : trajet (secondaire 18px) ── --}}
                    <div class="flex items-center gap-2 mb-4" style="font-size:18px;">
                        <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-gray-400">{{ $mission->lieu_depart }}</span>
                        <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        <span class="text-gray-200 font-semibold">{{ $mission->destination }}</span>
                    </div>
 
                    {{-- Séparateur --}}
                    <div class="border-t border-gray-700/40 mb-4"></div>
 
                    {{-- ── Ligne 3 : blocs conducteur + planning + actions ── --}}
                    <div class="flex flex-col sm:flex-row items-stretch gap-3">
 
                        <div class="flex flex-1 gap-3">
 
                            {{-- Bloc Conducteur --}}
                            <div class="flex-1 flex items-center gap-3 bg-gray-900/50 rounded-xl px-4 py-3 border border-gray-700/40">
                                <div class="p-2 rounded-lg bg-indigo-500/15 text-indigo-400 shrink-0 border border-indigo-500/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div>
                                    {{-- Label secondaire — 18px, couleur distincte --}}
                                    <p class="font-bold text-indigo-400 uppercase tracking-widest leading-none mb-2" style="font-size:18px;">Conducteur</p>
                                    {{-- Nom — info primaire 23px --}}
                                    <p class="font-bold text-white leading-tight" style="font-size:23px;">
                                        {{ $mission->driver->prenom }} {{ $mission->driver->nom }}
                                    </p>
                                    {{-- Véhicule — secondaire 18px --}}
                                    <p class="text-gray-400 mt-1" style="font-size:18px;">
                                        {{ $mission->vehicle->marque }} {{ $mission->vehicle->modele }}
                                    </p>
                                </div>
                            </div>
 
                            {{-- Bloc Planning --}}
                            <div class="flex-1 flex items-center gap-3 bg-gray-900/50 rounded-xl px-4 py-3 border border-gray-700/40">
                                <div class="p-2 rounded-lg bg-amber-500/15 text-amber-400 shrink-0 border border-amber-500/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="w-full">
                                    {{-- Label secondaire — 18px, couleur distincte --}}
                                    <p class="font-bold text-amber-400 uppercase tracking-widest leading-none mb-2" style="font-size:18px;">Planning</p>
                                    {{-- Départ — info primaire 23px --}}
                                    <div class="flex items-baseline gap-2">
                                        <span class="font-semibold text-gray-500" style="font-size:18px;">Dép</span>
                                        <span class="font-bold text-white" style="font-size:23px;">{{ $mission->date_depart->format('d/m/Y') }}</span>
                                        <span class="font-mono text-gray-400" style="font-size:18px;">{{ $mission->date_depart->format('H:i') }}</span>
                                    </div>
                                    {{-- Retour — info primaire 23px --}}
                                    <div class="flex items-baseline gap-2 mt-1">
                                        <span class="font-semibold text-gray-500" style="font-size:18px;">Ret</span>
                                        <span class="font-bold text-gray-200" style="font-size:23px;">{{ $mission->date_retour_prevue->format('d/m/Y') }}</span>
                                        <span class="font-mono text-gray-400" style="font-size:18px;">{{ $mission->date_retour_prevue->format('H:i') }}</span>
                                    </div>
                                </div>
                            </div>
 
                        </div>
 
                        {{-- Boutons d'action --}}
                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 shrink-0 self-center">
 
                            <button wire:click="openDetails({{ $mission->id }})"
                                class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-gray-300 bg-gray-700/60 hover:bg-gray-600/70 border border-gray-600/40 hover:border-gray-500/60 transition-all whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Voir
                            </button>
 
                            @if(auth()->user()->canEdit())
 
                                @if(in_array($mission->statut, ['cloture', 'rejete', 'annule']))
                                    <button @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { detail: { callback: () => $wire.archive({{ $mission->id }}) } }))" type="button"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-gray-400 bg-gray-700/40 hover:bg-gray-600/60 border border-gray-600/30 transition-all whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                        Archiver
                                    </button>
                                @endif
 
                                @if($mission->statut === 'brouillon')
                                    <button wire:click="openEdit({{ $mission->id }})"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-indigo-300 bg-indigo-500/15 hover:bg-indigo-500/25 border border-indigo-500/30 transition-all whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Modifier
                                    </button>
                                    <button wire:click="submit({{ $mission->id }})"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-amber-300 bg-amber-500/15 hover:bg-amber-500/25 border border-amber-500/30 transition-all whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        Soumettre
                                    </button>
                                    <button @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { detail: { callback: () => $wire.delete({{ $mission->id }}) } }))" type="button"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-rose-300 bg-rose-500/15 hover:bg-rose-500/25 border border-rose-500/30 transition-all whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Supprimer
                                    </button>
                                @endif
 
                                @if($mission->statut === 'en_attente' && auth()->user()->canValidate())
                                    <button wire:click="validate_mission({{ $mission->id }})"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-emerald-300 bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/30 transition-all whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Valider
                                    </button>
                                    <button wire:click="openReject({{ $mission->id }})"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-rose-300 bg-rose-500/15 hover:bg-rose-500/25 border border-rose-500/30 transition-all whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Rejeter
                                    </button>
                                @endif
 
                                @if($mission->statut === 'valide')
                                    <button wire:click="start({{ $mission->id }})"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-blue-300 bg-blue-500/15 hover:bg-blue-500/25 border border-blue-500/30 transition-all whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Départ anticipé
                                    </button>
                                @endif

                                @if(in_array($mission->statut, ['en_cours', 'depart_anticipe', 'termine_attente']) && auth()->user()->canEdit())
                                    <button wire:click="closeModal({{ $mission->id }})"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-emerald-300 bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/30 transition-all whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Clôturer
                                    </button>
                                @endif

                                @if(!in_array($mission->statut, ['en_cours', 'depart_anticipe', 'termine_attente', 'cloture', 'annule']))
                                    <button @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { detail: { callback: () => $wire.cancel({{ $mission->id }}) } }))" type="button"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-gray-400 bg-gray-700/40 hover:bg-gray-600/60 border border-gray-600/30 transition-all whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        Annuler
                                    </button>
                                @endif
 
                            @endif
                        </div>
                    </div>
 
                </div>
            </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 bg-gray-800/40 rounded-2xl border border-gray-700/50">
                    <svg class="w-14 h-14 mb-4 opacity-20 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-base font-semibold text-gray-400">Aucun ordre de mission trouvé.</p>
                    <p class="text-sm mt-1 text-gray-600">Modifiez vos filtres ou créez une nouvelle mission.</p>
                </div>
            @endforelse
 
            <div class="pt-2">{{ $missions->links() }}</div>
        </div>
    </div>
 
    {{-- ── Modal Formulaire ── --}}
    @if($showFormModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" x-data x-transition.opacity>
        <div class="fixed inset-0 bg-gray-950/75 backdrop-blur-sm" wire:click="$set('showFormModal', false)"></div>
        <div class="bg-gray-900 rounded-2xl shadow-2xl max-w-2xl w-full border border-gray-700/80 relative z-10 flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800/60 shrink-0">
                <h3 class="text-lg font-bold text-white">{{ $editingId ? 'Modifier l\'ordre de mission' : 'Nouvel ordre de mission' }}</h3>
                <button wire:click="$set('showFormModal', false)" class="text-gray-500 hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="overflow-y-auto p-6 custom-scrollbar">
                <form wire:submit.prevent="save" class="space-y-5" id="om-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Véhicule <span class="text-rose-500">*</span></label>
                            <select wire:model="form.vehicle_id" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                                <option value="">Sélectionner un véhicule</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->immatriculation }} - {{ $v->marque }} {{ $v->modele }}</option>
                                @endforeach
                            </select>
                            @error('form.vehicle_id') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Conducteur <span class="text-rose-500">*</span></label>
                            <select wire:model="form.driver_id" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                                <option value="">Sélectionner un conducteur</option>
                                @foreach($drivers as $d)
                                    <option value="{{ $d->id }}">{{ $d->prenom }} {{ $d->nom }} ({{ $d->categories }})</option>
                                @endforeach
                            </select>
                            @error('form.driver_id') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-300">Objet de la mission <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="form.objet" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all placeholder-gray-500" placeholder="Ex: Réunion direction régionale">
                        @error('form.objet') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-300">Description</label>
                        <textarea wire:model="form.description" rows="3" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all placeholder-gray-500" placeholder="Détails supplémentaires..."></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Lieu de départ <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="form.lieu_depart" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                            @error('form.lieu_depart') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Destination <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="form.destination" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all placeholder-gray-500" placeholder="Ex: Rabat">
                            @error('form.destination') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Date/heure départ <span class="text-rose-500">*</span></label>
                            <input type="datetime-local" wire:model="form.date_depart" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all [&::-webkit-calendar-picker-indicator]:filter-invert">
                            @error('form.date_depart') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Date/heure retour prévue <span class="text-rose-500">*</span></label>
                            <input type="datetime-local" wire:model="form.date_retour_prevue" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all [&::-webkit-calendar-picker-indicator]:filter-invert">
                            @error('form.date_retour_prevue') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-800/60 bg-gray-900/50 shrink-0 rounded-b-2xl">
                <button type="button" wire:click="$set('showFormModal', false)" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 border border-gray-700/50 transition-colors">Annuler</button>
                <button type="submit" form="om-form" class="px-5 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 shadow-lg shadow-indigo-500/20 transition-all">
                    {{ $editingId ? 'Enregistrer les modifications' : 'Créer la mission' }}
                </button>
            </div>
        </div>
    </div>
    @endif
 
    {{-- ── Modal Détails ── --}}
    @if($showDetailsModal && $detailMission)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="fixed inset-0 bg-gray-950/75 backdrop-blur-sm" wire:click="$set('showDetailsModal', false)"></div>
        <div class="bg-gray-900 rounded-2xl shadow-2xl max-w-3xl w-full border border-gray-700/80 relative z-10 flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800/60 shrink-0">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-bold text-white">{{ $detailMission->reference }}</h3>
                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statutColors[$detailMission->statut] ?? '' }}">{{ $detailMission->statut_label }}</span>
                </div>
                <button wire:click="$set('showDetailsModal', false)" class="text-gray-500 hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="overflow-y-auto p-6 space-y-5 custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Objet</p>
                        <p class="font-semibold text-gray-100">{{ $detailMission->objet }}</p>
                        @if($detailMission->description)<p class="text-sm text-gray-400 mt-1.5">{{ $detailMission->description }}</p>@endif
                    </div>
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Trajet</p>
                        <div class="flex items-center gap-2 text-gray-100 font-semibold">
                            {{ $detailMission->lieu_depart }}
                            <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            {{ $detailMission->destination }}
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50 flex items-start gap-3">
                        <div class="p-2 bg-indigo-500/15 rounded-lg text-indigo-400 shrink-0 border border-indigo-500/20"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg></div>
                        <div><p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Véhicule</p><p class="font-semibold text-gray-100">{{ $detailMission->vehicle->marque }} {{ $detailMission->vehicle->modele }}</p><p class="text-sm text-gray-400">{{ $detailMission->vehicle->immatriculation }}</p></div>
                    </div>
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50 flex items-start gap-3">
                        <div class="p-2 bg-indigo-500/15 rounded-lg text-indigo-400 shrink-0 border border-indigo-500/20"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                        <div><p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Conducteur</p><p class="font-semibold text-gray-100">{{ $detailMission->driver->prenom }} {{ $detailMission->driver->nom }}</p><p class="text-sm text-gray-400">{{ $detailMission->driver->telephone }}</p></div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Départ prévu</p>
                        <p class="font-semibold text-gray-200">{{ $detailMission->date_depart->format('d/m/Y') }}</p>
                        <p class="text-sm text-gray-400 font-mono">{{ $detailMission->date_depart->format('H:i') }}</p>
                    </div>
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Retour prévu</p>
                        <p class="font-semibold text-gray-200">{{ $detailMission->date_retour_prevue->format('d/m/Y') }}</p>
                        <p class="text-sm text-gray-400 font-mono">{{ $detailMission->date_retour_prevue->format('H:i') }}</p>
                    </div>
                    @if($detailMission->date_retour_effective)
                    <div class="bg-emerald-500/10 rounded-xl p-4 border border-emerald-500/20">
                        <p class="text-[10px] font-bold text-emerald-500/80 uppercase tracking-widest mb-1">Retour effectif</p>
                        <p class="font-semibold text-emerald-300">{{ $detailMission->date_retour_effective->format('d/m/Y') }}</p>
                        <p class="text-sm text-emerald-400/70 font-mono">{{ $detailMission->date_retour_effective->format('H:i') }}</p>
                    </div>
                    @endif
                </div>
                @if($detailMission->km_depart)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Km départ</p><p class="font-semibold text-gray-200">{{ number_format($detailMission->km_depart, 0, ',', ' ') }} km</p></div>
                    @if($detailMission->km_retour)
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Km retour</p><p class="font-semibold text-gray-200">{{ number_format($detailMission->km_retour, 0, ',', ' ') }} km</p></div>
                    <div class="bg-indigo-500/10 rounded-xl p-4 border border-indigo-500/20"><p class="text-[10px] font-bold text-indigo-400/80 uppercase tracking-widest mb-1">Distance parcourue</p><p class="font-bold text-xl text-indigo-300">{{ number_format($detailMission->km_parcourus, 0, ',', ' ') }} km</p></div>
                    @endif
                </div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 border border-gray-600/50 shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                        <div><p class="text-xs text-gray-500">Demandé par</p><p class="text-sm font-semibold text-gray-200">{{ $detailMission->user->name }} <span class="text-gray-500 font-normal">le {{ $detailMission->created_at->format('d/m/y') }}</span></p></div>
                    </div>
                    @if($detailMission->validator)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full {{ $detailMission->statut === 'rejete' ? 'bg-rose-500/15 text-rose-400 border-rose-500/20' : 'bg-emerald-500/15 text-emerald-400 border-emerald-500/20' }} flex items-center justify-center border shrink-0">
                            @if($detailMission->statut === 'rejete')<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @else<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>@endif
                        </div>
                        <div><p class="text-xs text-gray-500">{{ $detailMission->statut === 'rejete' ? 'Rejeté par' : 'Validé par' }}</p><p class="text-sm font-semibold text-gray-200">{{ $detailMission->validator->name }} <span class="text-gray-500 font-normal">le {{ $detailMission->validated_at->format('d/m/y') }}</span></p></div>
                    </div>
                    @endif
                </div>
                @if($detailMission->statut === 'rejete' && $detailMission->motif_rejet)
                <div class="bg-rose-500/10 rounded-xl p-4 border border-rose-500/20">
                    <p class="text-[10px] font-bold text-rose-400 uppercase tracking-widest mb-1">Motif du rejet</p>
                    <p class="text-sm text-rose-200">{{ $detailMission->motif_rejet }}</p>
                </div>
                @endif
                @if(in_array($detailMission->statut, ['en_cours', 'depart_anticipe', 'termine_attente']) && auth()->user()->canEdit())
                <div class="bg-emerald-500/5 rounded-2xl p-5 border border-emerald-500/20 space-y-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Clôturer la mission</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-emerald-300">Kilométrage retour <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="form.km_retour" min="{{ $detailMission->km_depart }}" class="w-full rounded-lg border border-emerald-500/30 bg-gray-900/50 text-gray-100 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                            @error('form.km_retour') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-emerald-300">Observations</label>
                            <input type="text" wire:model="form.observations" class="w-full rounded-lg border border-emerald-500/30 bg-gray-900/50 text-gray-100 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all" placeholder="RAS, incident...">
                        </div>
                    </div>
                    <div class="flex justify-end pt-1">
                        <button wire:click="close({{ $detailMission->id }})" class="px-5 py-2 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-500 shadow-lg shadow-emerald-500/20 transition-all">Valider la clôture</button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
 
    {{-- ── Modal Rejet ── --}}
    @if($showRejectModal && $detailMission)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-950/80 backdrop-blur-sm" wire:click="$set('showRejectModal', false)"></div>
        <div class="bg-gray-900 rounded-2xl shadow-2xl max-w-md w-full border border-gray-700/80 relative z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800/60">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <h3 class="text-lg font-bold text-white">Rejeter la mission</h3>
                </div>
                <button wire:click="$set('showRejectModal', false)" class="text-gray-500 hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-5">
                <p class="text-sm text-gray-300">Vous êtes sur le point de rejeter la mission <strong class="text-indigo-400 font-mono">{{ $detailMission->reference }}</strong>.</p>
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-300">Motif du rejet <span class="text-rose-500">*</span> <span class="text-gray-500 font-normal">(min. 10 caractères)</span></label>
                    <textarea wire:model="motifRejet" rows="3" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition-all placeholder-gray-500" placeholder="Expliquez la raison du rejet..."></textarea>
                    @error('motifRejet') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-3 pt-1">
                    <button wire:click="$set('showRejectModal', false)" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 border border-gray-700/50 transition-colors">Annuler</button>
                    <button wire:click="reject" class="px-5 py-2 text-sm font-semibold rounded-lg bg-rose-600 text-white hover:bg-rose-500 shadow-lg shadow-rose-500/20 transition-all">Confirmer le rejet</button>
                </div>
            </div>
        </div>
    </div>
    @endif
 
</div>