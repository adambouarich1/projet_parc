<?php
    $roleBadges = [
        'admin' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
        'responsable_parc' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
        'valideur' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
        'agent_saisie' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
        'consultation' => 'bg-gray-700/50 text-gray-300 border-gray-600/50',
    ];
 
    $prioriteBand = [
        'basse' => 'bg-gray-500',
        'moyenne' => 'bg-blue-500',
        'haute' => 'bg-amber-500',
        'critique' => 'bg-rose-500',
    ];
 
    $prioriteColors = [
        'basse' => 'bg-gray-500/15 text-gray-300 border-gray-500/30',
        'moyenne' => 'bg-blue-500/15 text-blue-300 border-blue-500/30',
        'haute' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
        'critique' => 'bg-rose-500/15 text-rose-300 border-rose-500/30',
    ];
 
    $statutColors = [
        'active' => 'bg-rose-500/15 text-rose-300 border-rose-500/30',
        'vue' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
        'traitee' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
        'ignoree' => 'bg-gray-700/50 text-gray-400 border-gray-600/50',
    ];
 
    $typeGroups = [
        'permis_expire' => ['icon' => 'id', 'color' => 'rose'],
        'permis_bientot' => ['icon' => 'id', 'color' => 'amber'],
        'assurance_expiree' => ['icon' => 'shield', 'color' => 'rose'],
        'assurance_bientot' => ['icon' => 'shield', 'color' => 'amber'],
        'ct_expire' => ['icon' => 'wrench', 'color' => 'rose'],
        'ct_bientot' => ['icon' => 'wrench', 'color' => 'amber'],
        'vidange_km' => ['icon' => 'drop', 'color' => 'orange'],
        'vidange_date' => ['icon' => 'drop', 'color' => 'orange'],
        'vignette_expiree' => ['icon' => 'tag', 'color' => 'rose'],
        'vignette_bientot' => ['icon' => 'tag', 'color' => 'amber'],
        'autre' => ['icon' => 'bell', 'color' => 'gray'],
    ];
?>
 
