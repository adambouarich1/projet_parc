<?php
    $roleBadges = [
        'admin' => 'bg-red-50 text-red-700 border-red-200',
        'responsable_parc' => 'bg-green-50 text-green-700 border-green-200',
        'valideur' => 'bg-amber-50 text-amber-700 border-amber-200',
        'agent_saisie' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'consultation' => 'bg-gray-100 text-gray-800 border-gray-300',
    ];
 
    $prioriteConfig = [
        'critique' => [
            'label' => 'CRITIQUES',
            'dot' => 'bg-rose-500',
            'dot2' => 'bg-rose-400',
            'border' => 'border-rose-500/30',
            'bg' => 'bg-rose-950/30',
            'hoverBorder' => 'hover:border-rose-500/50',
            'band' => 'bg-rose-500',
            'countBg' => 'bg-red-50 text-red-700 border-red-200',
            'titleColor' => 'text-rose-400',
        ],
        'haute' => [
            'label' => 'ÉLEVÉES',
            'dot' => 'bg-orange-500',
            'dot2' => 'bg-orange-400',
            'border' => 'border-orange-500/30',
            'bg' => 'bg-orange-950/20',
            'hoverBorder' => 'hover:border-orange-500/50',
            'band' => 'bg-orange-500',
            'countBg' => 'bg-orange-50 text-orange-700 border-orange-200',
            'titleColor' => 'text-orange-400',
        ],
        'moyenne' => [
            'label' => 'MOYENNES',
            'dot' => 'bg-amber-500',
            'dot2' => 'bg-amber-400',
            'border' => 'border-amber-500/30',
            'bg' => 'bg-amber-950/15',
            'hoverBorder' => 'hover:border-amber-500/40',
            'band' => 'bg-amber-500',
            'countBg' => 'bg-amber-50 text-amber-700 border-amber-200',
            'titleColor' => 'text-amber-400',
        ],
        'basse' => [
            'label' => 'FAIBLES',
            'dot' => 'bg-emerald-500',
            'dot2' => 'bg-emerald-400',
            'border' => 'border-emerald-500/30',
            'bg' => 'bg-white',
            'hoverBorder' => 'hover:border-emerald-500/40',
            'band' => 'bg-emerald-500',
            'countBg' => 'bg-green-50 text-green-700 border-green-200',
            'titleColor' => 'text-emerald-400',
        ],
    ];
 
    $typeLabels = [
        'permis_expire' => 'Permis expiré',
        'permis_bientot' => 'Permis',
        'assurance_expiree' => 'Assurance',
        'assurance_bientot' => 'Assurance',
        'ct_expire' => 'Visite technique',
        'ct_bientot' => 'Visite technique',
        'vidange_km' => 'Entretien',
        'vidange_date' => 'Entretien',
        'vignette_expiree' => 'Vignette',
        'vignette_bientot' => 'Vignette',
        'autre' => 'Autre',
    ];
 
    $typeBadgeColors = [
        'permis_expire' => 'bg-rose-500/20 text-rose-300',
        'permis_bientot' => 'bg-amber-500/20 text-amber-300',
        'assurance_expiree' => 'bg-rose-500/20 text-rose-300',
        'assurance_bientot' => 'bg-amber-500/20 text-amber-300',
        'ct_expire' => 'bg-rose-500/20 text-rose-300',
        'ct_bientot' => 'bg-amber-500/20 text-amber-300',
        'vidange_km' => 'bg-orange-500/20 text-orange-300',
        'vidange_date' => 'bg-orange-500/20 text-orange-300',
        'vignette_expiree' => 'bg-rose-500/20 text-rose-300',
        'vignette_bientot' => 'bg-amber-500/20 text-amber-300',
        'autre' => 'bg-gray-600/30 text-gray-800',
    ];
?>
 
