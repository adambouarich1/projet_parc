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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Total assurances</p>
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
    </div>

    <div class="flex flex-col lg:flex-row gap-4">
        {{-- Sidebar filtres --}}
        <div class="lg:w-1/4 space-y-4">
            <div class="bg-gray-900 shadow rounded-lg p-4 space-y-3 border border-gray-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-100">Filtres</h3>
                    <button type="button" wire:click="$set('filters', { vehicle_id: '', statut: '', search: '' })" class="text-xs text-gray-400 hover:text-gray-200">
                        Réinitialiser
                    </button>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Recherche</label>
                    <input type="text" wire:model.live.debounce.300ms="filters.search" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Assureur, police, immat...">
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
                + Nouvelle assurance
            </button>
            @endif
        </div>

        {{-- Liste des assurances --}}
        <div class="lg:w-3/4">
            <div class="bg-gray-900 shadow rounded-lg border border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-800">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Véhicule</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Assureur</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Période</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Expiration</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Montant</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Statut</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse ($insurances as $insurance)
                                @php
                                    $joursRestants = $insurance->jours_restants;
                                    $isExpired = $joursRestants < 0;
                                    $isExpiringSoon = !$isExpired && $joursRestants <= 30;
                                @endphp
                                <tr class="hover:bg-gray-800/50 transition {{ $isExpired ? 'bg-rose-900/20' : ($isExpiringSoon ? 'bg-amber-900/20' : '') }}">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="text-sm font-medium text-gray-100">{{ $insurance->vehicle->immatriculation }}</p>
                                        <p class="text-xs text-gray-400">{{ $insurance->vehicle->marque }} {{ $insurance->vehicle->modele }}</p>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="text-sm font-medium text-gray-100">{{ $insurance->assureur }}</p>
                                        @if($insurance->numero_police)
                                            <p class="text-xs text-gray-400">N° {{ $insurance->numero_police }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="text-sm text-gray-300">{{ $insurance->date_debut->format('d/m/Y') }}</p>
                                        <p class="text-xs text-gray-400">{{ $insurance->duree_label }}</p>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="text-sm text-gray-300">{{ $insurance->date_expiration->format('d/m/Y') }}</p>
                                        <p class="text-xs {{ $isExpired ? 'text-rose-400' : ($isExpiringSoon ? 'text-amber-400' : 'text-gray-500') }}">
                                            @if($isExpired)
                                                {{ abs($joursRestants) }}j de retard
                                            @else
                                                {{ $joursRestants }}j restants
                                            @endif
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="text-sm font-medium text-gray-100">{{ number_format($insurance->montant, 2, ',', ' ') }} DH</p>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statutColors[$insurance->statut] ?? 'bg-gray-500' }}">
                                            {{ $insurance->statut_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-2">
                                            <button wire:click="openDetails({{ $insurance->id }})" class="text-indigo-400 hover:text-indigo-300 text-sm" title="Détails">
                                                👁️
                                            </button>
                                            @if(auth()->user()->canEdit())
                                                <button wire:click="openEdit({{ $insurance->id }})" class="text-amber-400 hover:text-amber-300 text-sm" title="Modifier">
                                                    ✏️
                                                </button>
                                                <button wire:click="archive({{ $insurance->id }})" class="text-gray-400 hover:text-gray-300 text-sm" title="Archiver">
                                                    📦
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                        Aucune assurance trouvée.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-4 py-3 border-t border-gray-800">
                    {{ $insurances->links() }}
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
                    {{ $editingId ? 'Modifier l\'assurance' : 'Nouvelle assurance' }}
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

                {{-- Assureur --}}
                <div class="space-y-2">
                    <label class="text-sm text-gray-400">Assureur *</label>
                    <input type="text" wire:model="form.assureur" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Ex: Wafa Assurance, AXA...">
                    @error('form.assureur') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                {{-- Numéro police --}}
                <div class="space-y-2">
                    <label class="text-sm text-gray-400">Numéro de police</label>
                    <input type="text" wire:model="form.numero_police" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Ex: POL-123456">
                </div>

                {{-- Date début + Durée --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm text-gray-400">Date début *</label>
                        <input type="date" wire:model="form.date_debut" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        @error('form.date_debut') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm text-gray-400">Durée *</label>
                        <select wire:model="form.duree_mois" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            @foreach ($durees as $mois => $label)
                                <option value="{{ $mois }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Info calcul auto --}}
                <div class="bg-indigo-900/30 border border-indigo-700 rounded-lg p-3">
                    <p class="text-xs text-indigo-300">
                        💡 La date d'expiration sera calculée automatiquement (date début + durée)
                    </p>
                </div>

                {{-- Montant --}}
                <div class="space-y-2">
                    <label class="text-sm text-gray-400">Montant (DH)</label>
                    <input type="number" step="0.01" wire:model="form.montant" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="0.00">
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
    @if($showDetailsModal && $detailInsurance)
    <div class="fixed inset-0 bg-black/60 z-40 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-xl shadow-2xl max-w-lg w-full border border-gray-800">
            <div class="flex items-center justify-between p-4 border-b border-gray-800">
                <div>
                    <h3 class="text-lg font-semibold text-gray-100">Détails de l'assurance</h3>
                    <p class="text-sm text-gray-400">{{ $detailInsurance->vehicle->immatriculation }}</p>
                </div>
                <button wire:click="closeModals" class="text-gray-400 hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 space-y-4 max-h-[60vh] overflow-y-auto">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Assureur</p>
                        <p class="font-medium text-gray-100">{{ $detailInsurance->assureur }}</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">N° Police</p>
                        <p class="font-medium text-gray-100">{{ $detailInsurance->numero_police ?? '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Date début</p>
                        <p class="font-medium text-gray-100">{{ $detailInsurance->date_debut->format('d/m/Y') }}</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Durée</p>
                        <p class="font-medium text-gray-100">{{ $detailInsurance->duree_label }}</p>
                    </div>
                </div>

                @php
                    $joursRestants = $detailInsurance->jours_restants;
                    $isExpired = $joursRestants < 0;
                @endphp
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700 {{ $isExpired ? 'border-rose-700 bg-rose-900/20' : '' }}">
                    <p class="text-xs text-gray-400">Date d'expiration</p>
                    <p class="font-medium text-gray-100">{{ $detailInsurance->date_expiration->format('d/m/Y') }}</p>
                    <p class="text-sm {{ $isExpired ? 'text-rose-400' : 'text-amber-400' }}">
                        {{ $isExpired ? abs($joursRestants) . ' jours de retard' : $joursRestants . ' jours restants' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Montant</p>
                        <p class="font-medium text-emerald-400">{{ number_format($detailInsurance->montant, 2, ',', ' ') }} DH</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Statut</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statutColors[$detailInsurance->statut] ?? 'bg-gray-500' }}">
                            {{ $detailInsurance->statut_label }}
                        </span>
                    </div>
                </div>

                @if($detailInsurance->observations)
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Observations</p>
                    <p class="text-gray-100">{{ $detailInsurance->observations }}</p>
                </div>
                @endif

                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Créée par</p>
                    <p class="font-medium text-gray-100">{{ $detailInsurance->user->name }}</p>
                    <p class="text-sm text-gray-400">{{ $detailInsurance->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>