<div class="space-y-5 text-gray-100 font-sans">
 
    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Alertes</h2>
            <p class="text-sm text-gray-400 mt-0.5">Surveillez les échéances et anomalies de votre flotte.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full border {{ $roleBadges[auth()->user()->role] ?? 'bg-gray-800 text-gray-300 border-gray-700' }}">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ auth()->user()->role_label }}
            </span>
            @if(auth()->user()->canEdit())
                <button wire:click="refreshAlerts" wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 bg-gray-700/60 hover:bg-gray-600/70 border border-gray-600/40 text-gray-200 text-sm font-semibold rounded-lg transition-all duration-200">
                    <svg wire:loading.remove wire:target="refreshAlerts" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <svg wire:loading wire:target="refreshAlerts" class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span wire:loading.remove wire:target="refreshAlerts">Actualiser</span>
                    <span wire:loading wire:target="refreshAlerts">Analyse…</span>
                </button>
                <button wire:click="markAllAsViewed"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-lg shadow-lg shadow-indigo-500/20 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Tout marquer vu
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
 
    {{-- ── Stats 4 colonnes ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-gray-800/60 rounded-2xl border border-gray-700/50 px-5 py-4 flex items-center gap-4">
            <div class="p-2.5 rounded-xl bg-indigo-500/15 border border-indigo-500/20 text-indigo-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Total</p>
                <p class="font-bold text-indigo-300 mt-0.5" style="font-size:23px;">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="bg-gray-800/60 rounded-2xl border border-gray-700/50 px-5 py-4 flex items-center gap-4">
            <div class="p-2.5 rounded-xl bg-amber-500/15 border border-amber-500/20 text-amber-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Actives</p>
                <p class="font-bold text-amber-300 mt-0.5" style="font-size:23px;">{{ $stats['actives'] }}</p>
            </div>
        </div>
        <div class="bg-gray-800/60 rounded-2xl border border-rose-500/20 px-5 py-4 flex items-center gap-4">
            <div class="p-2.5 rounded-xl bg-rose-500/15 border border-rose-500/20 text-rose-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Critiques</p>
                <p class="font-bold text-rose-300 mt-0.5" style="font-size:23px;">{{ $stats['critiques'] }}</p>
            </div>
        </div>
        <div class="bg-gray-800/60 rounded-2xl border border-gray-700/50 px-5 py-4 flex items-center gap-4">
            <div class="p-2.5 rounded-xl bg-orange-500/15 border border-orange-500/20 text-orange-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Priorité haute</p>
                <p class="font-bold text-orange-300 mt-0.5" style="font-size:23px;">{{ $stats['hautes'] }}</p>
            </div>
        </div>
    </div>
 
    <div class="flex flex-col lg:flex-row gap-5 items-start">
 
        {{-- ── Sidebar filtres ── --}}
        <div class="lg:w-64 shrink-0 w-full">
            <div class="bg-gray-800/60 rounded-xl p-5 border border-gray-700/50 shadow-lg sticky top-4 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-gray-300 uppercase tracking-widest">Filtres</h3>
                    <button type="button" wire:click="$set('filters', { type: '', priorite: '', statut: '', entity_type: '' })"
                        class="text-xs font-medium text-indigo-400 hover:text-indigo-300 transition-colors">Réinit.</button>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 block mb-1.5">Type</label>
                    <select wire:model.live="filters.type"
                        class="w-full px-3 py-2 rounded-lg border border-gray-600/60 bg-gray-900/60 text-gray-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all appearance-none">
                        <option value="">Tous</option>
                        @foreach ($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 block mb-1.5">Priorité</label>
                    <select wire:model.live="filters.priorite"
                        class="w-full px-3 py-2 rounded-lg border border-gray-600/60 bg-gray-900/60 text-gray-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all appearance-none">
                        <option value="">Toutes</option>
                        @foreach ($priorites as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 block mb-1.5">Statut</label>
                    <select wire:model.live="filters.statut"
                        class="w-full px-3 py-2 rounded-lg border border-gray-600/60 bg-gray-900/60 text-gray-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all appearance-none">
                        <option value="">Tous</option>
                        @foreach ($statuts as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 block mb-1.5">Entité</label>
                    <select wire:model.live="filters.entity_type"
                        class="w-full px-3 py-2 rounded-lg border border-gray-600/60 bg-gray-900/60 text-gray-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all appearance-none">
                        <option value="">Toutes</option>
                        <option value="vehicle">Véhicules</option>
                        <option value="driver">Conducteurs</option>
                    </select>
                </div>
            </div>
        </div>
 
        {{-- ── Liste des alertes ── --}}
        <div class="flex-1 min-w-0 space-y-3">
 
            @forelse ($alerts as $alert)
            <?php
                $tg = $typeGroups[$alert->type] ?? ['icon' => 'bell', 'color' => 'gray'];
                $isCritique = $alert->priorite === 'critique';
                $isExpired = $alert->jours_restants !== null && $alert->jours_restants < 0;
                $isUrgent = $alert->jours_restants !== null && $alert->jours_restants <= 7 && $alert->jours_restants >= 0;
            ?>
            <div wire:key="alert-{{ $alert->id }}"
                 class="group relative border rounded-2xl shadow-md hover:shadow-xl transition-all duration-200 overflow-hidden
                    {{ $isCritique ? 'bg-rose-950/30 border-rose-800/40 hover:border-rose-700/60' : 'bg-gray-800/60 hover:bg-gray-800/80 border-gray-700/50 hover:border-gray-600/70' }}">
 
                {{-- Bande colorée gauche selon priorité --}}
                <div class="absolute left-0 top-0 bottom-0 w-1 {{ $prioriteBand[$alert->priorite] ?? 'bg-gray-500' }} {{ $isCritique ? 'animate-pulse' : '' }}"></div>
 
                <div class="pl-5 pr-5 py-4">
 
                    {{-- ── Ligne 1 : icône type + titre + badges ── --}}
                    <div class="flex flex-wrap items-center gap-3 mb-2">
 
                        {{-- Icône type --}}
                        <div class="p-2 rounded-xl shrink-0 border
                            @if($tg['color'] === 'rose') bg-rose-500/15 text-rose-400 border-rose-500/20
                            @elseif($tg['color'] === 'amber') bg-amber-500/15 text-amber-400 border-amber-500/20
                            @elseif($tg['color'] === 'orange') bg-orange-500/15 text-orange-400 border-orange-500/20
                            @elseif($tg['color'] === 'blue') bg-blue-500/15 text-blue-400 border-blue-500/20
                            @else bg-gray-700/60 text-gray-400 border-gray-600/40
                            @endif">
                            @if($tg['icon'] === 'id')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                            @elseif($tg['icon'] === 'shield')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            @elseif($tg['icon'] === 'wrench')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            @elseif($tg['icon'] === 'drop')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            @elseif($tg['icon'] === 'tag')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @endif
                        </div>
 
                        {{-- Titre — 23px --}}
                        <span class="font-bold text-white leading-tight flex-1 min-w-0 truncate" style="font-size:23px;" title="{{ $alert->titre }}">
                            {{ $alert->titre }}
                        </span>
 
                        {{-- Badge priorité --}}
                        <span class="inline-flex items-center px-2.5 py-0.5 font-semibold rounded-full border shrink-0 {{ $prioriteColors[$alert->priorite] ?? '' }}" style="font-size:18px;">
                            {{ $alert->priorite_label }}
                        </span>
 
                        {{-- Badge statut --}}
                        <span class="inline-flex items-center px-2.5 py-0.5 font-semibold rounded-full border shrink-0 {{ $statutColors[$alert->statut] ?? '' }}" style="font-size:18px;">
                            {{ $alert->statut_label }}
                        </span>
                    </div>
 
                    {{-- ── Ligne 2 : type label ── --}}
                    <div class="flex items-center gap-2 mb-4" style="font-size:18px;">
                        <span class="text-gray-400">{{ $alert->type_label }}</span>
                    </div>
 
                    {{-- Séparateur --}}
                    <div class="border-t border-gray-700/40 mb-4"></div>
 
                    {{-- ── Ligne 3 : blocs infos + actions ── --}}
                    <div class="flex flex-col sm:flex-row items-stretch gap-3">
 
                        <div class="flex flex-1 gap-3">
 
                            {{-- Bloc Entité --}}
                            <div class="flex-1 flex items-center gap-3 bg-gray-900/50 rounded-xl px-4 py-3 border border-gray-700/40">
                                <div class="p-2 rounded-lg bg-indigo-500/15 text-indigo-400 shrink-0 border border-indigo-500/20">
                                    @if(str_contains($alert->alertable_type ?? '', 'Driver'))
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-indigo-400 uppercase tracking-widest leading-none mb-1.5" style="font-size:18px;">Entité</p>
                                    <p class="font-bold text-white leading-tight" style="font-size:23px;">{{ $this->getEntityName($alert->alertable) }}</p>
                                    <p class="text-gray-400 mt-0.5" style="font-size:18px;">{{ $this->getEntityType($alert->alertable_type) }}</p>
                                </div>
                            </div>
 
                            {{-- Bloc Échéance --}}
                            <div class="flex-1 flex items-center gap-3 rounded-xl px-4 py-3 border
                                @if($isExpired) bg-rose-950/30 border-rose-500/30
                                @elseif($isUrgent) bg-amber-950/20 border-amber-500/30
                                @else bg-gray-900/50 border-gray-700/40
                                @endif">
                                <div class="p-2 rounded-lg shrink-0 border
                                    @if($isExpired) bg-rose-500/15 text-rose-400 border-rose-500/20
                                    @elseif($isUrgent) bg-amber-500/15 text-amber-400 border-amber-500/20
                                    @else bg-amber-500/15 text-amber-400 border-amber-500/20
                                    @endif">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold uppercase tracking-widest leading-none mb-1.5
                                        @if($isExpired) text-rose-400
                                        @elseif($isUrgent) text-amber-400
                                        @else text-amber-400
                                        @endif" style="font-size:18px;">Échéance</p>
                                    @if($alert->date_echeance)
                                        <p class="font-bold leading-tight
                                            @if($isExpired) text-rose-300
                                            @elseif($isUrgent) text-amber-300
                                            @else text-white
                                            @endif" style="font-size:23px;">
                                            {{ $alert->date_echeance->format('d/m/Y') }}
                                        </p>
                                        @if($alert->jours_restants !== null)
                                            <p class="font-semibold mt-0.5
                                                @if($isExpired) text-rose-400
                                                @elseif($isUrgent) text-amber-400
                                                @else text-gray-400
                                                @endif" style="font-size:18px;">
                                                {{ $isExpired ? abs($alert->jours_restants).'j de retard' : $alert->jours_restants.'j restants' }}
                                            </p>
                                        @endif
                                    @else
                                        <p class="text-gray-600 font-semibold" style="font-size:23px;">—</p>
                                    @endif
                                </div>
                            </div>
 
                        </div>
 
                        {{-- Boutons d'action --}}
                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 shrink-0 self-center">
 
                            <button wire:click="openDetails({{ $alert->id }})"
                                class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-gray-300 bg-gray-700/60 hover:bg-gray-600/70 border border-gray-600/40 transition-all whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Voir
                            </button>
 
                            @if(auth()->user()->canEdit() && in_array($alert->statut, ['active', 'vue']))
                                <button wire:click="openTraitement({{ $alert->id }})"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-emerald-300 bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/30 transition-all whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Traiter
                                </button>
                                <button wire:click="ignoreAlert({{ $alert->id }})"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-gray-400 bg-gray-700/40 hover:bg-gray-600/60 border border-gray-600/30 transition-all whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    Ignorer
                                </button>
                            @endif
 
                        </div>
                    </div>
 
                </div>
            </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 bg-gray-800/40 rounded-2xl border border-gray-700/50">
                    <svg class="w-14 h-14 mb-4 opacity-20 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <p class="text-base font-semibold text-gray-400">Aucune alerte trouvée.</p>
                    <p class="text-sm mt-1 text-gray-600">Cliquez sur "Actualiser" pour scanner le système.</p>
                </div>
            @endforelse
 
            <div class="pt-2">{{ $alerts->links() }}</div>
        </div>
    </div>
 
    {{-- ── Modal Détails ── --}}
    @if($showDetailsModal && $detailAlert)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="fixed inset-0 bg-gray-950/75 backdrop-blur-sm" wire:click="closeDetails"></div>
        <div class="bg-gray-900 rounded-2xl shadow-2xl max-w-lg w-full border border-gray-700/80 relative z-10 flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800/60 shrink-0">
                <div class="flex items-center gap-3 flex-wrap">
                    <h3 class="text-lg font-bold text-white">Détails de l'alerte</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $prioriteColors[$detailAlert->priorite] ?? '' }}">{{ $detailAlert->priorite_label }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $statutColors[$detailAlert->statut] ?? '' }}">{{ $detailAlert->statut_label }}</span>
                </div>
                <button wire:click="closeDetails" class="text-gray-500 hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-800 transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="overflow-y-auto p-6 space-y-4 custom-scrollbar">
 
                <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Type</p>
                    <p class="font-semibold text-gray-100">{{ $detailAlert->type_label }}</p>
                </div>
 
                <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Titre</p>
                    <p class="font-semibold text-gray-100">{{ $detailAlert->titre }}</p>
                </div>
 
                @if($detailAlert->message)
                <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Message</p>
                    <p class="text-gray-300 text-sm">{{ $detailAlert->message }}</p>
                </div>
                @endif
 
                <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50 flex items-start gap-3">
                    <div class="p-2 bg-indigo-500/15 rounded-lg text-indigo-400 shrink-0 border border-indigo-500/20">
                        @if(str_contains($detailAlert->alertable_type ?? '', 'Driver'))
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Entité concernée</p>
                        <p class="font-semibold text-gray-100">{{ $this->getEntityName($detailAlert->alertable) }}</p>
                        <p class="text-sm text-gray-400">{{ $this->getEntityType($detailAlert->alertable_type) }}</p>
                    </div>
                </div>
 
                @if($detailAlert->date_echeance)
                <div class="rounded-xl p-4 border
                    @if($detailAlert->jours_restants < 0) bg-rose-500/10 border-rose-500/20
                    @elseif($detailAlert->jours_restants <= 7) bg-amber-500/10 border-amber-500/20
                    @else bg-gray-800/60 border-gray-700/50
                    @endif">
                    <p class="text-[10px] font-bold uppercase tracking-widest mb-1
                        @if($detailAlert->jours_restants < 0) text-rose-400
                        @elseif($detailAlert->jours_restants <= 7) text-amber-400
                        @else text-gray-500
                        @endif">Date d'échéance</p>
                    <p class="font-semibold
                        @if($detailAlert->jours_restants < 0) text-rose-300
                        @elseif($detailAlert->jours_restants <= 7) text-amber-300
                        @else text-gray-200
                        @endif">{{ $detailAlert->date_echeance->format('d/m/Y') }}</p>
                    @if($detailAlert->jours_restants !== null)
                        <p class="text-sm mt-0.5
                            @if($detailAlert->jours_restants < 0) text-rose-400
                            @else text-amber-400
                            @endif">
                            {{ $detailAlert->jours_restants < 0 ? abs($detailAlert->jours_restants).' jours de retard' : $detailAlert->jours_restants.' jours restants' }}
                        </p>
                    @endif
                </div>
                @endif
 
                @if($detailAlert->statut === 'traitee' && $detailAlert->treatedBy)
                <div class="bg-emerald-500/10 rounded-xl p-4 border border-emerald-500/20">
                    <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-1">Traitée par</p>
                    <p class="font-semibold text-emerald-300">{{ $detailAlert->treatedBy->name }}</p>
                    <p class="text-sm text-emerald-400/70">{{ $detailAlert->treated_at->format('d/m/Y H:i') }}</p>
                    @if($detailAlert->notes_traitement)
                        <p class="mt-2 text-sm text-gray-300 border-t border-emerald-500/20 pt-2">{{ $detailAlert->notes_traitement }}</p>
                    @endif
                </div>
                @endif
 
                <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Créée le</p>
                    <p class="text-sm text-gray-300">{{ $detailAlert->created_at->format('d/m/Y H:i') }}</p>
                </div>
 
            </div>
        </div>
    </div>
    @endif
 
    {{-- ── Modal Traitement ── --}}
    @if($showTraitementModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-950/75 backdrop-blur-sm" wire:click="closeTraitement"></div>
        <div class="bg-gray-900 rounded-2xl shadow-2xl max-w-md w-full border border-gray-700/80 relative z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800/60">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-lg font-bold text-white">Marquer comme traitée</h3>
                </div>
                <button wire:click="closeTraitement" class="text-gray-500 hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-5">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-300">Notes <span class="text-gray-500 font-normal">(optionnel)</span></label>
                    <textarea wire:model="notesTraitement" rows="3"
                        class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all placeholder-gray-600"
                        placeholder="Décrivez les actions effectuées..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-1">
                    <button wire:click="closeTraitement" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 border border-gray-700/50 transition-colors">Annuler</button>
                    <button wire:click="markAsTreated" class="px-5 py-2 text-sm font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-500 shadow-lg shadow-emerald-500/20 transition-all">
                        Confirmer le traitement
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
 
</div>