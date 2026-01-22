<?php
    $roleBadges = [
        'admin' => 'bg-rose-500 text-white',
        'responsable_parc' => 'bg-indigo-500 text-white',
        'valideur' => 'bg-amber-500 text-white',
        'agent_saisie' => 'bg-emerald-500 text-white',
        'consultation' => 'bg-gray-500 text-white',
    ];

    $prioriteColors = [
        'basse' => 'bg-gray-500 text-white',
        'moyenne' => 'bg-blue-500 text-white',
        'haute' => 'bg-amber-500 text-white',
        'critique' => 'bg-rose-600 text-white animate-pulse',
    ];

    $statutColors = [
        'active' => 'bg-rose-500 text-white',
        'vue' => 'bg-amber-500 text-white',
        'traitee' => 'bg-emerald-500 text-white',
        'ignoree' => 'bg-gray-500 text-white',
    ];

    $typeIcons = [
        'permis_expire' => '🪪',
        'permis_bientot' => '🪪',
        'assurance_expiree' => '📋',
        'assurance_bientot' => '📋',
        'ct_expire' => '🔧',
        'ct_bientot' => '🔧',
        'vidange_km' => '🛢️',
        'vidange_date' => '🛢️',
        'vignette_expiree' => '🏷️',
        'vignette_bientot' => '🏷️',
        'autre' => '⚠️',
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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Total alertes</p>
            <p class="text-2xl font-bold text-indigo-400">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Alertes actives</p>
            <p class="text-2xl font-bold text-amber-400">{{ $stats['actives'] }}</p>
        </div>
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Critiques</p>
            <p class="text-2xl font-bold text-rose-500">{{ $stats['critiques'] }}</p>
        </div>
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Priorité haute</p>
            <p class="text-2xl font-bold text-orange-400">{{ $stats['hautes'] }}</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-4">
        {{-- Sidebar filtres --}}
        <div class="lg:w-1/4 space-y-4">
            <div class="bg-gray-900 shadow rounded-lg p-4 space-y-3 border border-gray-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-100">Filtres</h3>
                    <button type="button" wire:click="$set('filters', { type: '', priorite: '', statut: '', entity_type: '' })" class="text-xs text-gray-400 hover:text-gray-200">
                        Réinitialiser
                    </button>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Type</label>
                    <select wire:model.live="filters.type" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Tous</option>
                        @foreach ($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Priorité</label>
                    <select wire:model.live="filters.priorite" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Toutes</option>
                        @foreach ($priorites as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
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
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Entité</label>
                    <select wire:model.live="filters.entity_type" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Toutes</option>
                        <option value="vehicle">Véhicules</option>
                        <option value="driver">Conducteurs</option>
                    </select>
                </div>
            </div>

            {{-- Actions rapides --}}
            @if(auth()->user()->canEdit())
            <div class="bg-gray-900 shadow rounded-lg p-4 space-y-3 border border-gray-800">
                <h3 class="text-sm font-semibold text-gray-100">Actions</h3>
                <button wire:click="refreshAlerts" wire:loading.attr="disabled" class="w-full px-3 py-2 text-sm font-medium rounded-md bg-indigo-600 hover:bg-indigo-500 text-white transition">
                    <span wire:loading.remove wire:target="refreshAlerts">🔄 Actualiser les alertes</span>
                    <span wire:loading wire:target="refreshAlerts">Analyse en cours...</span>
                </button>
                <button wire:click="markAllAsViewed" class="w-full px-3 py-2 text-sm font-medium rounded-md bg-gray-700 hover:bg-gray-600 text-white transition">
                    ✓ Tout marquer comme vu
                </button>
            </div>
            @endif
        </div>

        {{-- Liste des alertes --}}
        <div class="lg:w-3/4">
            <div class="bg-gray-900 shadow rounded-lg border border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-800">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Priorité</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Titre</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Entité</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Échéance</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Statut</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse ($alerts as $alert)
                                <tr class="hover:bg-gray-800/50 transition {{ $alert->priorite === 'critique' ? 'bg-rose-900/20' : '' }}">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="text-lg" title="{{ $alert->type_label }}">{{ $typeIcons[$alert->type] ?? '⚠️' }}</span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $prioriteColors[$alert->priorite] ?? 'bg-gray-500' }}">
                                            {{ $alert->priorite_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-sm font-medium text-gray-100 truncate max-w-xs" title="{{ $alert->titre }}">{{ $alert->titre }}</p>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="text-sm text-gray-300">{{ $this->getEntityName($alert->alertable) }}</p>
                                        <p class="text-xs text-gray-500">{{ $this->getEntityType($alert->alertable_type) }}</p>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($alert->date_echeance)
                                            <p class="text-sm text-gray-300">{{ $alert->date_echeance->format('d/m/Y') }}</p>
                                            @if($alert->jours_restants !== null)
                                                <p class="text-xs {{ $alert->jours_restants < 0 ? 'text-rose-400' : ($alert->jours_restants <= 7 ? 'text-amber-400' : 'text-gray-500') }}">
                                                    {{ $alert->jours_restants < 0 ? abs($alert->jours_restants) . 'j retard' : $alert->jours_restants . 'j restants' }}
                                                </p>
                                            @endif
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statutColors[$alert->statut] ?? 'bg-gray-500' }}">
                                            {{ $alert->statut_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-2">
                                            <button wire:click="openDetails({{ $alert->id }})" class="text-indigo-400 hover:text-indigo-300 text-sm" title="Détails">
                                                👁️
                                            </button>
                                            @if(auth()->user()->canEdit() && in_array($alert->statut, ['active', 'vue']))
                                                <button wire:click="openTraitement({{ $alert->id }})" class="text-emerald-400 hover:text-emerald-300 text-sm" title="Marquer traitée">
                                                    ✓
                                                </button>
                                                <button wire:click="ignoreAlert({{ $alert->id }})" class="text-gray-400 hover:text-gray-300 text-sm" title="Ignorer">
                                                    ✕
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                        Aucune alerte. Cliquez sur "Actualiser les alertes" pour scanner le système.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-4 py-3 border-t border-gray-800">
                    {{ $alerts->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Détails --}}
    @if($showDetailsModal && $detailAlert)
    <div class="fixed inset-0 bg-black/60 z-40 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-xl shadow-2xl max-w-lg w-full border border-gray-800">
            <div class="flex items-center justify-between p-4 border-b border-gray-800">
                <div>
                    <h3 class="text-lg font-semibold text-gray-100">Détails de l'alerte</h3>
                    <div class="flex gap-2 mt-1">
                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $prioriteColors[$detailAlert->priorite] ?? 'bg-gray-500' }}">
                            {{ $detailAlert->priorite_label }}
                        </span>
                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $statutColors[$detailAlert->statut] ?? 'bg-gray-500' }}">
                            {{ $detailAlert->statut_label }}
                        </span>
                    </div>
                </div>
                <button wire:click="closeDetails" class="text-gray-400 hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 space-y-4 max-h-[60vh] overflow-y-auto">
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Type</p>
                    <p class="font-medium text-gray-100">{{ $typeIcons[$detailAlert->type] ?? '⚠️' }} {{ $detailAlert->type_label }}</p>
                </div>

                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Titre</p>
                    <p class="font-medium text-gray-100">{{ $detailAlert->titre }}</p>
                </div>

                @if($detailAlert->message)
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Message</p>
                    <p class="text-gray-100">{{ $detailAlert->message }}</p>
                </div>
                @endif

                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Entité concernée</p>
                    <p class="font-medium text-gray-100">{{ $this->getEntityName($detailAlert->alertable) }}</p>
                    <p class="text-sm text-gray-400">{{ $this->getEntityType($detailAlert->alertable_type) }}</p>
                </div>

                @if($detailAlert->date_echeance)
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Date d'échéance</p>
                    <p class="font-medium text-gray-100">{{ $detailAlert->date_echeance->format('d/m/Y') }}</p>
                    @if($detailAlert->jours_restants !== null)
                        <p class="text-sm {{ $detailAlert->jours_restants < 0 ? 'text-rose-400' : 'text-amber-400' }}">
                            {{ $detailAlert->jours_restants < 0 ? abs($detailAlert->jours_restants) . ' jours de retard' : $detailAlert->jours_restants . ' jours restants' }}
                        </p>
                    @endif
                </div>
                @endif

                @if($detailAlert->statut === 'traitee' && $detailAlert->treatedBy)
                <div class="bg-emerald-900/30 rounded-lg p-3 border border-emerald-700">
                    <p class="text-xs text-emerald-400">Traitée par</p>
                    <p class="font-medium text-emerald-100">{{ $detailAlert->treatedBy->name }}</p>
                    <p class="text-sm text-emerald-300">{{ $detailAlert->treated_at->format('d/m/Y H:i') }}</p>
                    @if($detailAlert->notes_traitement)
                        <p class="mt-2 text-sm text-gray-300">{{ $detailAlert->notes_traitement }}</p>
                    @endif
                </div>
                @endif

                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Créée le</p>
                    <p class="text-gray-100">{{ $detailAlert->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Traitement --}}
    @if($showTraitementModal)
    <div class="fixed inset-0 bg-black/60 z-40 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-xl shadow-2xl max-w-md w-full border border-gray-800">
            <div class="flex items-center justify-between p-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold text-gray-100">Marquer comme traitée</h3>
                <button wire:click="closeTraitement" class="text-gray-400 hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 space-y-4">
                <div class="space-y-2">
                    <label class="text-sm text-gray-400">Notes (optionnel)</label>
                    <textarea wire:model="notesTraitement" rows="3" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Décrivez les actions effectuées..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button wire:click="closeTraitement" class="px-4 py-2 text-sm font-medium rounded-md bg-gray-700 text-gray-200 hover:bg-gray-600 transition">
                        Annuler
                    </button>
                    <button wire:click="markAsTreated" class="px-4 py-2 text-sm font-medium rounded-md bg-emerald-600 text-white hover:bg-emerald-500 transition">
                        Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>