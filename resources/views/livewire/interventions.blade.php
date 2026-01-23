<?php
    $roleBadges = [
        'admin' => 'bg-rose-500 text-white',
        'responsable_parc' => 'bg-indigo-500 text-white',
        'valideur' => 'bg-amber-500 text-white',
        'agent_saisie' => 'bg-emerald-500 text-white',
        'consultation' => 'bg-gray-500 text-white',
    ];

    $typeColors = [
        'entretien' => 'bg-blue-500 text-white',
        'reparation' => 'bg-orange-500 text-white',
        'controle_technique' => 'bg-purple-500 text-white',
        'autre' => 'bg-gray-500 text-white',
    ];

    $statutColors = [
        'planifie' => 'bg-amber-500 text-white',
        'en_cours' => 'bg-blue-500 text-white',
        'termine' => 'bg-emerald-500 text-white',
        'annule' => 'bg-gray-600 text-white',
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Coût total interventions</p>
            <p class="text-2xl font-bold text-emerald-400">{{ number_format($stats['total_cout'], 2, ',', ' ') }} DH</p>
        </div>
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Nombre d'interventions</p>
            <p class="text-2xl font-bold text-indigo-400">{{ $stats['nb_interventions'] }}</p>
        </div>
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-4">
            <p class="text-xs text-gray-400">Interventions planifiées</p>
            <p class="text-2xl font-bold text-amber-400">{{ $stats['planifiees'] }}</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-4">
        {{-- Sidebar filtres --}}
        <div class="lg:w-1/4 space-y-4">
            <div class="bg-gray-900 shadow rounded-lg p-4 space-y-3 border border-gray-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-100">Filtres</h3>
                    <button type="button" wire:click="$set('filters', { vehicle_id: '', type: '', statut: '' })" class="text-xs text-gray-400 hover:text-gray-200">
                        Réinitialiser
                    </button>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Véhicule</label>
                    <select wire:model.live="filters.vehicle_id" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Tous</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}">{{ $v->immatriculation }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Type</label>
                    <select wire:model.live="filters.type" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Tous</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Statut</label>
                    <select wire:model.live="filters.statut" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Tous</option>
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if(auth()->user()->canEdit())
            <button type="button" wire:click="openCreate" class="w-full inline-flex items-center justify-center px-4 py-3 bg-indigo-500 text-white rounded-md shadow hover:bg-indigo-600 transition">
                + Nouvelle intervention
            </button>
            @endif
        </div>

        {{-- Liste --}}
        <div class="flex-1 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-100">Interventions</h3>
                @if(auth()->user()->canEdit())
                <div class="hidden lg:block">
                    <button type="button" wire:click="openCreate" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 transition">
                        + Nouvelle intervention
                    </button>
                </div>
                @endif
            </div>

            {{-- Tableau --}}
            <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-800 text-gray-300 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">Date</th>
                                <th class="px-4 py-3 text-left">Véhicule</th>
                                <th class="px-4 py-3 text-left">Type</th>
                                <th class="px-4 py-3 text-left">Titre</th>
                                <th class="px-4 py-3 text-right">Coût</th>
                                <th class="px-4 py-3 text-left">Statut</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse ($interventions as $intervention)
                                <tr class="hover:bg-gray-800/50" wire:key="intervention-{{ $intervention->id }}">
                                    <td class="px-4 py-3 text-gray-300">{{ $intervention->date_intervention->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-100">{{ $intervention->vehicle->immatriculation }}</div>
                                        <div class="text-xs text-gray-400">{{ $intervention->vehicle->marque }} {{ $intervention->vehicle->modele }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $typeColors[$intervention->type] ?? 'bg-gray-500 text-white' }}">
                                            {{ $intervention->type_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-100">{{ Str::limit($intervention->titre, 30) }}</div>
                                        @if($intervention->prestataire)
                                            <div class="text-xs text-gray-400">{{ $intervention->prestataire }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-emerald-400">
                                        {{ number_format($intervention->cout_total, 2, ',', ' ') }} DH
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $statutColors[$intervention->statut] ?? 'bg-gray-500 text-white' }}">
                                            {{ $intervention->statut_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button wire:click="openDetails({{ $intervention->id }})" class="p-1.5 text-gray-400 hover:text-gray-200 hover:bg-gray-700 rounded" title="Détails">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                            @if(auth()->user()->canEdit())
                                                <button wire:click="openEdit({{ $intervention->id }})" class="p-1.5 text-indigo-400 hover:text-indigo-300 hover:bg-indigo-900/40 rounded" title="Modifier">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                @if($intervention->statut === 'planifie')
                                                    <button wire:click="markAs({{ $intervention->id }}, 'en_cours')" class="p-1.5 text-blue-400 hover:text-blue-300 hover:bg-blue-900/40 rounded" title="Démarrer">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </button>
                                                @endif
                                                @if($intervention->statut === 'en_cours')
                                                    <button wire:click="markAs({{ $intervention->id }}, 'termine')" class="p-1.5 text-emerald-400 hover:text-emerald-300 hover:bg-emerald-900/40 rounded" title="Terminer">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    </button>
                                                @endif
                                                @if($intervention->statut === 'termine')
                                            <button wire:click="archive({{ $intervention->id }})" class="p-1.5 text-gray-400 hover:text-gray-300 hover:bg-gray-700 rounded" title="Archiver">
                                                📦
                                            </button>
                                                @endif
                                                <button wire:click="delete({{ $intervention->id }})" wire:confirm="Supprimer cette intervention ?" class="p-1.5 text-rose-400 hover:text-rose-300 hover:bg-rose-900/40 rounded" title="Supprimer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                        Aucune intervention trouvée.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>{{ $interventions->links() }}</div>
        </div>
    </div>

    {{-- Modal Formulaire --}}
    @if($showFormModal)
    <div class="fixed inset-0 bg-black/60 z-40 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-xl shadow-2xl max-w-3xl w-full border border-gray-800 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold text-gray-100">
                    {{ $editingId ? 'Modifier l\'intervention' : 'Nouvelle intervention' }}
                </h3>
                <button wire:click="$set('showFormModal', false)" class="text-gray-400 hover:text-gray-200">✕</button>
            </div>
            <form wire:submit.prevent="save" class="p-4 space-y-4">
                {{-- Ligne 1: Véhicule et Type --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Véhicule *</label>
                        <select wire:model.live="form.vehicle_id" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            <option value="">Sélectionner</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}">{{ $v->immatriculation }} - {{ $v->marque }} {{ $v->modele }}</option>
                            @endforeach
                        </select>
                        @error('form.vehicle_id') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Type *</label>
                        <select wire:model.live="form.type" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Titre --}}
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Titre *</label>
                    <input type="text" wire:model="form.titre" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Ex: Vidange + filtres">
                    @error('form.titre') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Description</label>
                    <textarea wire:model="form.description" rows="2" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400"></textarea>
                </div>

                {{-- Dates et Km --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Date intervention *</label>
                        <input type="date" wire:model="form.date_intervention" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Prochaine (date)</label>
                        <input type="date" wire:model="form.date_prochaine" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Kilométrage</label>
                        <input type="number" wire:model="form.kilometrage" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Prochain (km)</label>
                        <input type="number" wire:model="form.km_prochaine" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                    </div>
                </div>

                {{-- Coûts --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Coût pièces (DH)</label>
                        <input type="number" step="0.01" wire:model.live="form.cout_pieces" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Main d'œuvre (DH)</label>
                        <input type="number" step="0.01" wire:model.live="form.cout_main_oeuvre" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Total (DH)</label>
                        <input type="number" step="0.01" wire:model="form.cout_total" readonly class="w-full rounded-md border-gray-700 bg-gray-700 text-gray-100 text-sm">
                    </div>
                </div>

                {{-- Prestataire --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">Prestataire</label>
                        <input type="text" wire:model="form.prestataire" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Ex: Garage Central">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400">N° Facture</label>
                        <input type="text" wire:model="form.numero_facture" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                    </div>
                </div>

                {{-- Champs spécifiques Assurance --}}
                @if($form['type'] === 'assurance')
                <div class="bg-emerald-900/20 border border-emerald-800 rounded-lg p-4 space-y-4">
                    <h4 class="text-sm font-semibold text-emerald-300">Informations Assurance</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label class="text-xs text-emerald-300">Assureur</label>
                            <input type="text" wire:model="form.assureur" class="w-full rounded-md border-emerald-700 bg-gray-800 text-gray-100 text-sm focus:border-emerald-400 focus:ring-emerald-400">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs text-emerald-300">N° Police</label>
                            <input type="text" wire:model="form.numero_police" class="w-full rounded-md border-emerald-700 bg-gray-800 text-gray-100 text-sm focus:border-emerald-400 focus:ring-emerald-400">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs text-emerald-300">Date expiration</label>
                            <input type="date" wire:model="form.date_expiration_assurance" class="w-full rounded-md border-emerald-700 bg-gray-800 text-gray-100 text-sm focus:border-emerald-400 focus:ring-emerald-400">
                        </div>
                    </div>
                </div>
                @endif

                {{-- Champs spécifiques Contrôle Technique --}}
                @if($form['type'] === 'controle_technique')
                <div class="bg-purple-900/20 border border-purple-800 rounded-lg p-4 space-y-4">
                    <h4 class="text-sm font-semibold text-purple-300">Informations Contrôle Technique</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-xs text-purple-300">Résultat</label>
                            <select wire:model="form.resultat_ct" class="w-full rounded-md border-purple-700 bg-gray-800 text-gray-100 text-sm focus:border-purple-400 focus:ring-purple-400">
                                <option value="">Sélectionner</option>
                                @foreach($resultats_ct as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs text-purple-300">Date expiration CT</label>
                            <input type="date" wire:model="form.date_expiration_ct" class="w-full rounded-md border-purple-700 bg-gray-800 text-gray-100 text-sm focus:border-purple-400 focus:ring-purple-400">
                        </div>
                    </div>
                </div>
                @endif

                {{-- Statut --}}
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Statut</label>
                    <select wire:model="form.statut" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        @foreach($statuts as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Observations --}}
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Observations</label>
                    <textarea wire:model="form.observations" rows="2" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 text-sm focus:border-indigo-400 focus:ring-indigo-400"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-800">
                    <button type="button" wire:click="$set('showFormModal', false)" class="px-4 py-2 rounded-md text-gray-200 bg-gray-800 hover:bg-gray-700 border border-gray-700">
                        Annuler
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-md bg-indigo-500 text-white hover:bg-indigo-600 shadow">
                        {{ $editingId ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal Détails --}}
    @if($showDetailsModal && $detailIntervention)
    <div class="fixed inset-0 bg-black/60 z-40 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-xl shadow-2xl max-w-2xl w-full border border-gray-800 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-4 border-b border-gray-800">
                <div>
                    <h3 class="text-lg font-semibold text-gray-100">{{ $detailIntervention->titre }}</h3>
                    <div class="flex gap-2 mt-1">
                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $typeColors[$detailIntervention->type] ?? 'bg-gray-500' }}">
                            {{ $detailIntervention->type_label }}
                        </span>
                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $statutColors[$detailIntervention->statut] ?? 'bg-gray-500' }}">
                            {{ $detailIntervention->statut_label }}
                        </span>
                    </div>
                </div>
                <button wire:click="$set('showDetailsModal', false)" class="text-gray-400 hover:text-gray-200">✕</button>
            </div>
            <div class="p-4 space-y-4">
                {{-- Véhicule --}}
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Véhicule</p>
                    <p class="font-medium text-gray-100">{{ $detailIntervention->vehicle->marque }} {{ $detailIntervention->vehicle->modele }}</p>
                    <p class="text-sm text-gray-300">{{ $detailIntervention->vehicle->immatriculation }}</p>
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Date intervention</p>
                        <p class="font-medium text-gray-100">{{ $detailIntervention->date_intervention->format('d/m/Y') }}</p>
                    </div>
                    @if($detailIntervention->date_prochaine)
                    <div class="bg-amber-900/40 rounded-lg p-3 border border-amber-700">
                        <p class="text-xs text-amber-300">Prochaine intervention</p>
                        <p class="font-medium text-amber-100">{{ $detailIntervention->date_prochaine->format('d/m/Y') }}</p>
                    </div>
                    @endif
                </div>

                {{-- Kilométrage --}}
                @if($detailIntervention->kilometrage || $detailIntervention->km_prochaine)
                <div class="grid grid-cols-2 gap-4">
                    @if($detailIntervention->kilometrage)
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Kilométrage</p>
                        <p class="font-medium text-gray-100">{{ number_format($detailIntervention->kilometrage, 0, ',', ' ') }} km</p>
                    </div>
                    @endif
                    @if($detailIntervention->km_prochaine)
                    <div class="bg-amber-900/40 rounded-lg p-3 border border-amber-700">
                        <p class="text-xs text-amber-300">Prochain km</p>
                        <p class="font-medium text-amber-100">{{ number_format($detailIntervention->km_prochaine, 0, ',', ' ') }} km</p>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Coûts --}}
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Pièces</p>
                        <p class="font-medium text-gray-100">{{ number_format($detailIntervention->cout_pieces, 2, ',', ' ') }} DH</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Main d'œuvre</p>
                        <p class="font-medium text-gray-100">{{ number_format($detailIntervention->cout_main_oeuvre, 2, ',', ' ') }} DH</p>
                    </div>
                    <div class="bg-emerald-900/40 rounded-lg p-3 border border-emerald-700">
                        <p class="text-xs text-emerald-300">Total</p>
                        <p class="font-bold text-emerald-100">{{ number_format($detailIntervention->cout_total, 2, ',', ' ') }} DH</p>
                    </div>
                </div>

                {{-- Prestataire --}}
                @if($detailIntervention->prestataire || $detailIntervention->numero_facture)
                <div class="grid grid-cols-2 gap-4">
                    @if($detailIntervention->prestataire)
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">Prestataire</p>
                        <p class="font-medium text-gray-100">{{ $detailIntervention->prestataire }}</p>
                    </div>
                    @endif
                    @if($detailIntervention->numero_facture)
                    <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                        <p class="text-xs text-gray-400">N° Facture</p>
                        <p class="font-medium text-gray-100">{{ $detailIntervention->numero_facture }}</p>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Assurance --}}
                @if($detailIntervention->type === 'assurance')
                <div class="bg-emerald-900/20 border border-emerald-800 rounded-lg p-4 space-y-2">
                    <h4 class="text-sm font-semibold text-emerald-300">Assurance</h4>
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div><span class="text-gray-400">Assureur:</span> <span class="text-gray-100">{{ $detailIntervention->assureur }}</span></div>
                        <div><span class="text-gray-400">N° Police:</span> <span class="text-gray-100">{{ $detailIntervention->numero_police }}</span></div>
                        <div><span class="text-gray-400">Expire:</span> <span class="text-emerald-300 font-medium">{{ $detailIntervention->date_expiration_assurance?->format('d/m/Y') }}</span></div>
                    </div>
                </div>
                @endif

                {{-- Contrôle Technique --}}
                @if($detailIntervention->type === 'controle_technique')
                <div class="bg-purple-900/20 border border-purple-800 rounded-lg p-4 space-y-2">
                    <h4 class="text-sm font-semibold text-purple-300">Contrôle Technique</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="text-gray-400">Résultat:</span> <span class="text-purple-300 font-medium">{{ $detailIntervention->resultat_ct_label }}</span></div>
                        <div><span class="text-gray-400">Expire:</span> <span class="text-purple-300 font-medium">{{ $detailIntervention->date_expiration_ct?->format('d/m/Y') }}</span></div>
                    </div>
                </div>
                @endif

                {{-- Description --}}
                @if($detailIntervention->description)
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Description</p>
                    <p class="text-gray-100">{{ $detailIntervention->description }}</p>
                </div>
                @endif

                {{-- Observations --}}
                @if($detailIntervention->observations)
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Observations</p>
                    <p class="text-gray-100">{{ $detailIntervention->observations }}</p>
                </div>
                @endif

                {{-- Saisi par --}}
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <p class="text-xs text-gray-400">Saisi par</p>
                    <p class="font-medium text-gray-100">{{ $detailIntervention->user->name }}</p>
                    <p class="text-sm text-gray-300">{{ $detailIntervention->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>