<div class="space-y-5 text-gray-900 font-sans">
 
    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Centre d'alertes</h2>
            <p class="text-sm mt-0.5">
                <span class="{{ $stats['total'] > 0 ? 'text-amber-400 font-semibold' : 'text-gray-800' }}">{{ $stats['total'] }} alerte(s) en attente</span>
            </p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            {{-- Toggle vues --}}
            <button wire:click="setViewMode('pending')"
                class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg border transition-all duration-200
                    {{ $viewMode === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-gray-700/60 text-gray-800 border-gray-300 hover:bg-gray-600/70' }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                En attente
            </button>
            <button wire:click="setViewMode('treated')"
                class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg border transition-all duration-200
                    {{ $viewMode === 'treated' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-700/60 text-gray-800 border-gray-300 hover:bg-gray-600/70' }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Traitées
            </button>
            <button wire:click="setViewMode('all')"
                class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-lg border transition-all duration-200
                    {{ $viewMode === 'all' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-700/60 text-gray-800 border-gray-300 hover:bg-gray-600/70' }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                Tout voir
            </button>
            {{-- Filtres type --}}
            <select wire:model.live="filters.type"
                class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-200 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all appearance-none">
                <option value="">Tous les types</option>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
 
            @if(auth()->user()->canEdit())
                <button wire:click="refreshAlerts" wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2.5 bg-gray-700/60 hover:bg-gray-600/70 border border-gray-300 text-gray-200 text-sm font-semibold rounded-lg transition-all">
                    <svg wire:loading.remove wire:target="refreshAlerts" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <svg wire:loading wire:target="refreshAlerts" class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Actualiser
                </button>
            @endif
        </div>
    </div>
 
    {{-- Flash succès --}}
    @if (session()->has('status'))
        <div class="flex items-center gap-3 rounded-xl bg-green-50 border border-green-300 text-green-700 px-4 py-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium">{{ session('status') }}</p>
        </div>
    @endif

    {{-- Erreur de vérification --}}
    @if($verificationError)
        <div class="flex items-start gap-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 px-4 py-3">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="text-sm font-semibold">Traitement impossible</p>
                <p class="text-sm mt-0.5 text-rose-400/80">{{ $verificationError }}</p>
            </div>
            <button wire:click="$set('verificationError', '')" class="ml-auto text-rose-400 hover:text-rose-300 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif
 
    {{-- ── Stats en bannières colorées ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-rose-950/40 rounded-2xl border border-rose-500/20 px-5 py-5 text-center">
            <p class="font-bold text-rose-400" style="font-size:28px;">{{ $stats['critiques'] }}</p>
            <p class="text-xs text-rose-300/70 font-medium mt-1">Action immédiate requise</p>
        </div>
        <div class="bg-orange-950/30 rounded-2xl border border-orange-500/20 px-5 py-5 text-center">
            <p class="font-bold text-orange-400" style="font-size:28px;">{{ $stats['hautes'] }}</p>
            <p class="text-xs text-orange-300/70 font-medium mt-1">À traiter rapidement</p>
        </div>
        <div class="bg-amber-950/20 rounded-2xl border border-amber-500/20 px-5 py-5 text-center">
            <p class="font-bold text-amber-400" style="font-size:28px;">{{ $stats['moyennes'] }}</p>
            <p class="text-xs text-amber-300/70 font-medium mt-1">À planifier</p>
        </div>
        <div class="bg-emerald-950/20 rounded-2xl border border-emerald-500/20 px-5 py-5 text-center">
            <p class="font-bold text-emerald-400" style="font-size:28px;">{{ $stats['basses'] }}</p>
            <p class="text-xs text-emerald-300/70 font-medium mt-1">Pour information</p>
        </div>
    </div>
 
    {{-- ══════════════════════════════════════════════════ --}}
    {{-- ── ALERTES GROUPÉES PAR PRIORITÉ ──               --}}
    {{-- ══════════════════════════════════════════════════ --}}
 
    @php $hasAnyAlert = false; @endphp
 
    @foreach(['critique', 'haute', 'moyenne', 'basse'] as $priorite)
        @php
            $alertsGroup = $grouped[$priorite] ?? collect();
            $config = $prioriteConfig[$priorite];
            if ($alertsGroup->count() > 0) $hasAnyAlert = true;
        @endphp
 
        @if($alertsGroup->count() > 0)
        <div class="space-y-3">
 
            {{-- Titre de section --}}
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full {{ $config['dot'] }}"></span>
                    <span class="w-2 h-2 rounded-full {{ $config['dot2'] }} opacity-60"></span>
                </div>
                <h3 class="text-sm font-bold {{ $config['titleColor'] }} uppercase tracking-widest">{{ $config['label'] }}</h3>
                <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold rounded-full border {{ $config['countBg'] }}">
                    {{ $alertsGroup->count() }}
                </span>
            </div>
 
            {{-- Grille de cartes --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                @foreach($alertsGroup as $alert)
                @php
                    $isVehicle = str_contains($alert->alertable_type ?? '', 'Vehicle');
                    $isExpired = $alert->jours_restants !== null && $alert->jours_restants < 0;
                @endphp
                <div wire:key="alert-{{ $alert->id }}"
                     class="relative border rounded-2xl overflow-hidden transition-all duration-200 {{ $config['bg'] }} {{ $config['border'] }} {{ $config['hoverBorder'] }} hover:shadow-lg">
 
                    {{-- Bande gauche --}}
                    <div class="absolute left-0 top-0 bottom-0 w-1 {{ $config['band'] }} {{ $priorite === 'critique' ? 'animate-pulse' : '' }}"></div>
 
                    <div class="pl-5 pr-4 py-4 space-y-3">
 
                        {{-- Ligne 1 : badge type + date --}}
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg {{ $typeBadgeColors[$alert->type] ?? 'bg-gray-600/30 text-gray-800' }}">
                                {{ $typeLabels[$alert->type] ?? $alert->type_label }}
                            </span>
                            <span class="text-xs text-gray-700">{{ $alert->created_at->format('Y-m-d') }}</span>
                        </div>
 
                        {{-- Ligne 2 : titre --}}
                        <p class="font-semibold text-gray-900 leading-snug" style="font-size:16px;">
                            {{ $alert->titre }}
                        </p>
 
                        {{-- Ligne 3 : entité (logo + nom) --}}
                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            @if($isVehicle && $alert->alertable)
                                <x-marque-logo :marque="$this->getEntityMarque($alert->alertable)" size="lg" />
                                <span>{{ $alert->alertable->marque }} {{ $alert->alertable->modele }}</span>
                                <span class="text-gray-800">—</span>
                                <span class="font-mono text-gray-800">{{ $alert->alertable->immatriculation }}</span>
                            @elseif($alert->alertable)
                                <svg class="w-5 h-5 text-gray-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>{{ $this->getEntityName($alert->alertable) }}</span>
                            @endif
                        </div>
 
                        {{-- Ligne 4 : actions --}}
                        <div class="flex items-center gap-2 pt-2 flex-wrap">
                            @if(auth()->user()->canEdit() && $alert->statut === 'active')
                                <button wire:click="markAsViewedById({{ $alert->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-amber-500/15 text-amber-300 border border-amber-500/30 hover:bg-amber-500/25 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Marquer comme vue
                                </button>
                            @endif
                            @if(auth()->user()->canEdit() && in_array($alert->statut, ['active', 'vue']))
                                <button wire:click="openTraitement({{ $alert->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/25 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Marquer comme traitée
                                </button>
                            @endif
                            @if(auth()->user()->canEdit() && $alert->statut === 'traitee')
                                <button wire:click="archiveAlert({{ $alert->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-purple-500/15 text-purple-300 border border-purple-500/30 hover:bg-purple-500/25 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    Archiver
                                </button>
                            @endif
                            <button wire:click="openDetails({{ $alert->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-700/15 text-green-700 border border-indigo-500/30 hover:bg-green-700/25 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Détails
                            </button>
                        </div>
 
                    </div>
                </div>
                @endforeach
            </div>
 
        </div>
        @endif
    @endforeach
 
    @if(!$hasAnyAlert)
        <div class="flex flex-col items-center justify-center py-20 bg-gray-50 rounded-2xl border border-gray-200">
            <svg class="w-16 h-16 mb-4 opacity-15 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-lg font-semibold text-gray-800">
                {{ $viewMode === 'treated' ? 'Aucune alerte traitée.' : ($viewMode === 'all' ? 'Aucune alerte.' : 'Aucune alerte en attente.') }}
            </p>
            <p class="text-sm mt-1 text-gray-800">
                {{ $viewMode === 'pending' ? 'Cliquez sur "Actualiser" pour scanner le système.' : 'Les alertes correspondantes apparaîtront ici.' }}
            </p>
        </div>
    @endif
 
    {{-- ══════════════════════════════════════════ --}}
    {{-- ── Modal Détails ──                       --}}
    {{-- ══════════════════════════════════════════ --}}
    @if($showDetailsModal && $detailAlert)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="fixed inset-0 bg-gray-50/75 backdrop-blur-sm" wire:click="closeDetails"></div>
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full border border-gray-200/80 relative z-10 flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200/60 shrink-0">
                <h3 class="text-lg font-bold text-gray-900">Détails de l'alerte</h3>
                <button wire:click="closeDetails" class="text-gray-700 hover:text-gray-800 p-1.5 rounded-lg hover:bg-gray-50 transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="overflow-y-auto p-6 space-y-4 custom-scrollbar">
 
                {{-- Type + priorité + statut --}}
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg {{ $typeBadgeColors[$detailAlert->type] ?? 'bg-gray-600/30 text-gray-800' }}">
                        {{ $typeLabels[$detailAlert->type] ?? $detailAlert->type_label }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg
                        @if($detailAlert->priorite === 'critique') bg-rose-500/20 text-rose-300
                        @elseif($detailAlert->priorite === 'haute') bg-orange-500/20 text-orange-300
                        @elseif($detailAlert->priorite === 'moyenne') bg-amber-500/20 text-amber-300
                        @else bg-emerald-500/20 text-emerald-300
                        @endif">
                        {{ $detailAlert->priorite_label }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg
                        @if($detailAlert->statut === 'active') bg-rose-500/20 text-rose-300
                        @elseif($detailAlert->statut === 'vue') bg-amber-500/20 text-amber-300
                        @elseif($detailAlert->statut === 'traitee') bg-emerald-500/20 text-emerald-300
                        @else bg-gray-600/30 text-gray-800
                        @endif">
                        {{ $detailAlert->statut_label }}
                    </span>
                </div>
 
                {{-- Titre --}}
                <div class="bg-white rounded-xl p-4 border border-gray-200">
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Titre</p>
                    <p class="font-semibold text-gray-900">{{ $detailAlert->titre }}</p>
                </div>
 
                @if($detailAlert->message)
                <div class="bg-white rounded-xl p-4 border border-gray-200">
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Message</p>
                    <p class="text-gray-800 text-sm">{{ $detailAlert->message }}</p>
                </div>
                @endif
 
                {{-- Entité --}}
                <div class="bg-white rounded-xl p-4 border border-gray-200 flex items-start gap-3">
                    @php $isVehicleDetail = str_contains($detailAlert->alertable_type ?? '', 'Vehicle'); @endphp
                    @if($isVehicleDetail && $detailAlert->alertable)
                        <x-marque-logo :marque="$this->getEntityMarque($detailAlert->alertable)" size="lg" />
                    @else
                        <div class="p-2 bg-green-700/15 rounded-lg text-green-700 shrink-0 border border-indigo-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    @endif
                    <div>
                        <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-0.5">Entité concernée</p>
                        <p class="font-semibold text-gray-900">{{ $this->getEntityName($detailAlert->alertable) }}</p>
                        <p class="text-sm text-gray-700">{{ $this->getEntityType($detailAlert->alertable_type) }}</p>
                    </div>
                </div>
 
                {{-- Échéance --}}
                @if($detailAlert->date_echeance)
                @php $jd = $detailAlert->jours_restants; $exp = $jd !== null && $jd < 0; @endphp
                <div class="rounded-xl p-4 border {{ $exp ? 'bg-rose-500/10 border-rose-500/20' : ($jd <= 7 ? 'bg-amber-500/10 border-amber-500/20' : 'bg-white border-gray-200') }}">
                    <p class="text-[10px] font-bold uppercase tracking-widest mb-1 {{ $exp ? 'text-rose-400' : ($jd <= 7 ? 'text-amber-400' : 'text-gray-700') }}">Date d'échéance</p>
                    <p class="font-semibold {{ $exp ? 'text-rose-300' : ($jd <= 7 ? 'text-amber-300' : 'text-gray-200') }}">{{ $detailAlert->date_echeance->format('d/m/Y') }}</p>
                    @if($jd !== null)
                        <p class="text-sm mt-0.5 {{ $exp ? 'text-rose-400' : 'text-amber-400' }}">
                            {{ $exp ? abs($jd).' jours de retard' : $jd.' jours restants' }}
                        </p>
                    @endif
                </div>
                @endif
 
                {{-- Vue par --}}
                @if($detailAlert->viewedBy)
                <div class="bg-amber-500/10 rounded-xl p-4 border border-amber-500/20">
                    <p class="text-[10px] font-bold text-amber-400 uppercase tracking-widest mb-1">Vue par</p>
                    <p class="font-semibold text-amber-300">{{ $detailAlert->viewedBy->name }}</p>
                    <p class="text-sm text-amber-400/70">{{ $detailAlert->viewed_at?->format('d/m/Y H:i') }}</p>
                </div>
                @endif

                {{-- Traitement --}}
                @if($detailAlert->statut === 'traitee' && $detailAlert->treatedBy)
                <div class="bg-emerald-500/10 rounded-xl p-4 border border-emerald-500/20">
                    <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-1">Traitée par</p>
                    <p class="font-semibold text-emerald-300">{{ $detailAlert->treatedBy->name }}</p>
                    <p class="text-sm text-emerald-400/70">{{ $detailAlert->treated_at?->format('d/m/Y H:i') }}</p>
                    @if($detailAlert->notes_traitement)
                        <p class="mt-2 text-sm text-gray-800 border-t border-emerald-500/20 pt-2">{{ $detailAlert->notes_traitement }}</p>
                    @endif
                </div>
                @endif
 
                <div class="bg-white rounded-xl p-4 border border-gray-200">
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Créée le</p>
                    <p class="text-sm text-gray-800">{{ $detailAlert->created_at->format('d/m/Y H:i') }}</p>
                </div>
 
            </div>
        </div>
    </div>
    @endif
 
    {{-- ══════════════════════════════════════════ --}}
    {{-- ── Modal Traitement ──                    --}}
    {{-- ══════════════════════════════════════════ --}}
    @if($showTraitementModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-50/75 backdrop-blur-sm" wire:click="closeTraitement"></div>
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full border border-gray-200/80 relative z-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200/60">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-lg font-bold text-gray-900">Marquer comme traitée</h3>
                </div>
                <button wire:click="closeTraitement" class="text-gray-700 hover:text-gray-800 p-1.5 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-5">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-800">Notes <span class="text-gray-700 font-normal">(optionnel)</span></label>
                    <textarea wire:model="notesTraitement" rows="3"
                        class="w-full rounded-lg border border-gray-300 bg-white text-gray-900 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all placeholder-gray-600"
                        placeholder="Décrivez les actions effectuées..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-1">
                    <button wire:click="closeTraitement" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-800 hover:bg-gray-50 border border-gray-200 transition-colors">Annuler</button>
                    <button wire:click="markAsTreated" class="px-5 py-2 text-sm font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-500 shadow-lg shadow-emerald-500/20 transition-all">
                        Confirmer le traitement
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
 
</div>