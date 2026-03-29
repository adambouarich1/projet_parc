<?php
    $roleBadges = [
        'admin' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
        'responsable_parc' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
        'valideur' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
        'agent_saisie' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
        'consultation' => 'bg-gray-700/50 text-gray-300 border-gray-600/50',
    ];
 
    $typeBand = [
        'entretien' => 'bg-blue-500',
        'reparation' => 'bg-orange-500',
        'controle_technique' => 'bg-purple-500',
        'autre' => 'bg-gray-500',
    ];
 
    $typeColors = [
        'entretien' => 'bg-blue-500/15 text-blue-300 border-blue-500/30',
        'reparation' => 'bg-orange-500/15 text-orange-300 border-orange-500/30',
        'controle_technique' => 'bg-purple-500/15 text-purple-300 border-purple-500/30',
        'autre' => 'bg-gray-500/15 text-gray-300 border-gray-500/30',
    ];
 
    $typeIcons = [
        'entretien' => 'text-blue-400 bg-blue-500/15 border-blue-500/20',
        'reparation' => 'text-orange-400 bg-orange-500/15 border-orange-500/20',
        'controle_technique' => 'text-purple-400 bg-purple-500/15 border-purple-500/20',
        'autre' => 'text-gray-400 bg-gray-500/15 border-gray-500/20',
    ];
 
    $statutColors = [
        'planifie' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
        'en_cours' => 'bg-blue-500/15 text-blue-300 border-blue-500/30',
        'termine' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
        'annule' => 'bg-gray-700/50 text-gray-400 border-gray-600/50',
    ];
 
    $statutBand = [
        'planifie' => 'bg-amber-500',
        'en_cours' => 'bg-blue-500',
        'termine' => 'bg-emerald-500',
        'annule' => 'bg-gray-600',
    ];
?>
 
