<?php
    $moisLabels = [
        1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',
        7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'
    ];
    $modules = [
        'missions'      => 'Missions',
        'carburant'     => 'Carburant',
        'interventions' => 'Interventions',
        'alertes'       => 'Alertes',
    ];
?>

<div class="space-y-5 text-gray-900 font-sans">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-green-700 tracking-tight">Archives</h2>
            <p class="text-sm text-gray-700 mt-0.5">Historique mensuel archivé automatiquement</p>
        </div>
    </div>

    @if(session()->has('status'))
    <div class="flex items-center gap-3 rounded-xl bg-green-50 border border-green-300 text-green-700 px-4 py-3">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-medium">{{ session('status') }}</p>
    </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-5 items-start">

        {{-- Sidebar --}}
        <div class="lg:w-52 shrink-0 w-full space-y-4">

            {{-- Modules --}}
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-lg">
                <h3 class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-3">Module</h3>
                <div class="flex flex-col gap-1.5">
                    @foreach($modules as $key => $label)
                    <button type="button" wire:click="setModule('{{ $key }}')"
                        class="w-full text-left px-3 py-2 rounded-lg text-sm font-semibold border transition-all
                            {{ $activeModule === $key
                                ? 'bg-green-100 text-indigo-200 border-indigo-500/40'
                                : 'bg-gray-50 text-gray-800 border-gray-200 hover:bg-gray-100 hover:text-gray-200' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Périodes --}}
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-lg">
                <h3 class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-3">Période</h3>
                @if($availablePeriods->isEmpty())
                    <p class="text-xs text-gray-800 italic">Aucune archive</p>
                @else
                <div class="flex flex-col gap-1">
                    @foreach($availablePeriods as $period)
                    <button type="button" wire:click="setPeriod({{ $period['month'] }}, {{ $period['year'] }})"
                        class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all
                            {{ $selectedMonth === $period['month'] && $selectedYear === $period['year']
                                ? 'bg-green-100 text-indigo-200 border-indigo-500/40'
                                : 'bg-gray-50 text-gray-800 border-gray-200 hover:bg-gray-100 hover:text-gray-200' }}">
                        {{ $moisLabels[$period['month']] }} {{ $period['year'] }}
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Contenu --}}
        <div class="flex-1 min-w-0 space-y-4">

            {{-- KPIs --}}
            @if($availablePeriods->isNotEmpty())
            @php
                $kpiLabels = match($activeModule) {
                    'carburant'     => ['nb_pleins'=>'Pleins','total_litres'=>'Litres','total_montant'=>'Dépense totale'],
                    'interventions' => ['nb_total'=>'Interventions','total_cout'=>'Coût total'],
                    'alertes'       => ['nb_total'=>'Total','nb_critique'=>'Critiques','nb_haute'=>'Hautes','nb_traitees'=>'Traitées'],
                    default         => ['nb_total'=>'Missions archivées','km_parcourus'=>'Km parcourus'],
                };
                $kpiFormats = match($activeModule) {
                    'carburant'     => ['nb_pleins'=>'int','total_litres'=>'litres','total_montant'=>'money'],
                    'interventions' => ['nb_total'=>'int','total_cout'=>'money'],
                    'alertes'       => ['nb_total'=>'int','nb_critique'=>'int','nb_haute'=>'int','nb_traitees'=>'int'],
                    default         => ['nb_total'=>'int','km_parcourus'=>'km'],
                };
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-{{ min(count($kpis), 4) }} gap-3">
                @foreach($kpis as $key => $value)
                <div class="bg-white rounded-xl p-4 border border-gray-200">
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">{{ $kpiLabels[$key] ?? $key }}</p>
                    @php $fmt = $kpiFormats[$key] ?? 'int'; @endphp
                    <p class="text-xl font-bold text-gray-900">
                        @if($fmt === 'money') {{ number_format($value, 2, ',', ' ') }} <span class="text-sm font-normal text-gray-800">DH</span>
                        @elseif($fmt === 'litres') {{ number_format($value, 2, ',', ' ') }} <span class="text-sm font-normal text-gray-800">L</span>
                        @elseif($fmt === 'km') {{ number_format($value, 0, ',', ' ') }} <span class="text-sm font-normal text-gray-800">km</span>
                        @else {{ number_format($value, 0, ',', ' ') }}
                        @endif
                    </p>
                    <p class="text-[10px] text-gray-800 mt-1">{{ $moisLabels[$selectedMonth] }} {{ $selectedYear }}</p>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Liste --}}
            @if($availablePeriods->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 bg-gray-50 rounded-2xl border border-gray-200">
                <svg class="w-14 h-14 mb-4 opacity-20 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                <p class="text-base font-semibold text-gray-800">Aucune archive pour ce module.</p>
                <p class="text-sm text-gray-800 mt-1">Les éléments archivés apparaîtront ici.</p>
            </div>
            @elseif($items->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 bg-gray-50 rounded-2xl border border-gray-200">
                <p class="text-base font-semibold text-gray-800">Aucun élément pour {{ $moisLabels[$selectedMonth] }} {{ $selectedYear }}.</p>
            </div>
            @else
            <div class="space-y-2">
                @foreach($items as $item)
                <div wire:key="arch-{{ $item->id }}" class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:border-gray-600/70 transition-colors">
                    <div class="flex items-center gap-4 px-5 py-4">
                        <div class="flex-1 min-w-0 grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-1">

                            @if($activeModule === 'missions')
                            <div>
                                <span class="font-mono text-xs text-green-700 bg-green-700/10 px-2 py-0.5 rounded border border-indigo-500/20">{{ $item->reference }}</span>
                                <p class="font-semibold text-gray-900 mt-1.5 truncate">{{ $item->objet }}</p>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <x-marque-logo :marque="$item->vehicle->marque" size="sm" />
                                <div>
                                    <p class="text-white font-medium text-xs">{{ $item->driver->prenom }} {{ $item->driver->nom }}</p>
                                    <p class="text-xs">{{ $item->vehicle->immatriculation }}</p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-700">
                                <p>Dép. {{ $item->date_depart->format('d/m/Y') }}</p>
                                <p class="text-xs">Ret. {{ $item->date_retour_prevue->format('d/m/Y') }}</p>
                            </div>

                            @elseif($activeModule === 'carburant')
                            <div class="flex items-center gap-2">
                                <x-marque-logo :marque="$item->vehicle->marque" size="sm" />
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $item->vehicle->immatriculation }}</p>
                                    <p class="text-xs text-gray-700">{{ $item->vehicle->marque }} {{ $item->vehicle->modele }}</p>
                                </div>
                            </div>
                            <div class="text-sm">
                                <p class="text-white font-semibold">{{ number_format($item->quantite_litres, 2, ',', ' ') }} L &mdash; {{ number_format($item->montant_total, 2, ',', ' ') }} DH</p>
                                <p class="text-xs text-gray-700">{{ $item->date_plein->format('d/m/Y') }} &middot; {{ $item->station ?? 'N/A' }}</p>
                            </div>
                            <div class="text-sm text-gray-700">
                                <p>{{ $item->type_carburant ?? '—' }}</p>
                                <p class="text-xs">{{ $item->kilometrage ? number_format($item->kilometrage,0,',',' ').' km' : '—' }}</p>
                            </div>

                            @elseif($activeModule === 'interventions')
                            <div>
                                <p class="font-semibold text-gray-900 truncate">{{ $item->titre }}</p>
                                <p class="text-xs text-gray-700 mt-0.5">{{ $item->type_label }}</p>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <x-marque-logo :marque="$item->vehicle->marque" size="sm" />
                                <div>
                                    <p class="text-white text-xs font-medium">{{ $item->vehicle->immatriculation }}</p>
                                    <p class="text-xs">{{ $item->date_intervention->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="text-sm">
                                <p class="font-bold text-gray-900">{{ number_format($item->cout_total,2,',',' ') }} DH</p>
                                <p class="text-xs text-gray-700">{{ $item->prestataire ?? '—' }}</p>
                            </div>

                            @elseif($activeModule === 'alertes')
                            <div>
                                <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full border font-semibold
                                    {{ $item->priorite === 'critique' ? 'bg-red-50 text-red-700 border-red-200' :
                                      ($item->priorite === 'haute' ? 'bg-orange-500/20 text-orange-300 border-orange-500/30' :
                                      ($item->priorite === 'moyenne' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-gray-500/20 text-gray-800 border-gray-500/30')) }}">
                                    {{ ucfirst($item->priorite) }}
                                </span>
                                <p class="font-semibold text-gray-900 mt-1.5 truncate">{{ $item->titre }}</p>
                            </div>
                            <div class="text-sm text-gray-700">
                                <p>{{ $item->type_label }}</p>
                            </div>
                            <div class="text-sm text-gray-700">
                                <p>{{ $item->created_at->format('d/m/Y') }}</p>
                                @if($item->treatedBy)<p class="text-xs">Traité par {{ $item->treatedBy->name }}</p>@endif
                            </div>
                            @endif

                        </div>

                        <button type="button" wire:click="openDetail({{ $item->id }})"
                            class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all bg-gray-100 text-gray-800 border-gray-300 hover:bg-green-100 hover:text-indigo-200 hover:border-indigo-500/40">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Voir
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="pt-2">{{ $items->links() }}</div>
            @endif

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MODAL DÉTAIL                                                  --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if($detailItem)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data
         x-init="document.body.classList.add('overflow-hidden')"
         x-destroy="document.body.classList.remove('overflow-hidden')">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-gray-50/80 backdrop-blur-sm"
             wire:click="openDetail({{ $detailId }})"></div>

        {{-- Panel --}}
        <div class="relative z-10 w-full max-w-2xl max-h-[90vh] flex flex-col bg-gray-900 border border-gray-200/60 rounded-2xl shadow-2xl overflow-hidden">

            {{-- Header modal --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-green-700/15 border border-indigo-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-700 uppercase tracking-widest">Détail archivé — {{ $modules[$activeModule] ?? '' }}</p>
                        <p class="text-sm font-semibold text-gray-200 leading-tight mt-0.5">
                            @if($activeModule === 'missions') {{ $detailItem->reference }}
                            @elseif($activeModule === 'carburant') {{ $detailItem->vehicle->immatriculation }}
                            @elseif($activeModule === 'interventions') {{ $detailItem->titre }}
                            @elseif($activeModule === 'alertes') {{ $detailItem->titre }}
                            @endif
                        </p>
                    </div>
                </div>
                <button type="button" wire:click="openDetail({{ $detailId }})"
                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-800 border border-gray-200 text-gray-800 hover:text-white hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Corps scrollable --}}
            <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">

                @if($activeModule === 'missions')
                {{-- Titre mission --}}
                <div>
                    <p class="text-xl font-bold text-gray-900 leading-tight">{{ $detailItem->objet }}</p>
                    <span class="font-mono text-xs text-green-700 bg-green-700/10 px-2 py-0.5 rounded border border-indigo-500/20 mt-1 inline-block">{{ $detailItem->reference }}</span>
                </div>

                {{-- Véhicule & conducteur --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200 flex items-center gap-3">
                        <x-marque-logo :marque="$detailItem->vehicle->marque" size="md" />
                        <div>
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest">Véhicule</p>
                            <p class="text-sm font-bold text-gray-900">{{ $detailItem->vehicle->immatriculation }}</p>
                            <p class="text-xs text-gray-700">{{ $detailItem->vehicle->marque }} {{ $detailItem->vehicle->modele }}</p>
                        </div>
                    </div>
                    <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200 flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest">Conducteur</p>
                            <p class="text-sm font-bold text-gray-900">{{ $detailItem->driver->prenom }} {{ $detailItem->driver->nom }}</p>
                        </div>
                    </div>
                </div>

                {{-- Trajet --}}
                <div>
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-2">Trajet</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Lieu de départ</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $detailItem->lieu_depart }}</p>
                        </div>
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Destination</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $detailItem->destination }}</p>
                        </div>
                    </div>
                </div>

                {{-- Dates & km --}}
                <div>
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-2">Dates & kilométrage</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Départ réel</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $detailItem->started_at?->format('d/m/Y H:i') ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Retour réel</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $detailItem->date_retour_effective?->format('d/m/Y H:i') ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Km départ → retour</p>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $detailItem->km_depart ? number_format($detailItem->km_depart,0,',',' ') : '—' }}
                                <span class="text-gray-700">→</span>
                                {{ $detailItem->km_retour ? number_format($detailItem->km_retour,0,',',' ') : '—' }}
                            </p>
                        </div>
                        <div class="bg-green-700/10 rounded-xl px-4 py-3 border border-indigo-500/30">
                            <p class="text-[10px] font-bold text-green-700 uppercase tracking-widest mb-1">Distance</p>
                            <p class="text-sm font-bold text-green-700">{{ $detailItem->km_parcourus ? number_format($detailItem->km_parcourus,0,',',' ').' km' : '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Acteurs --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                        <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Créé par</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $detailItem->user?->name ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                        <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Validé par</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $detailItem->validator?->name ?? '—' }}</p>
                    </div>
                </div>

                @if($detailItem->description)
                <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Description</p>
                    <p class="text-sm text-gray-800 leading-relaxed">{{ $detailItem->description }}</p>
                </div>
                @endif
                @if($detailItem->observations)
                <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Observations</p>
                    <p class="text-sm text-gray-800 leading-relaxed">{{ $detailItem->observations }}</p>
                </div>
                @endif

                @elseif($activeModule === 'carburant')
                {{-- Véhicule header --}}
                <div class="flex items-center gap-4">
                    <x-marque-logo :marque="$detailItem->vehicle->marque" size="lg" />
                    <div>
                        <p class="text-xl font-bold text-gray-900">{{ $detailItem->vehicle->immatriculation }}</p>
                        <p class="text-sm text-gray-700">{{ $detailItem->vehicle->marque }} {{ $detailItem->vehicle->modele }}</p>
                    </div>
                </div>

                {{-- Plein --}}
                <div>
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-2">Détails du plein</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <div class="bg-emerald-500/10 rounded-xl px-4 py-3 border border-emerald-500/20">
                            <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-1">Quantité</p>
                            <p class="text-sm font-bold text-emerald-300">{{ number_format($detailItem->quantite_litres,2,',',' ') }} L</p>
                        </div>
                        <div class="bg-amber-500/10 rounded-xl px-4 py-3 border border-amber-500/20">
                            <p class="text-[10px] font-bold text-amber-400 uppercase tracking-widest mb-1">Montant</p>
                            <p class="text-sm font-bold text-amber-300">{{ number_format($detailItem->montant_total,2,',',' ') }} DH</p>
                        </div>
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Prix / L</p>
                            <p class="text-sm font-semibold text-gray-900">{{ number_format($detailItem->prix_unitaire,2,',',' ') }} DH</p>
                        </div>
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Type</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $detailItem->type_carburant ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Station --}}
                <div>
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-2">Station & traçabilité</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Station</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $detailItem->station ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">N° bon</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $detailItem->numero_bon ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Kilométrage</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $detailItem->kilometrage ? number_format($detailItem->kilometrage,0,',',' ').' km' : '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Conducteur & saisie --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                        <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Conducteur</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $detailItem->driver ? $detailItem->driver->prenom.' '.$detailItem->driver->nom : '—' }}</p>
                    </div>
                    <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                        <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Saisi par</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $detailItem->user?->name ?? '—' }}</p>
                    </div>
                </div>

                @if($detailItem->observations)
                <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Observations</p>
                    <p class="text-sm text-gray-800 leading-relaxed">{{ $detailItem->observations }}</p>
                </div>
                @endif

                @elseif($activeModule === 'interventions')
                {{-- Titre --}}
                <div>
                    <p class="text-xl font-bold text-gray-900 leading-tight">{{ $detailItem->titre }}</p>
                    <span class="inline-flex items-center mt-1 text-xs px-2 py-0.5 rounded-full border font-semibold bg-blue-500/10 text-blue-300 border-blue-500/20">{{ $detailItem->type_label }}</span>
                </div>

                {{-- Véhicule --}}
                <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200 flex items-center gap-3">
                    <x-marque-logo :marque="$detailItem->vehicle->marque" size="md" />
                    <div>
                        <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest">Véhicule</p>
                        <p class="text-sm font-bold text-gray-900">{{ $detailItem->vehicle->immatriculation }}</p>
                        <p class="text-xs text-gray-700">{{ $detailItem->vehicle->marque }} {{ $detailItem->vehicle->modele }}</p>
                    </div>
                </div>

                {{-- Détails --}}
                <div>
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-2">Détails</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Date</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $detailItem->date_intervention->format('d/m/Y') }}</p>
                        </div>
                        <div class="bg-amber-500/10 rounded-xl px-4 py-3 border border-amber-500/20">
                            <p class="text-[10px] font-bold text-amber-400 uppercase tracking-widest mb-1">Coût total</p>
                            <p class="text-sm font-bold text-amber-300">{{ number_format($detailItem->cout_total,2,',',' ') }} DH</p>
                        </div>
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Prestataire</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $detailItem->prestataire ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">N° facture</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $detailItem->numero_facture ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Saisi par</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $detailItem->user?->name ?? '—' }}</p>
                </div>

                @if($detailItem->observations)
                <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Observations</p>
                    <p class="text-sm text-gray-800 leading-relaxed">{{ $detailItem->observations }}</p>
                </div>
                @endif

                @elseif($activeModule === 'alertes')
                {{-- Header alerte --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-xl font-bold text-gray-900 leading-tight">{{ $detailItem->titre }}</p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full border font-semibold
                                {{ $detailItem->priorite === 'critique' ? 'bg-red-50 text-red-700 border-red-200' :
                                  ($detailItem->priorite === 'haute' ? 'bg-orange-500/20 text-orange-300 border-orange-500/30' :
                                  ($detailItem->priorite === 'moyenne' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-gray-500/20 text-gray-800 border-gray-500/30')) }}">
                                {{ ucfirst($detailItem->priorite) }}
                            </span>
                            <span class="text-xs text-gray-700">{{ $detailItem->type_label }}</span>
                        </div>
                    </div>
                </div>

                {{-- Message --}}
                <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Message</p>
                    <p class="text-sm text-gray-200 leading-relaxed">{{ $detailItem->message }}</p>
                </div>

                {{-- Traitement --}}
                <div>
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-2">Traitement</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Échéance</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $detailItem->date_echeance?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Traité par</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $detailItem->treatedBy?->name ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Traité le</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $detailItem->treated_at?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                @if($detailItem->notes_traitement)
                <div class="bg-gray-800/70 rounded-xl px-4 py-3 border border-gray-200">
                    <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1">Notes de traitement</p>
                    <p class="text-sm text-gray-800 leading-relaxed">{{ $detailItem->notes_traitement }}</p>
                </div>
                @endif
                @endif

            </div>

            {{-- Footer archivage --}}
            <div class="shrink-0 flex items-center gap-2.5 px-6 py-3 border-t border-gray-200 bg-gray-100">
                <div class="w-6 h-6 rounded-md bg-green-700/10 border border-indigo-500/20 flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
                <p class="text-xs text-gray-700">
                    Archivé automatiquement par le système
                    @if($detailItem->archived_at)
                        le <span class="text-gray-800 font-semibold">{{ $detailItem->archived_at->format('d/m/Y à H:i') }}</span>
                    @endif
                </p>
            </div>

        </div>
    </div>
    @endif

</div>
