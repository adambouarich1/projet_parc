<?php
    $roleBadges = [
        'admin' => 'bg-rose-500 text-white',
        'responsable_parc' => 'bg-indigo-500 text-white',
        'valideur' => 'bg-amber-500 text-white',
        'agent_saisie' => 'bg-emerald-500 text-white',
        'consultation' => 'bg-gray-500 text-white',
    ];

    $tabIcons = [
        'missions' => '📋',
        'carburant' => '⛽',
        'interventions' => '🔧',
        'assurances' => '🛡️',
        'alertes' => '🔔',
        'vignettes' => '🏷️',
    ];

    $tabLabels = [
        'missions' => 'Ordres de Mission',
        'carburant' => 'Carburant',
        'interventions' => 'Interventions',
        'assurances' => 'Assurances',
        'alertes' => 'Alertes',
        'vignettes' => 'Vignettes',
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

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <span class="text-3xl">🗄️</span>
        <div>
            <h1 class="text-2xl font-bold text-white">Archives</h1>
            <p class="text-sm text-gray-400">Éléments archivés, traités ou clôturés</p>
        </div>
    </div>

    {{-- Stats globales --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        @foreach ($stats as $key => $count)
            <button 
                wire:click="setTab('{{ $key }}')"
                class="p-4 rounded-xl border transition-all {{ $activeTab === $key ? 'bg-indigo-600 border-indigo-500 shadow-lg shadow-indigo-500/20' : 'bg-gray-900 border-gray-800 hover:border-gray-700' }}"
            >
                <div class="flex items-center gap-2">
                    <span class="text-xl">{{ $tabIcons[$key] }}</span>
                    <div class="text-left">
                        <p class="text-2xl font-bold {{ $activeTab === $key ? 'text-white' : 'text-gray-100' }}">{{ $count }}</p>
                        <p class="text-xs {{ $activeTab === $key ? 'text-indigo-200' : 'text-gray-400' }}">{{ $tabLabels[$key] }}</p>
                    </div>
                </div>
            </button>
        @endforeach
    </div>

    {{-- Barre de recherche --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xl">{{ $tabIcons[$activeTab] }}</span>
                <h2 class="text-lg font-semibold text-white">{{ $tabLabels[$activeTab] }} archivés</h2>
            </div>
            <div class="w-full md:w-80">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    class="w-full rounded-lg border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400" 
                    placeholder="Rechercher..."
                >
            </div>
        </div>
    </div>

    {{-- Contenu selon l'onglet --}}
    <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
        
        {{-- Onglet Missions --}}
        @if($activeTab === 'missions')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Référence</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Objet</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Véhicule</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Conducteur</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Date</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse ($items as $item)
                            <tr wire:key="archive-mission-{{ $item->id }}" class="hover:bg-gray-800/50 transition">
                                <td class="px-4 py-3">
                                    <span class="font-mono text-sm text-indigo-400">{{ $item->reference }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-100 truncate max-w-xs">{{ $item->objet }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->destination }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-300">{{ $item->vehicle->immatriculation ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-300">{{ $item->driver ? $item->driver->nom . ' ' . $item->driver->prenom : '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-400">{{ $item->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if(auth()->user()->canEdit())
                                            <button wire:click="restore('mission', {{ $item->id }})" class="text-emerald-400 hover:text-emerald-300 text-sm" title="Restaurer">
                                                ♻️
                                            </button>
                                            <button
                                                @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { 
                                                    detail: { 
                                                        callback: () => $wire.deletePermanently('mission', {{ $item->id }}) 
                                                    } 
                                                }))"
                                                type="button"
                                                class="text-rose-400 hover:text-rose-300 text-sm" 
                                                title="Supprimer définitivement">
                                                🗑️
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    Aucun ordre de mission archivé
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Onglet Carburant --}}
        @if($activeTab === 'carburant')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Véhicule</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Station</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Quantité</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Montant</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse ($items as $item)
                            <tr wire:key="archive-carburant-{{ $item->id }}" class="hover:bg-gray-800/50 transition">
                                <td class="px-4 py-3 text-sm text-gray-300">{{ $item->date_plein->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-100">{{ $item->vehicle->immatriculation ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-300">{{ $item->station ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-300">{{ $item->quantite_litres }} L</td>
                                <td class="px-4 py-3 text-sm font-medium text-emerald-400">{{ number_format($item->montant_total, 2, ',', ' ') }} DH</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if(auth()->user()->canEdit())
                                            <button wire:click="restore('carburant', {{ $item->id }})" class="text-emerald-400 hover:text-emerald-300 text-sm" title="Restaurer">
                                                ♻️
                                            </button>
                                            <button
                                                @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { 
                                                    detail: { 
                                                        callback: () => $wire.deletePermanently('carburant', {{ $item->id }}) 
                                                    } 
                                                }))"
                                                type="button"
                                                class="text-rose-400 hover:text-rose-300 text-sm" 
                                                title="Supprimer définitivement">
                                                🗑️
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    Aucune entrée carburant archivée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Onglet Interventions --}}
        @if($activeTab === 'interventions')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Titre</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Véhicule</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Coût</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse ($items as $item)
                            <tr wire:key="archive-intervention-{{ $item->id }}" class="hover:bg-gray-800/50 transition">
                                <td class="px-4 py-3 text-sm text-gray-300">{{ $item->date_intervention->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-700 text-gray-200">
                                        {{ $item->type_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-100 truncate max-w-xs">{{ $item->titre }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-300">{{ $item->vehicle->immatriculation ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-emerald-400">{{ number_format($item->cout_total, 2, ',', ' ') }} DH</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if(auth()->user()->canEdit())
                                            <button wire:click="restore('intervention', {{ $item->id }})" class="text-emerald-400 hover:text-emerald-300 text-sm" title="Restaurer">
                                                ♻️
                                            </button>
                                            <button
                                                @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { 
                                                    detail: { 
                                                        callback: () => $wire.deletePermanently('intervention', {{ $item->id }}) 
                                                    } 
                                                }))"
                                                type="button"
                                                class="text-rose-400 hover:text-rose-300 text-sm" 
                                                title="Supprimer définitivement">
                                                🗑️
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    Aucune intervention archivée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Onglet Assurances --}}
        @if($activeTab === 'assurances')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Véhicule</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Assureur</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">N° Police</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Période</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Montant</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse ($items as $item)
                            <tr wire:key="archive-assurance-{{ $item->id }}" class="hover:bg-gray-800/50 transition">
                                <td class="px-4 py-3 text-sm text-gray-100">{{ $item->vehicle->immatriculation ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-300">{{ $item->assureur }}</td>
                                <td class="px-4 py-3 text-sm text-gray-400 font-mono">{{ $item->numero_police ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-300">
                                    {{ $item->date_debut->format('d/m/Y') }} → {{ $item->date_expiration->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-emerald-400">{{ number_format($item->montant, 2, ',', ' ') }} DH</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if(auth()->user()->canEdit())
                                            <button wire:click="restore('assurance', {{ $item->id }})" class="text-emerald-400 hover:text-emerald-300 text-sm" title="Restaurer">
                                                ♻️
                                            </button>
                                            <button
                                                @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { 
                                                    detail: { 
                                                        callback: () => $wire.deletePermanently('assurance', {{ $item->id }}) 
                                                    } 
                                                }))"
                                                type="button"
                                                class="text-rose-400 hover:text-rose-300 text-sm" 
                                                title="Supprimer définitivement">
                                                🗑️
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    Aucune assurance archivée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Onglet Alertes --}}
        @if($activeTab === 'alertes')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Titre</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Traité par</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Date</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse ($items as $item)
                            <tr wire:key="archive-alerte-{{ $item->id }}" class="hover:bg-gray-800/50 transition">
                                <td class="px-4 py-3 text-sm text-gray-300">{{ $item->type_label }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-100 truncate max-w-xs">{{ $item->titre }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $item->statut === 'traitee' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-gray-500/20 text-gray-400' }}">
                                        {{ $item->statut_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-300">{{ $item->treatedBy->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-400">{{ $item->treated_at ? $item->treated_at->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if(auth()->user()->canEdit())
                                            <button wire:click="restore('alerte', {{ $item->id }})" class="text-emerald-400 hover:text-emerald-300 text-sm" title="Restaurer">
                                                ♻️
                                            </button>
                                            <button
                                                @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { 
                                                    detail: { 
                                                        callback: () => $wire.deletePermanently('alerte', {{ $item->id }}) 
                                                    } 
                                                }))"
                                                type="button"
                                                class="text-rose-400 hover:text-rose-300 text-sm" 
                                                title="Supprimer définitivement">
                                                🗑️
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    Aucune alerte archivée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Onglet Vignettes --}}
        @if($activeTab === 'vignettes')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Véhicule</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Année</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Période</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Montant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase">Référence</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse ($items as $item)
                            <tr wire:key="archive-vignette-{{ $item->id }}" class="hover:bg-gray-800/50 transition">
                                <td class="px-4 py-3 text-sm text-gray-100">{{ $item->vehicle->immatriculation ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 text-sm font-bold rounded-lg bg-indigo-500/20 text-indigo-400">
                                        {{ $item->annee }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-300">
                                    {{ $item->date_debut->format('d/m/Y') }} → {{ $item->date_expiration->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-emerald-400">{{ number_format($item->montant, 2, ',', ' ') }} DH</td>
                                <td class="px-4 py-3 text-sm text-gray-400 font-mono">{{ $item->reference_paiement ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if(auth()->user()->canEdit())
                                            <button wire:click="restore('vignette', {{ $item->id }})" class="text-emerald-400 hover:text-emerald-300 text-sm" title="Restaurer">
                                                ♻️
                                            </button>
                                            <button
                                                @click="window.dispatchEvent(new CustomEvent('delete-confirmation', { 
                                                    detail: { 
                                                        callback: () => $wire.deletePermanently('vignette', {{ $item->id }}) 
                                                    } 
                                                }))"
                                                type="button"
                                                class="text-rose-400 hover:text-rose-300 text-sm" 
                                                title="Supprimer définitivement">
                                                🗑️
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    Aucune vignette archivée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-gray-800">
            {{ $items->links() }}
        </div>
    </div>
</div>