<div class="space-y-5 text-gray-100 font-sans">
 
    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Interventions</h2>
            <p class="text-sm text-gray-400 mt-0.5">Entretiens, réparations et contrôles techniques de votre flotte.</p>
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
                    Nouvelle intervention
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
            <div class="p-2.5 rounded-xl bg-emerald-500/15 border border-emerald-500/20 text-emerald-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Coût total</p>
                <p class="font-bold text-emerald-300 mt-0.5" style="font-size:23px;">{{ number_format($stats['total_cout'], 2, ',', ' ') }} <span class="text-base font-semibold text-emerald-400/70">DH</span></p>
            </div>
        </div>
        <div class="bg-gray-800/60 rounded-2xl border border-gray-700/50 px-5 py-4 flex items-center gap-4">
            <div class="p-2.5 rounded-xl bg-indigo-500/15 border border-indigo-500/20 text-indigo-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Total interventions</p>
                <p class="font-bold text-indigo-300 mt-0.5" style="font-size:23px;">{{ $stats['nb_interventions'] }}</p>
            </div>
        </div>
        <div class="bg-gray-800/60 rounded-2xl border border-gray-700/50 px-5 py-4 flex items-center gap-4">
            <div class="p-2.5 rounded-xl bg-amber-500/15 border border-amber-500/20 text-amber-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Planifiées</p>
                <p class="font-bold text-amber-300 mt-0.5" style="font-size:23px;">{{ $stats['planifiees'] }}</p>
            </div>
        </div>
    </div>
 
    <div class="flex flex-col lg:flex-row gap-5 items-start">
 
        {{-- ── Sidebar filtres ── --}}
        <div class="lg:w-64 shrink-0 w-full">
            <div class="bg-gray-800/60 rounded-xl p-5 border border-gray-700/50 shadow-lg sticky top-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-gray-300 uppercase tracking-widest">Filtres</h3>
                    <button type="button" wire:click="$set('filters', { vehicle_id: '', type: '', statut: '' })"
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
                        <label class="text-xs font-semibold text-gray-400 block mb-1.5">Type</label>
                        <select wire:model.live="filters.type"
                            class="w-full px-3 py-2 rounded-lg border border-gray-600/60 bg-gray-900/60 text-gray-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all appearance-none">
                            <option value="">Tous</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 block mb-1.5">Statut</label>
                        <select wire:model.live="filters.statut"
                            class="w-full px-3 py-2 rounded-lg border border-gray-600/60 bg-gray-900/60 text-gray-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all appearance-none">
                            <option value="">Tous</option>
                            @foreach($statuts as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
 
        {{-- ── Liste des interventions ── --}}
        <div class="flex-1 min-w-0 space-y-3">
 
            @forelse ($interventions as $intervention)
            <div wire:key="intervention-{{ $intervention->id }}"
                 class="group relative bg-gray-800/60 hover:bg-gray-800/80 border border-gray-700/50 hover:border-gray-600/70 rounded-2xl shadow-md hover:shadow-xl transition-all duration-200 overflow-hidden">
 
                {{-- Bande gauche : couleur selon statut --}}
                <div class="absolute left-0 top-0 bottom-0 w-1 {{ $statutBand[$intervention->statut] ?? 'bg-gray-500' }}"></div>
 
                <div class="pl-5 pr-5 py-5">
 
                    {{-- ── Ligne 1 : date + immat + type badge + statut ── --}}
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        <span class="font-mono font-bold bg-gray-700/60 text-gray-200 px-3 py-1 rounded-md border border-gray-600/40 shrink-0 leading-tight" style="font-size:23px;">
                            {{ $intervention->vehicle->immatriculation }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 font-semibold rounded-full border shrink-0 {{ $typeColors[$intervention->type] ?? '' }}" style="font-size:18px;">
                            {{ $intervention->type_label }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 font-semibold rounded-full border shrink-0 {{ $statutColors[$intervention->statut] ?? '' }}" style="font-size:18px;">
                            {{ $intervention->statut_label }}
                        </span>
                        <span class="ml-auto font-bold text-white leading-tight" style="font-size:23px;">
                            {{ $intervention->date_intervention->format('d/m/Y') }}
                        </span>
                    </div>
 
                    {{-- ── Ligne 2 : titre + prestataire ── --}}
                    <div class="flex items-center gap-3 mb-4" style="font-size:18px;">
                        <span class="text-gray-200 font-semibold">{{ $intervention->titre }}</span>
                        @if($intervention->prestataire)
                            <span class="text-gray-600">·</span>
                            <span class="text-gray-400">{{ $intervention->prestataire }}</span>
                        @endif
                        @if($intervention->numero_facture)
                            <span class="text-gray-600">·</span>
                            <span class="text-gray-500">Fact. <span class="text-gray-400">{{ $intervention->numero_facture }}</span></span>
                        @endif
                    </div>
 
                    {{-- Séparateur --}}
                    <div class="border-t border-gray-700/40 mb-4"></div>
 
                    {{-- ── Ligne 3 : blocs infos + actions ── --}}
                    <div class="flex flex-col sm:flex-row items-stretch gap-3">
 
                        <div class="flex flex-1 gap-3">
 
                            {{-- Bloc Véhicule avec LOGO --}}
                            <div class="flex-1 flex items-center gap-3 bg-gray-900/50 rounded-xl px-4 py-3 border border-gray-700/40">
                                {{-- Logo marque (taille agrandie) --}}
                                <x-marque-logo :marque="$intervention->vehicle->marque" size="lg" class="shrink-0" />
                                <div>
                                    <p class="font-bold uppercase tracking-widest leading-none mb-1.5 {{ $intervention->type === 'entretien' ? 'text-blue-400' : ($intervention->type === 'reparation' ? 'text-orange-400' : ($intervention->type === 'controle_technique' ? 'text-purple-400' : 'text-gray-400')) }}" style="font-size:18px;">Véhicule</p>
                                    <p class="font-bold text-white leading-tight" style="font-size:23px;">{{ $intervention->vehicle->marque }} {{ $intervention->vehicle->modele }}</p>
                                </div>
                            </div>
 
                            {{-- Bloc Coût --}}
                            <div class="flex-1 flex items-center gap-3 bg-gray-900/50 rounded-xl px-4 py-3 border border-gray-700/40">
                                <div class="p-2 rounded-lg bg-emerald-500/15 text-emerald-400 shrink-0 border border-emerald-500/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-emerald-400 uppercase tracking-widest leading-none mb-1.5" style="font-size:18px;">Coût de l'opération</p>
                                    <p class="font-bold text-white leading-tight" style="font-size:23px;">
                                        {{ number_format($intervention->cout_total, 2, ',', ' ') }} <span class="text-emerald-300" style="font-size:18px;">DH</span>
                                    </p>
                                </div>
                            </div>
 
                            {{-- Bloc Prochaine intervention --}}
                            <div class="flex-1 flex items-center gap-3 bg-gray-900/50 rounded-xl px-4 py-3 border border-gray-700/40">
                                <div class="p-2 rounded-lg bg-amber-500/15 text-amber-400 shrink-0 border border-amber-500/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-amber-400 uppercase tracking-widest leading-none mb-1.5" style="font-size:18px;">Prochaine</p>
                                    @if($intervention->date_prochaine)
                                        <p class="font-bold text-white leading-tight" style="font-size:23px;">{{ $intervention->date_prochaine->format('d/m/Y') }}</p>
                                    @else
                                        <p class="text-gray-600 font-semibold" style="font-size:23px;">—</p>
                                    @endif
                                </div>
                            </div>
 
                        </div>
 
                        {{-- Boutons d'action --}}
                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 shrink-0 self-center">
 
                            <button wire:click="openDetails({{ $intervention->id }})"
                                class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-gray-300 bg-gray-700/60 hover:bg-gray-600/70 border border-gray-600/40 transition-all whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Voir
                            </button>
 
                            @if(auth()->user()->canEdit())
                                <button wire:click="openEdit({{ $intervention->id }})"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-indigo-300 bg-indigo-500/15 hover:bg-indigo-500/25 border border-indigo-500/30 transition-all whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Modifier
                                </button>
 
                                @if($intervention->statut === 'planifie')
                                    <button wire:click="markAs({{ $intervention->id }}, 'en_cours')"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-blue-300 bg-blue-500/15 hover:bg-blue-500/25 border border-blue-500/30 transition-all whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Démarrer
                                    </button>
                                @endif
 
                                @if($intervention->statut === 'en_cours')
                                    <button wire:click="markAs({{ $intervention->id }}, 'termine')"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-emerald-300 bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/30 transition-all whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Terminer
                                    </button>
                                @endif
 
                                @if($intervention->statut === 'termine')
                                    <button wire:click="archive({{ $intervention->id }})"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-gray-400 bg-gray-700/40 hover:bg-gray-600/60 border border-gray-600/30 transition-all whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                        Archiver
                                    </button>
                                @endif
 
                                    <button @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { detail: { callback: () => $wire.delete({{ $intervention->id }}) } }))" type="button"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-rose-300 bg-rose-500/15 hover:bg-rose-500/25 border border-rose-500/30 transition-all whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Supprimer
                                    </button>
                            @endif
 
                        </div>
                    </div>
 
                </div>
            </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 bg-gray-800/40 rounded-2xl border border-gray-700/50">
                    <svg class="w-14 h-14 mb-4 opacity-20 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="text-base font-semibold text-gray-400">Aucune intervention trouvée.</p>
                    <p class="text-sm mt-1 text-gray-600">Modifiez vos filtres ou créez une nouvelle intervention.</p>
                </div>
            @endforelse
 
            <div class="pt-2">{{ $interventions->links() }}</div>
        </div>
    </div>
 
    {{-- ── Modal Formulaire ── --}}
    @if($showFormModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" x-data x-transition.opacity>
        <div class="fixed inset-0 bg-gray-950/75 backdrop-blur-sm" wire:click="$set('showFormModal', false)"></div>
        <div class="bg-gray-900 rounded-2xl shadow-2xl max-w-3xl w-full border border-gray-700/80 relative z-10 flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800/60 shrink-0">
                <h3 class="text-lg font-bold text-white">{{ $editingId ? 'Modifier l\'intervention' : 'Nouvelle intervention' }}</h3>
                <button wire:click="$set('showFormModal', false)" class="text-gray-500 hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="overflow-y-auto p-6 custom-scrollbar">
                <form wire:submit.prevent="save" class="space-y-5" id="intervention-form">
 
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
                            <label class="text-xs font-semibold text-gray-300">Type <span class="text-rose-500">*</span></label>
                            <select wire:model.live="form.type" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
 
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-300">Titre <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="form.titre" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all placeholder-gray-600" placeholder="Ex: Vidange + filtres">
                        @error('form.titre') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>
 
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-300">Description</label>
                        <textarea wire:model="form.description" rows="2" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all placeholder-gray-600"></textarea>
                    </div>
 
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Date intervention <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model="form.date_intervention" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all [&::-webkit-calendar-picker-indicator]:filter-invert">
                            @error('form.date_intervention') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Date prochaine intervention</label>
                            <input type="date" wire:model="form.date_prochaine" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all [&::-webkit-calendar-picker-indicator]:filter-invert">
                        </div>
                    </div>
 
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-300">Coût de l'opération (DH)</label>
                        <input type="number" step="0.01" wire:model="form.cout_operation" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all" placeholder="Ex: 1500.00">
                        @error('form.cout_operation') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>
 
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">Prestataire</label>
                            <input type="text" wire:model="form.prestataire" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all placeholder-gray-600" placeholder="Ex: Garage Central">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-gray-300">N° Facture</label>
                            <input type="text" wire:model="form.numero_facture" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>
 
                    {{-- Champs spécifiques Assurance --}}
                    @if(isset($form['type']) && $form['type'] === 'assurance')
                    <div class="bg-emerald-500/5 rounded-2xl p-5 border border-emerald-500/20 space-y-4">
                        <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Informations Assurance</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-emerald-300">Assureur</label>
                                <input type="text" wire:model="form.assureur" class="w-full rounded-lg border border-emerald-500/30 bg-gray-900/50 text-gray-100 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-emerald-300">N° Police</label>
                                <input type="text" wire:model="form.numero_police" class="w-full rounded-lg border border-emerald-500/30 bg-gray-900/50 text-gray-100 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-emerald-300">Date expiration</label>
                                <input type="date" wire:model="form.date_expiration_assurance" class="w-full rounded-lg border border-emerald-500/30 bg-gray-900/50 text-gray-100 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all [&::-webkit-calendar-picker-indicator]:filter-invert">
                            </div>
                        </div>
                    </div>
                    @endif
 
                    {{-- Champs spécifiques Contrôle Technique --}}
                    @if(isset($form['type']) && $form['type'] === 'controle_technique')
                    <div class="bg-purple-500/5 rounded-2xl p-5 border border-purple-500/20 space-y-4">
                        <h4 class="text-xs font-bold text-purple-400 uppercase tracking-widest">Contrôle Technique</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-purple-300">Résultat</label>
                                <select wire:model="form.resultat_ct" class="w-full rounded-lg border border-purple-500/30 bg-gray-900/50 text-gray-100 text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all">
                                    <option value="">Sélectionner</option>
                                    @foreach($resultats_ct as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-purple-300">Date expiration CT</label>
                                <input type="date" wire:model="form.date_expiration_ct" class="w-full rounded-lg border border-purple-500/30 bg-gray-900/50 text-gray-100 text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all [&::-webkit-calendar-picker-indicator]:filter-invert">
                            </div>
                        </div>
                    </div>
                    @endif
 
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-300">Statut</label>
                        <select wire:model="form.statut" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                            @foreach($statuts as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
 
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-300">Observations</label>
                        <textarea wire:model="form.observations" rows="2" class="w-full rounded-lg border border-gray-600/60 bg-gray-800/60 text-gray-100 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all placeholder-gray-600"></textarea>
                    </div>
 
                </form>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-800/60 bg-gray-900/50 shrink-0 rounded-b-2xl">
                <button type="button" wire:click="$set('showFormModal', false)" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-800 border border-gray-700/50 transition-colors">Annuler</button>
                <button type="submit" form="intervention-form" class="px-5 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 shadow-lg shadow-indigo-500/20 transition-all">
                    {{ $editingId ? 'Mettre à jour' : 'Enregistrer' }}
                </button>
            </div>
        </div>
    </div>
    @endif
 
    {{-- ── Modal Détails ── --}}
    @if($showDetailsModal && $detailIntervention)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="fixed inset-0 bg-gray-950/75 backdrop-blur-sm" wire:click="$set('showDetailsModal', false)"></div>
        <div class="bg-gray-900 rounded-2xl shadow-2xl max-w-2xl w-full border border-gray-700/80 relative z-10 flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800/60 shrink-0">
                <div class="flex items-center gap-3 flex-wrap">
                    <h3 class="text-lg font-bold text-white">{{ $detailIntervention->titre }}</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $typeColors[$detailIntervention->type] ?? '' }}">{{ $detailIntervention->type_label }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $statutColors[$detailIntervention->statut] ?? '' }}">{{ $detailIntervention->statut_label }}</span>
                </div>
                <button wire:click="$set('showDetailsModal', false)" class="text-gray-500 hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-800 transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="overflow-y-auto p-6 space-y-4 custom-scrollbar">
 
                {{-- Véhicule --}}
                <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50 flex items-start gap-3">
                    <x-marque-logo :marque="$detailIntervention->vehicle->marque" size="sm" />
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Véhicule</p>
                        <p class="font-semibold text-gray-100">{{ $detailIntervention->vehicle->marque }} {{ $detailIntervention->vehicle->modele }}</p>
                        <p class="text-sm text-gray-400">{{ $detailIntervention->vehicle->immatriculation }}</p>
                    </div>
                </div>
 
                {{-- Dates --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Date intervention</p>
                        <p class="font-semibold text-gray-200">{{ $detailIntervention->date_intervention->format('d/m/Y') }}</p>
                    </div>
                    @if($detailIntervention->date_prochaine)
                    <div class="bg-amber-500/10 rounded-xl p-4 border border-amber-500/20">
                        <p class="text-[10px] font-bold text-amber-400/80 uppercase tracking-widest mb-1">Prochaine intervention</p>
                        <p class="font-semibold text-amber-300">{{ $detailIntervention->date_prochaine->format('d/m/Y') }}</p>
                    </div>
                    @endif
                </div>
 
                {{-- Coût --}}
                <div class="bg-emerald-500/10 rounded-xl p-4 border border-emerald-500/20">
                    <p class="text-[10px] font-bold text-emerald-400/80 uppercase tracking-widest mb-1">Coût de l'opération</p>
                    <p class="font-bold text-2xl text-emerald-300">{{ number_format($detailIntervention->cout_total, 2, ',', ' ') }} <span class="text-base text-emerald-400/70">DH</span></p>
                </div>
 
                @if($detailIntervention->prestataire || $detailIntervention->numero_facture)
                <div class="grid grid-cols-2 gap-4">
                    @if($detailIntervention->prestataire)
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Prestataire</p>
                        <p class="font-semibold text-gray-200">{{ $detailIntervention->prestataire }}</p>
                    </div>
                    @endif
                    @if($detailIntervention->numero_facture)
                    <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">N° Facture</p>
                        <p class="font-semibold text-gray-200">{{ $detailIntervention->numero_facture }}</p>
                    </div>
                    @endif
                </div>
                @endif
 
                @if($detailIntervention->type === 'assurance')
                <div class="bg-emerald-500/5 rounded-2xl p-5 border border-emerald-500/20 space-y-3">
                    <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Assurance</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div><p class="text-[10px] text-gray-500 uppercase tracking-widest mb-0.5">Assureur</p><p class="text-sm font-semibold text-gray-200">{{ $detailIntervention->assureur }}</p></div>
                        <div><p class="text-[10px] text-gray-500 uppercase tracking-widest mb-0.5">N° Police</p><p class="text-sm font-semibold text-gray-200">{{ $detailIntervention->numero_police }}</p></div>
                        <div><p class="text-[10px] text-gray-500 uppercase tracking-widest mb-0.5">Expire le</p><p class="text-sm font-semibold text-emerald-300">{{ $detailIntervention->date_expiration_assurance?->format('d/m/Y') }}</p></div>
                    </div>
                </div>
                @endif
 
                @if($detailIntervention->type === 'controle_technique')
                <div class="bg-purple-500/5 rounded-2xl p-5 border border-purple-500/20 space-y-3">
                    <h4 class="text-xs font-bold text-purple-400 uppercase tracking-widest">Contrôle Technique</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div><p class="text-[10px] text-gray-500 uppercase tracking-widest mb-0.5">Résultat</p><p class="text-sm font-semibold text-purple-300">{{ $detailIntervention->resultat_ct_label }}</p></div>
                        <div><p class="text-[10px] text-gray-500 uppercase tracking-widest mb-0.5">Expire le</p><p class="text-sm font-semibold text-purple-300">{{ $detailIntervention->date_expiration_ct?->format('d/m/Y') }}</p></div>
                    </div>
                </div>
                @endif
 
                @if($detailIntervention->description)
                <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Description</p>
                    <p class="text-sm text-gray-300">{{ $detailIntervention->description }}</p>
                </div>
                @endif
 
                @if($detailIntervention->observations)
                <div class="bg-gray-800/60 rounded-xl p-4 border border-gray-700/50">
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Observations</p>
                    <p class="text-sm text-gray-300">{{ $detailIntervention->observations }}</p>
                </div>
                @endif
 
                <div class="flex items-center gap-3 pt-1">
                    <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 border border-gray-600/50 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Saisi par</p>
                        <p class="text-sm font-semibold text-gray-200">{{ $detailIntervention->user->name }} <span class="text-gray-500 font-normal">le {{ $detailIntervention->created_at->format('d/m/Y H:i') }}</span></p>
                    </div>
                </div>
 
            </div>
        </div>
    </div>
    @endif
 
</div>