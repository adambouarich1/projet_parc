<?php
    $roleBadges = [
        'admin' => 'bg-red-50 text-red-700 border-red-200',
        'responsable_parc' => 'bg-green-50 text-green-700 border-green-200',
        'valideur' => 'bg-amber-50 text-amber-700 border-amber-200',
        'agent_saisie' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'consultation' => 'bg-gray-100 text-gray-800 border-gray-300',
    ];
?>
 
<div class="space-y-5 text-gray-900 font-sans">
 
    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Vignettes</h2>
            <p class="text-sm text-gray-700 mt-0.5">Suivi des vignettes fiscales de votre flotte.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full border {{ $roleBadges[auth()->user()->role] ?? 'bg-gray-800 text-gray-800 border-gray-200' }}">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ auth()->user()->role_label }}
            </span>
            @if(auth()->user()->canEdit())
                <button type="button" wire:click="openCreate"
                    class="inline-flex items-center px-6 py-3 bg-green-700 hover:bg-green-700 text-white text-base font-semibold rounded-lg shadow-lg shadow-green-500/20 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nouvelle vignette
                </button>
            @endif
        </div>
    </div>
 
    {{-- Flash --}}
    @if (session()->has('status'))
        <div class="flex items-center gap-3 rounded-xl bg-green-50 border border-green-300 text-green-700 px-4 py-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium">{{ session('status') }}</p>
        </div>
    @endif
 
    {{-- ── Stats ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl border border-gray-200 px-5 py-4 flex items-center gap-4">
            <div class="p-2.5 rounded-xl bg-green-700/15 border border-indigo-500/20 text-green-700 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-700 uppercase tracking-widest">Flotte</p>
                <p class="font-bold text-green-700 mt-0.5" style="font-size:23px;">{{ $stats['total_vehicules'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 px-5 py-4 flex items-center gap-4">
            <div class="p-2.5 rounded-xl bg-emerald-500/15 border border-emerald-500/20 text-emerald-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-700 uppercase tracking-widest">Avec vignette</p>
                <p class="font-bold text-emerald-300 mt-0.5" style="font-size:23px;">{{ $stats['avec_vignette'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 px-5 py-4 flex items-center gap-4">
            <div class="p-2.5 rounded-xl bg-rose-500/15 border border-rose-500/20 text-rose-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-700 uppercase tracking-widest">Sans vignette</p>
                <p class="font-bold text-rose-300 mt-0.5" style="font-size:23px;">{{ $stats['sans_vignette'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 px-5 py-4 flex items-center gap-4">
            <div class="p-2.5 rounded-xl bg-amber-500/15 border border-amber-500/20 text-amber-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-700 uppercase tracking-widest">Expire bientôt</p>
                <p class="font-bold text-amber-300 mt-0.5" style="font-size:23px;">{{ $stats['expire_bientot'] }}</p>
            </div>
        </div>
    </div>
 
    <div class="flex flex-col lg:flex-row gap-5 items-start">
 
        {{-- ── Sidebar filtres ── --}}
        <div class="lg:w-64 shrink-0 w-full">
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm sticky top-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-gray-800 uppercase tracking-widest">Filtres</h3>
                    <button type="button" wire:click="$set('filters', { search: '', annee: '{{ date('Y') }}', section: '' })"
                        class="text-xs font-medium text-green-700 hover:text-green-600 transition-colors">Réinit.</button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-700 block mb-1.5">Recherche</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" wire:model.live.debounce.300ms="filters.search"
                                class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-200 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all placeholder-gray-500"
                                placeholder="Immat, réf...">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700 block mb-1.5">Année</label>
                        <select wire:model.live="filters.annee"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-200 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all appearance-none">
                            @foreach($annees as $annee)
                                <option value="{{ $annee }}">{{ $annee }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700 block mb-1.5">Section</label>
                        <select wire:model.live="filters.section"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-200 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all appearance-none">
                            <option value="">Toutes</option>
                            <option value="sans_vignette">Sans vignette uniquement</option>
                            <option value="avec_vignette">Avec vignette uniquement</option>
                        </select>
                    </div>
                </div>
 
                {{-- Résumé coût --}}
                <div class="mt-5 pt-4 border-t border-gray-200">
                    <p class="text-xs font-bold text-gray-700 uppercase tracking-widest mb-1">Coût total {{ $filters['annee'] ?: date('Y') }}</p>
                    <p class="font-bold text-emerald-300" style="font-size:20px;">{{ number_format($stats['total_montant'], 2, ',', ' ') }} <span class="text-sm text-emerald-400/70">DH</span></p>
                </div>
            </div>
        </div>
 
        {{-- ── Contenu principal ── --}}
        <div class="flex-1 min-w-0 space-y-6">
 
            {{-- ═══════════════════════════════════════════ --}}
            {{-- ── SECTION 1 : Véhicules sans vignette ──  --}}
            {{-- ═══════════════════════════════════════════ --}}
            @if($filters['section'] !== 'avec_vignette')
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-6 bg-rose-500 rounded-full"></div>
                        <h3 class="text-lg font-bold text-gray-900">Véhicules sans vignette</h3>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-rose-500/15 text-rose-400 border border-rose-500/25">
                        {{ $vehiculesSansVignette->count() }}
                    </span>
                    <span class="text-xs text-gray-700">pour {{ $filters['annee'] ?: date('Y') }}</span>
                </div>
 
                @if($vehiculesSansVignette->count() > 0)
                <div class="space-y-2">
                    @foreach($vehiculesSansVignette as $vehicle)
                    <div wire:key="sans-vignette-{{ $vehicle->id }}"
                         class="group relative bg-white hover:bg-gray-50/80 border border-gray-200 hover:border-rose-500/30 rounded-2xl shadow-md hover:shadow-xl transition-all duration-200 overflow-hidden">
 
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-rose-500"></div>
 
                        <div class="pl-5 pr-5 py-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <x-marque-logo :marque="$vehicle->marque" size="lg" />
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-bold text-rose-300 bg-rose-500/15 px-2.5 py-0.5 rounded-md border border-rose-500/25" style="font-size:18px;">
                                                {{ $vehicle->immatriculation }}
                                            </span>
                                            <span class="text-gray-800 font-medium" style="font-size:16px;">
                                                {{ $vehicle->marque }} {{ $vehicle->modele }}
                                            </span>
                                        </div>
                                        @if($vehicle->derniere_vignette)
                                            <p class="text-xs text-gray-700 mt-1">
                                                Dernière vignette : {{ $vehicle->derniere_vignette->annee }} — expirée le {{ $vehicle->derniere_vignette->date_expiration->format('d/m/Y') }}
                                                <span class="text-rose-400 font-semibold">({{ abs($vehicle->derniere_vignette->jours_restants) }}j de retard)</span>
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-700 mt-1">Aucune vignette enregistrée</p>
                                        @endif
                                    </div>
                                </div>
 
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-rose-500/15 text-rose-400 border border-rose-500/25">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                        Sans vignette
                                    </span>
                                    @if(auth()->user()->canEdit())
                                        <button wire:click="openCreate({{ $vehicle->id }})"
                                            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-green-700 bg-green-700/15 hover:bg-green-700/25 border border-indigo-500/30 transition-all whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Ajouter vignette
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="flex items-center gap-3 py-6 px-5 bg-emerald-500/5 rounded-2xl border border-emerald-500/20">
                    <svg class="w-6 h-6 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-medium text-emerald-300">Tous les véhicules ont leur vignette {{ $filters['annee'] ?: date('Y') }}.</p>
                </div>
                @endif
            </div>
            @endif
 
            {{-- ═══════════════════════════════════════════ --}}
            {{-- ── SECTION 2 : Véhicules avec vignette ──  --}}
            {{-- ═══════════════════════════════════════════ --}}
            @if($filters['section'] !== 'sans_vignette')
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-6 bg-emerald-500 rounded-full"></div>
                        <h3 class="text-lg font-bold text-gray-900">Véhicules avec vignette</h3>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/25">
                        {{ $vehiculesAvecVignette->count() }}
                    </span>
                    <span class="text-xs text-gray-700">pour {{ $filters['annee'] ?: date('Y') }}</span>
                </div>
 
                @if($vehiculesAvecVignette->count() > 0)
                <div class="space-y-3">
                    @foreach($vehiculesAvecVignette as $vehicle)
                    @php
                        $vignette = $vehicle->vignette_active;
                        $jours = $vignette->jours_restants;
                        $expireBientot = $jours <= 30;
 
                        if ($jours <= 7) {
                            $barColor = 'bg-rose-500';
                            $badgeStyle = 'bg-rose-500/15 text-rose-400 border-rose-500/25';
                            $urgenceLabel = 'Critique';
                        } elseif ($jours <= 30) {
                            $barColor = 'bg-amber-500';
                            $badgeStyle = 'bg-amber-500/15 text-amber-400 border-amber-500/25';
                            $urgenceLabel = 'Expire bientôt';
                        } else {
                            $barColor = 'bg-emerald-500';
                            $badgeStyle = 'bg-emerald-500/15 text-emerald-400 border-emerald-500/25';
                            $urgenceLabel = 'En règle';
                        }
                    @endphp
 
                    <div wire:key="avec-vignette-{{ $vehicle->id }}"
                         class="group relative bg-white hover:bg-gray-50/80 border border-gray-200 hover:border-gray-600/70 rounded-2xl shadow-md hover:shadow-xl transition-all duration-200 overflow-hidden">
 
                        <div class="absolute left-0 top-0 bottom-0 w-1 {{ $barColor }}"></div>
 
                        <div class="pl-5 pr-5 py-5">
 
                            {{-- Ligne 1 : Véhicule + année + badge --}}
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                <x-marque-logo :marque="$vehicle->marque" size="lg" />
                                <span class="font-mono font-bold text-emerald-300 bg-emerald-500/15 px-3 py-1 rounded-md border border-emerald-500/25 shrink-0 leading-tight" style="font-size:23px;">
                                    {{ $vehicle->immatriculation }}
                                </span>
                                <span class="text-gray-800 font-medium" style="font-size:18px;">
                                    {{ $vehicle->marque }} {{ $vehicle->modele }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-lg bg-green-700/15 text-green-700 border border-indigo-500/25">
                                    {{ $vignette->annee }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeStyle }}">
                                    @if($expireBientot)
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @else
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                    {{ $urgenceLabel }}
                                </span>
                            </div>
 
                            {{-- Ligne 2 : Réf paiement --}}
                            @if($vignette->reference_paiement)
                            <div class="flex items-center gap-3 mb-4" style="font-size:18px;">
                                <div class="flex items-center gap-1.5 text-gray-800">
                                    <svg class="w-4 h-4 text-gray-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <span class="text-gray-800">Réf. <span class="font-medium">{{ $vignette->reference_paiement }}</span></span>
                                </div>
                                @if($vignette->date_paiement)
                                <span class="text-gray-700">·</span>
                                <span class="text-gray-800">Payée le <span class="text-gray-800">{{ $vignette->date_paiement->format('d/m/Y') }}</span></span>
                                @endif
                            </div>
                            @else
                            <div class="mb-4"></div>
                            @endif
 
                            <div class="border-t border-gray-200 mb-4"></div>
 
                            {{-- Ligne 3 : Blocs infos + actions --}}
                            <div class="flex flex-col sm:flex-row items-stretch gap-3">
 
                                <div class="flex flex-1 gap-3">
 
                                    {{-- Bloc Validité --}}
                                    <div class="flex-1 flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3 border border-gray-200">
                                        <div class="p-2 rounded-lg {{ $expireBientot ? 'bg-amber-500/15 text-amber-400 border-amber-500/20' : 'bg-green-700/15 text-green-700 border-indigo-500/20' }} shrink-0 border">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-bold {{ $expireBientot ? 'text-amber-400' : 'text-green-700' }} uppercase tracking-widest leading-none mb-1.5" style="font-size:14px;">Validité</p>
                                            <p class="font-bold text-gray-900 leading-tight" style="font-size:23px;">
                                                {{ $jours }} <span class="{{ $expireBientot ? 'text-amber-300' : 'text-green-700' }}" style="font-size:18px;">jours</span>
                                            </p>
                                            <p class="text-gray-800 mt-0.5" style="font-size:14px;">Exp. {{ $vignette->date_expiration->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
 
                                    {{-- Bloc Coût --}}
                                    <div class="flex-1 flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3 border border-gray-200">
                                        <div class="p-2 rounded-lg bg-emerald-500/15 text-emerald-400 shrink-0 border border-emerald-500/20">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-emerald-400 uppercase tracking-widest leading-none mb-1.5" style="font-size:14px;">Coût</p>
                                            <p class="font-bold text-gray-900 leading-tight" style="font-size:23px;">
                                                {{ number_format($vignette->montant, 2, ',', ' ') }} <span class="text-emerald-300" style="font-size:18px;">DH</span>
                                            </p>
                                            <p class="text-gray-800 mt-0.5" style="font-size:14px;">Vignette {{ $vignette->annee }}</p>
                                        </div>
                                    </div>
 
                                </div>
 
                                {{-- Boutons d'action --}}
                                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 shrink-0 self-center">
                                    <button wire:click="openDetails({{ $vignette->id }})"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-gray-800 bg-gray-700/60 hover:bg-gray-600/70 border border-gray-300 transition-all whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Voir
                                    </button>
 
                                    @if(auth()->user()->canEdit())
                                        <button wire:click="openEdit({{ $vignette->id }})"
                                            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-green-700 bg-green-700/15 hover:bg-green-700/25 border border-indigo-500/30 transition-all whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Modifier
                                        </button>
                                        <button wire:click="archive({{ $vignette->id }})"
                                            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-gray-800 bg-gray-700/40 hover:bg-gray-600/60 border border-gray-600/30 transition-all whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                            Archiver
                                        </button>
                                        <button @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { detail: { callback: () => $wire.delete({{ $vignette->id }}) } }))" type="button"
                                            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-rose-300 bg-rose-500/15 hover:bg-rose-500/25 border border-rose-500/30 transition-all whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Supprimer
                                        </button>
                                    @endif
                                </div>
 
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-16 bg-gray-50 rounded-2xl border border-gray-200">
                    <svg class="w-14 h-14 mb-4 opacity-20 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-base font-semibold text-gray-800">Aucune vignette trouvée pour {{ $filters['annee'] ?: date('Y') }}.</p>
                    <p class="text-sm mt-1 text-gray-800">Modifiez vos filtres ou enregistrez une nouvelle vignette.</p>
                </div>
                @endif
            </div>
            @endif
 
        </div>
    </div>
 
    {{-- ══════════════════════════════════════════ --}}
    {{-- ── Modal Formulaire ──                    --}}
    {{-- ══════════════════════════════════════════ --}}
    @if($showFormModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" x-data x-transition.opacity>
        <div class="fixed inset-0 bg-gray-50/75 backdrop-blur-sm" wire:click="closeModals"></div>
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full border border-gray-200/80 relative z-10 flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200/60 shrink-0">
                <h3 class="text-lg font-bold text-gray-900">{{ $editingId ? 'Modifier la vignette' : 'Nouvelle vignette' }}</h3>
                <button wire:click="closeModals" class="text-gray-700 hover:text-gray-800 p-1.5 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="overflow-y-auto p-6 custom-scrollbar">
                <form wire:submit.prevent="save" class="space-y-5" id="vignette-form">
 
                    {{-- Véhicule --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-800">Véhicule <span class="text-rose-500">*</span></label>
                        <select wire:model="form.vehicle_id" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all">
                            <option value="">Sélectionner</option>
                            @foreach($vehiclesForForm as $v)
                                <option value="{{ $v->id }}">{{ $v->immatriculation }} — {{ $v->marque }} {{ $v->modele }}</option>
                            @endforeach
                        </select>
                        @error('form.vehicle_id') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>
 
                    {{-- Année --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-800">Année <span class="text-rose-500">*</span></label>
                        <select wire:model.live="form.annee" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all">
                            @for($y = date('Y') + 1; $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                        @error('form.annee') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>
 
                    {{-- Dates --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-800">Date début <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model="form.date_debut" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all [&::-webkit-calendar-picker-indicator]:filter-invert">
                            @error('form.date_debut') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-800">Date expiration <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model="form.date_expiration" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all [&::-webkit-calendar-picker-indicator]:filter-invert">
                            @error('form.date_expiration') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
 
                    {{-- Info dates auto --}}
                    <div class="flex items-center gap-2 bg-green-700/10 border border-indigo-500/20 rounded-lg px-4 py-3">
                        <svg class="w-4 h-4 text-green-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-green-700">Les dates sont automatiquement remplies selon l'année sélectionnée (01/01 au 31/12).</p>
                    </div>
 
                    {{-- Montant + Réf paiement --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-800">Montant (DH) <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.01" wire:model="form.montant" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all placeholder-gray-600" placeholder="0.00">
                            @error('form.montant') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-800">Réf. paiement</label>
                            <input type="text" wire:model="form.reference_paiement" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all placeholder-gray-600" placeholder="Ex: REC-12345">
                        </div>
                    </div>
 
                    {{-- Date paiement + Statut --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-800">Date de paiement</label>
                            <input type="date" wire:model="form.date_paiement" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all [&::-webkit-calendar-picker-indicator]:filter-invert">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-800">Statut</label>
                            <select wire:model="form.statut" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all">
                                @foreach($statuts as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
 
                    {{-- Observations --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-800">Observations</label>
                        <textarea wire:model="form.observations" rows="2" class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all placeholder-gray-600" placeholder="Notes éventuelles..."></textarea>
                    </div>
                </form>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200/60 bg-gray-50 shrink-0 rounded-b-2xl">
                <button type="button" wire:click="closeModals" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-800 hover:bg-gray-50 border border-gray-200 transition-colors">Annuler</button>
                <button type="submit" form="vignette-form" class="px-5 py-2 text-sm font-semibold rounded-lg bg-green-700 text-white hover:bg-green-700 shadow-lg shadow-green-500/20 transition-all">
                    {{ $editingId ? 'Mettre à jour' : 'Enregistrer' }}
                </button>
            </div>
        </div>
    </div>
    @endif
 
    {{-- ══════════════════════════════════════════ --}}
    {{-- ── Modal Détails ──                       --}}
    {{-- ══════════════════════════════════════════ --}}
    @if($showDetailsModal && $detailVignette)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="fixed inset-0 bg-gray-50/75 backdrop-blur-sm" wire:click="closeModals"></div>
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full border border-gray-200/80 relative z-10 flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200/60 shrink-0">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-bold text-gray-900">Vignette {{ $detailVignette->annee }}</h3>
                    <span class="font-mono text-sm font-bold text-emerald-300 bg-emerald-500/15 px-2.5 py-0.5 rounded-md border border-emerald-500/25">
                        {{ $detailVignette->vehicle->immatriculation }}
                    </span>
                </div>
                <button wire:click="closeModals" class="text-gray-700 hover:text-gray-800 p-1.5 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="overflow-y-auto p-6 space-y-4 custom-scrollbar">
 
                {{-- Véhicule --}}
                <div class="bg-white rounded-xl p-4 border border-gray-200 flex items-start gap-3">
                    <div class="p-2 bg-emerald-500/15 rounded-lg text-emerald-400 shrink-0 border border-emerald-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-0.5">Véhicule</p>
                        <p class="font-semibold text-gray-900">{{ $detailVignette->vehicle->marque }} {{ $detailVignette->vehicle->modele }}</p>
                        <p class="text-sm text-gray-700">{{ $detailVignette->vehicle->immatriculation }}</p>
                    </div>
                </div>
 
                {{-- Chiffres clés --}}
                @php
                    $joursDetail = $detailVignette->jours_restants;
                    $isExpiredDetail = $joursDetail < 0;
                @endphp
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-green-700/10 rounded-xl p-4 border border-indigo-500/20 text-center">
                        <p class="text-[10px] font-bold text-green-700 uppercase tracking-widest mb-1">Année</p>
                        <p class="font-bold text-green-700 text-xl">{{ $detailVignette->annee }}</p>
                    </div>
                    <div class="bg-emerald-500/10 rounded-xl p-4 border border-emerald-500/20 text-center">
                        <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-1">Montant</p>
                        <p class="font-bold text-emerald-300 text-xl">{{ number_format($detailVignette->montant, 2, ',', ' ') }}</p>
                        <p class="text-xs text-emerald-400/70 mt-0.5">dirhams</p>
                    </div>
                    <div class="rounded-xl p-4 border text-center {{ $isExpiredDetail ? 'bg-rose-500/10 border-rose-500/20' : ($joursDetail <= 30 ? 'bg-amber-500/10 border-amber-500/20' : 'bg-emerald-500/10 border-emerald-500/20') }}">
                        <p class="text-[10px] font-bold uppercase tracking-widest mb-1 {{ $isExpiredDetail ? 'text-rose-400' : ($joursDetail <= 30 ? 'text-amber-400' : 'text-emerald-400') }}">
                            {{ $isExpiredDetail ? 'Expirée' : 'Restant' }}
                        </p>
                        <p class="font-bold text-xl {{ $isExpiredDetail ? 'text-rose-300' : ($joursDetail <= 30 ? 'text-amber-300' : 'text-emerald-300') }}">
                            {{ $isExpiredDetail ? abs($joursDetail) : $joursDetail }}
                        </p>
                        <p class="text-xs mt-0.5 {{ $isExpiredDetail ? 'text-rose-400/70' : ($joursDetail <= 30 ? 'text-amber-400/70' : 'text-emerald-400/70') }}">
                            {{ $isExpiredDetail ? 'jours de retard' : 'jours' }}
                        </p>
                    </div>
                </div>
 
                {{-- Dates --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl p-4 border border-gray-200">
                        <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Date début</p>
                        <p class="font-semibold text-gray-200">{{ $detailVignette->date_debut->format('d/m/Y') }}</p>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-gray-200 {{ $isExpiredDetail ? 'border-rose-500/30' : '' }}">
                        <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Date expiration</p>
                        <p class="font-semibold {{ $isExpiredDetail ? 'text-rose-300' : 'text-gray-200' }}">{{ $detailVignette->date_expiration->format('d/m/Y') }}</p>
                    </div>
                </div>
 
                {{-- Paiement --}}
                @if($detailVignette->reference_paiement || $detailVignette->date_paiement)
                <div class="grid grid-cols-2 gap-4">
                    @if($detailVignette->reference_paiement)
                    <div class="bg-white rounded-xl p-4 border border-gray-200">
                        <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Réf. paiement</p>
                        <p class="font-semibold text-gray-200 font-mono">{{ $detailVignette->reference_paiement }}</p>
                    </div>
                    @endif
                    @if($detailVignette->date_paiement)
                    <div class="bg-white rounded-xl p-4 border border-gray-200">
                        <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Date paiement</p>
                        <p class="font-semibold text-gray-200">{{ $detailVignette->date_paiement->format('d/m/Y') }}</p>
                    </div>
                    @endif
                </div>
                @endif
 
                @if($detailVignette->observations)
                <div class="bg-white rounded-xl p-4 border border-gray-200">
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Observations</p>
                    <p class="text-gray-800 text-sm">{{ $detailVignette->observations }}</p>
                </div>
                @endif
 
                <div class="flex items-center gap-3 pt-1">
                    <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-gray-800 border border-gray-300 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-700">Créée par</p>
                        <p class="text-sm font-semibold text-gray-200">{{ $detailVignette->user->name }} <span class="text-gray-700 font-normal">le {{ $detailVignette->created_at->format('d/m/Y H:i') }}</span></p>
                    </div>
                </div>
 
            </div>
        </div>
    </div>
    @endif
 
</div>