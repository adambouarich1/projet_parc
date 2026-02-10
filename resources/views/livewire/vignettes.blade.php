<?php
    $roleBadges = [
        'admin' => 'bg-rose-500 text-white',
        'responsable_parc' => 'bg-indigo-500 text-white',
        'valideur' => 'bg-amber-500 text-white',
        'agent_saisie' => 'bg-emerald-500 text-white',
        'consultation' => 'bg-gray-500 text-white',
    ];

    $statutColors = [
        'active' => 'bg-emerald-500 text-white',
        'expiree' => 'bg-rose-500 text-white',
        'archivee' => 'bg-gray-500 text-white',
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
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Total vignettes</p>
            <p class="text-2xl font-bold text-indigo-400">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Actives</p>
            <p class="text-2xl font-bold text-emerald-400">{{ $stats['actives'] }}</p>
        </div>
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Expirées</p>
            <p class="text-2xl font-bold text-rose-500">{{ $stats['expirees'] }}</p>
        </div>
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Expire bientôt</p>
            <p class="text-2xl font-bold text-amber-400">{{ $stats['expire_bientot'] }}</p>
        </div>
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Total {{ date('Y') }}</p>
            <p class="text-2xl font-bold text-emerald-400">{{ number_format($stats['total_montant'], 2, ',', ' ') }} DH</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-4">
        {{-- Sidebar filtres --}}
        <div class="lg:w-1/4 space-y-4">
            <div class="bg-gray-900 shadow rounded-lg p-4 space-y-3 border border-gray-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-100">Filtres</h3>
                    <button type="button" wire:click="$set('filters', { vehicle_id: '', annee: '{{ date('Y') }}', statut: '', search: '' })" class="text-xs text-gray-400 hover:text-gray-200">
                        Réinitialiser
                    </button>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Recherche</label>
                    <input type="text" wire:model.live.debounce.300ms="filters.search" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Référence, immat...">
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Véhicule</label>
                    <select wire:model.live="filters.vehicle_id" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Tous</option>
                        @foreach ($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->immatriculation }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Année</label>
                    <select wire:model.live="filters.annee" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Toutes</option>
                        @foreach ($annees as $annee)
                            <option value="{{ $annee }}">{{ $annee }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Statut</label>
                    <select wire:model.live="filters.statut" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Tous</option>
                        @foreach ($statuts as $key => $label)
                            @if($key !== 'archivee')
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Bouton créer --}}
            @if(auth()->user()->canEdit())
            <button wire:click="openCreate" class="w-full px-4 py-2 text-sm font-medium rounded-md bg-indigo-600 hover:bg-indigo-500 text-white transition">
                + Nouvelle vignette
            </button>
            @endif
        </div>

        {{-- Liste des vignettes --}}
        <div class="lg:w-3/4">
            <div class="bg-gray-900 shadow rounded-lg border border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-800">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Véhicule</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Année</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Période</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Expiration</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Montant</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Statut</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse ($vignettes as $vignette)
                                @php
                                    $joursRestants = $vignette->jours_restants;
                                    $isExpired = $joursRestants < 0;
                                    $isExpiringSoon = !$isExpired && $joursRestants <= 30;
                                @endphp
                                <tr class="hover:bg-gray-800/50 transition {{ $isExpired ? 'bg-rose-900/20' : ($isExpiringSoon ? 'bg-amber-900/20' : '') }}">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="text-sm font-medium text-gray-100">{{ $vignette->vehicle->immatriculation }}</p>
                                        <p class="text-xs text-gray-400">{{ $vignette->vehicle->marque }} {{ $vignette->vehicle->modele }}</p>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 text-sm font-bold rounded-lg bg-indigo-500/20 text-indigo-400">
                                            {{ $vignette->annee }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="text-sm text-gray-300">{{ $vignette->date_debut->format('d/m/Y') }}</p>
                                        <p class="text-xs text-gray-500">au {{ $vignette->date_expiration->format('d/m/Y') }}</p>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="text-sm text-gray-300">{{ $vignette->date_expiration->format('d/m/Y') }}</p>
                                        <p class="text-xs {{ $isExpired ? 'text-rose-400' : ($isExpiringSoon ? 'text-amber-400' : 'text-gray-500') }}">
                                            @if($isExpired)
                                                {{ abs($joursRestants) }}j de retard
                                            @else
                                                {{ $joursRestants }}j restants
                                            @endif
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="text-sm font-medium text-emerald-400">{{ number_format($vignette->montant, 2, ',', ' ') }} DH</p>
                                        @if($vignette->reference_paiement)
                                            <p class="text-xs text-gray-500">Réf: {{ $vignette->reference_paiement }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statutColors[$vignette->statut] ?? 'bg-gray-500' }}">
                                            {{ $vignette->statut_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-2">
                                            <button wire:click="openDetails({{ $vignette->id }})" class="text-indigo-400 hover:text-indigo-300 text-sm" title="Détails">
                                                👁️
                                            </button>
                                            @if(auth()->user()->canEdit())
                                                <button wire:click="openEdit({{ $vignette->id }})" class="text-amber-400 hover:text-amber-300 text-sm" title="Modifier">
                                                    ✏️
                                                </button>
                                                <button wire:click="archive({{ $vignette->id }})" class="text-gray-400 hover:text-gray-300 text-sm" title="Archiver">
                                                    📦
                                                </button>
                                                <button wire:click="delete({{ $vignette->id }})" wire:confirm="Supprimer cette vignette ?" class="text-rose-400 hover:text-rose-300 text-sm" title="Supprimer">
                                                    🗑️
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                        Aucune vignette trouvée.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-4 py-3 border-t border-gray-800">
                    {{ $vignettes->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Formulaire --}}
    @if($showFormModal)
    <div class="fixed inset-0 bg-black/60 z-40 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-xl shadow-2xl max-w-lg w-full border border-gray-800">
            <div class="flex items-center justify-between p-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold text-gray-100">
                    {{ $editingId ? 'Modifier la vignette' : 'Nouvelle vignette' }}
                </h3>
                <button wire:click="closeModals" class="text-gray-400 hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save" class="p-4 space-y-4 max-h-[70vh] overflow-y-auto">
                {{-- Véhicule --}}
                <div class="space-y-2">
                    <label class="text-sm text-gray-400">Véhicule *</label>
                    <select wire:model="form.vehicle_id" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">-- Sélectionner --</option>
                        @foreach ($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->immatriculation }} - {{ $vehicle->marque }} {{ $vehicle->modele }}</option>
                        @endforeach
                    </select>
                    @error('form.vehicle_id') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                {{-- Année --}}
                <div class="space-y-2">
                    <label class="text-sm text-gray-400">Année *</label>
                    <select wire:model.live="form.annee" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        @for($y = date('Y') + 1; $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                    @error('form.annee') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm text-gray-400">Date début *</label>
                        <input type="date" wire:model="form.date_debut" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        @error('form.date_debut') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm text-gray-400">Date expiration *</label>
                        <input type="date" wire:model="form.date_expiration" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        @error('form.date_expiration') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Info dates auto --}}
                <div class="bg-indigo-900/30 border border-indigo-700 rounded-lg p-3">
                    <p class="text-xs text-indigo-300">
                        💡 Les dates sont automatiquement remplies selon l'année sélectionnée (01/01 au 31/12)
                    </p>
                </div>

                {{-- Montant et Référence --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm text-gray-400">Montant (DH) *</label>
                        <input type="number" step="0.01" wire:model="form.montant" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="0.00">
                        @error('form.montant') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm text-gray-400">Réf. paiement</label>
                        <input type="text" wire:model="form.reference_paiement" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Ex: REC-12345">
                    </div>
                </div>

                {{-- Date paiement --}}
                <div class="space-y-2">
                    <label class="text-sm text-gray-400">Date de paiement</label>
                    <input type="date" wire:model="form.date_paiement" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                </div>

                {{-- Statut --}}
                <div class="space-y-2">
                    <label class="text-sm text-gray-400">Statut</label>
                    <select wire:model="form.statut" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        @foreach ($statuts as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Observations --}}
                <div class="space-y-2">
                    <label class="text-sm text-gray-400">Observations</label>
                    <textarea wire:model="form.observations" rows="2" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Notes..."></textarea>
                </div>

                {{-- Boutons --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-800">
                    <button type="button" wire:click="closeModals" class="px-4 py-2 text-sm font-medium rounded-md bg-gray-700 text-gray-200 hover:bg-gray-600 transition">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium rounded-md bg-indigo-600 text-white hover:bg-indigo-500 transition">
                        {{ $editingId ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal Détails --}}
    @if($showDetailsModal && $detailVignette)
    <div class="fixed inset-0 bg-black/60 z-40 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-xl shadow-2xl max-w-lg w-full border border-gray-800">
            <div class="flex items-center justify-between p-4 border-b border-gray-800">
                <div>
                    <h3 class="text-lg font-semibold text-gray-100">Vignette {{ $detailVignette->annee }}</h3>
                    <p class="text-sm text-gray-400">{{ $detailVignette->vehicle->immatriculation }}</p>
                </div>
                <button wire:click="closeModals" class="text-gray-400 hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 space-y-4 max-h-[60vh] overflow-y-auto">
                {{-- Véhicule --}}
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Véhicule</p>
                    <p class="font-medium text-gray-100">{{ $detailVignette->vehicle->marque }} {{ $detailVignette->vehicle->modele }}</p>
                    <p class="text-sm text-indigo-400">{{ $detailVignette->vehicle->immatriculation }}</p>
                </div>

                {{-- Année et Période --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-indigo-900/30 rounded-lg p-3 border border-indigo-700">
                        <p class="text-xs text-indigo-300">Année</p>
                        <p class="text-2xl font-bold text-indigo-400">{{ $detailVignette->annee }}</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Période</p>
                        <p class="font-medium text-gray-100">{{ $detailVignette->date_debut->format('d/m/Y') }}</p>
                        <p class="text-sm text-gray-400">au {{ $detailVignette->date_expiration->format('d/m/Y') }}</p>
                    </div>
                </div>

                {{-- Expiration --}}
                @php
                    $joursRestants = $detailVignette->jours_restants;
                    $isExpired = $joursRestants < 0;
                @endphp
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700 {{ $isExpired ? 'border-rose-700 bg-rose-900/20' : '' }}">
                    <p class="text-xs text-gray-400">Expiration</p>
                    <p class="font-medium text-gray-100">{{ $detailVignette->date_expiration->format('d/m/Y') }}</p>
                    <p class="text-sm {{ $isExpired ? 'text-rose-400' : 'text-amber-400' }}">
                        {{ $isExpired ? abs($joursRestants) . ' jours de retard' : $joursRestants . ' jours restants' }}
                    </p>
                </div>

                {{-- Paiement --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-emerald-900/30 rounded-lg p-3 border border-emerald-700">
                        <p class="text-xs text-emerald-300">Montant</p>
                        <p class="text-xl font-bold text-emerald-400">{{ number_format($detailVignette->montant, 2, ',', ' ') }} DH</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Date paiement</p>
                        <p class="font-medium text-gray-100">{{ $detailVignette->date_paiement?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                </div>

                @if($detailVignette->reference_paiement)
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Référence paiement</p>
                    <p class="font-mono text-gray-100">{{ $detailVignette->reference_paiement }}</p>
                </div>
                @endif

                {{-- Statut --}}
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Statut</p>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statutColors[$detailVignette->statut] ?? 'bg-gray-500' }}">
                        {{ $detailVignette->statut_label }}
                    </span>
                </div>

                @if($detailVignette->observations)
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Observations</p>
                    <p class="text-gray-100">{{ $detailVignette->observations }}</p>
                </div>
                @endif

                {{-- Créé par --}}
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Créée par</p>
                    <p class="font-medium text-gray-100">{{ $detailVignette->user->name }}</p>
                    <p class="text-sm text-gray-400">{{ $detailVignette->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>