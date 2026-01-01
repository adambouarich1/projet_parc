<?php
    use Illuminate\Support\Facades\Storage;

    $statusColors = [
        'Disponible' => 'bg-emerald-900/50 text-emerald-100 border border-emerald-700',
        'En congé' => 'bg-amber-900/40 text-amber-100 border border-amber-700',
        'Maladie' => 'bg-sky-900/40 text-sky-100 border border-sky-700',
        'Non disponible' => 'bg-rose-900/40 text-rose-100 border border-rose-700',
    ];
?>

<div class="space-y-4 text-gray-100">
    @if (session()->has('status'))
        <div class="rounded-md bg-emerald-900/40 border border-emerald-700 text-emerald-100 px-4 py-3">
            {{ session('status') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-4">
        <div class="lg:w-1/4 space-y-4">
            <div class="bg-gray-900 shadow rounded-lg p-4 space-y-3 border border-gray-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-100">Filtres</h3>
                    <button
                        type="button"
                        wire:click="$set('filters', { service_affecte: '', statut_actuel: '', categories: '' })"
                        class="text-xs text-gray-400 hover:text-gray-200"
                    >
                        Réinitialiser
                    </button>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Service</label>
                    <input type="text" wire:model.debounce.400ms="filters.service_affecte" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="Service affecté">
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Statut</label>
                    <select wire:model.live="filters.statut_actuel" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Tous</option>
                        @foreach ($statuts as $statut)
                            <option value="{{ $statut }}">{{ $statut }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs text-gray-400">Catégorie permis</label>
                    <input type="text" wire:model.debounce.300ms="filters.categories" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400" placeholder="B, C, D...">
                </div>
            </div>
            <button
                type="button"
                wire:click="openCreate"
                class="w-full inline-flex items-center justify-center px-4 py-3 bg-indigo-500 text-white rounded-md shadow hover:bg-indigo-600 transition"
            >
                Ajouter un conducteur
            </button>
        </div>

        <div class="flex-1 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Module CRUD complet</p>
                    <h3 class="text-lg font-semibold text-gray-100">Gestion des conducteurs</h3>
                </div>
                <div class="hidden lg:block">
                    <button
                        type="button"
                        wire:click="openCreate"
                        class="inline-flex items-center px-4 py-2 bg-indigo-500 text-white rounded-md shadow hover:bg-indigo-600 transition"
                    >
                        + Ajouter un conducteur
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse ($drivers as $driver)
                    <div class="relative bg-gray-900 rounded-xl shadow border border-gray-800 p-4 flex flex-col" wire:key="driver-{{ $driver->id }}">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-3">
                                @if ($driver->photo_path)
                                    <img src="{{ Storage::url($driver->photo_path) }}" class="h-12 w-12 rounded-full object-cover border border-gray-700" alt="{{ $driver->nom }} {{ $driver->prenom }}">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-indigo-700 text-white flex items-center justify-center font-semibold">
                                        {{ strtoupper(substr($driver->prenom, 0, 1)) }}{{ strtoupper(substr($driver->nom, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-100">{{ $driver->prenom }} {{ $driver->nom }}</h4>
                                    <p class="text-sm text-gray-300">Matricule: {{ $driver->matricule }}</p>
                                </div>
                            </div>
                            <button
                                type="button"
                                wire:click="openDetails({{ $driver->id }})"
                                class="h-8 w-8 inline-flex items-center justify-center rounded-full border border-gray-700 text-gray-200 hover:bg-gray-800"
                                title="Détails"
                            >
                                i
                            </button>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-sm text-gray-200 font-medium">
                                Statut:
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$driver->statut_actuel] ?? 'bg-gray-800 text-gray-200 border border-gray-700' }}">
                                    {{ $driver->statut_actuel }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-300">
                                Cat.: {{ $driver->categories ?: 'N/A' }}
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-end gap-2">
                            <button
                                type="button"
                                wire:click="openEdit({{ $driver->id }})"
                                class="px-3 py-1.5 text-xs font-semibold text-indigo-200 bg-indigo-900/40 rounded-md hover:bg-indigo-900/60"
                            >
                                Modifier
                            </button>
                            <button
                                type="button"
                                x-data
                                x-on:click.prevent="if (confirm('Supprimer ce conducteur ?')) { $wire.deleteDriver({{ $driver->id }}) }"
                                class="px-3 py-1.5 text-xs font-semibold text-rose-200 bg-rose-900/40 rounded-md hover:bg-rose-900/60"
                            >
                                Supprimer
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-gray-900 border border-dashed border-gray-800 rounded-lg p-8 text-center text-gray-300">
                        Aucun conducteur pour le moment. Cliquez sur « Ajouter un conducteur » pour démarrer.
                    </div>
                @endforelse
            </div>

            <div>
                {{ $drivers->links() }}
            </div>
        </div>
    </div>

    @if ($showFormModal)
        <div class="fixed inset-0 bg-black/60 z-40 flex items-start justify-center overflow-y-auto py-10 px-4" wire:key="form-modal">
            <div class="bg-gray-900 rounded-xl shadow-2xl max-w-5xl w-full relative border border-gray-800">
                <button
                    type="button"
                    class="absolute top-3 right-3 h-8 w-8 inline-flex items-center justify-center rounded-full bg-gray-800 text-gray-200 hover:bg-gray-700"
                    wire:click="$set('showFormModal', false)"
                >
                    ✕
                </button>
                <form wire:submit.prevent="save" class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400">Fiche conducteur</p>
                            <h3 class="text-xl font-semibold text-gray-100">
                                {{ $editingId ? 'Modifier un conducteur' : 'Ajouter un conducteur' }}
                            </h3>
                        </div>
                        <div class="text-sm text-gray-400">
                            Identité, contact et permis regroupés.
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-1 space-y-3">
                            <h4 class="text-sm font-semibold text-gray-100">Identité</h4>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Nom *</label>
                                <input type="text" wire:model.live="form.nom" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                @error('form.nom') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Prénom *</label>
                                <input type="text" wire:model.live="form.prenom" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                @error('form.prenom') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Matricule *</label>
                                <input type="text" wire:model.live="form.matricule" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                @error('form.matricule') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">CIN *</label>
                                <input type="text" wire:model.live="form.cin" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                @error('form.cin') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Date de naissance</label>
                                <input type="date" wire:model.live="form.date_naissance" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Photo</label>
                                <input type="file" wire:model="photo" accept="image/*" class="w-full text-sm text-gray-200">
                                @error('photo') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                                <div class="flex items-center gap-3">
                                    @if ($photo)
                                        <img src="{{ $photo->temporaryUrl() }}" class="h-16 w-16 object-cover rounded border border-gray-700">
                                    @elseif ($currentPhotoPath)
                                        <img src="{{ Storage::url($currentPhotoPath) }}" class="h-16 w-16 object-cover rounded border border-gray-700">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-1 space-y-3">
                            <h4 class="text-sm font-semibold text-gray-100">Affectation & contact</h4>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Service affecté</label>
                                <input type="text" wire:model.live="form.service_affecte" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Responsable hiérarchique</label>
                                <input type="text" wire:model.live="form.responsable_hierarchique" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Poste occupé</label>
                                <input type="text" wire:model.live="form.poste_occupe" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Téléphone</label>
                                <input type="text" wire:model.live="form.telephone" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Email pro</label>
                                <input type="email" wire:model.live="form.email_pro" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                        </div>

                        <div class="lg:col-span-1 space-y-3">
                            <h4 class="text-sm font-semibold text-gray-100">Permis & statut</h4>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Numéro de permis</label>
                                <input type="text" wire:model.live="form.num_permis" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Date délivrance</label>
                                <input type="date" wire:model.live="form.date_delivrance" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Date expiration</label>
                                <input type="date" wire:model.live="form.date_expiration" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                @error('form.date_expiration') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Catégories (B, C...)</label>
                                <input type="text" wire:model.live="form.categories" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Scan permis (PDF/Image)</label>
                                <input type="file" wire:model="scan_permis" accept=".pdf,image/*" class="w-full text-sm text-gray-200">
                                @error('scan_permis') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                                @if ($scan_permis)
                                    <p class="text-xs text-gray-300">Fichier prêt à être envoyé.</p>
                                @elseif ($currentScanPath)
                                    <a href="{{ Storage::url($currentScanPath) }}" target="_blank" class="text-xs text-indigo-300 underline">Voir le fichier</a>
                                @endif
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Statut *</label>
                                <select wire:model.live="form.statut_actuel" class="w-full rounded-md border-gray-700 bg-gray-800 text-gray-100 shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-400">
                                    @foreach ($statuts as $statut)
                                        <option value="{{ $statut }}">{{ $statut }}</option>
                                    @endforeach
                                </select>
                                @error('form.statut_actuel') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-800">
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

    @if ($showDetailsModal && $detailDriver)
        <div class="fixed inset-0 bg-black/60 z-40 flex items-start justify-center overflow-y-auto py-10 px-4" wire:key="details-modal">
            <div class="bg-gray-900 rounded-xl shadow-2xl max-w-5xl w-full relative border border-gray-800">
                <button
                    type="button"
                    class="absolute top-3 right-3 h-8 w-8 inline-flex items-center justify-center rounded-full bg-gray-800 text-gray-200 hover:bg-gray-700"
                    wire:click="$set('showDetailsModal', false)"
                >
                    ✕
                </button>
                <div class="p-6 space-y-4 text-gray-100" x-data="{ tab: 'identite' }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            @if ($detailDriver->photo_path)
                                <img src="{{ Storage::url($detailDriver->photo_path) }}" class="h-16 w-16 rounded-full object-cover border border-gray-700" alt="{{ $detailDriver->nom }} {{ $detailDriver->prenom }}">
                            @else
                                <div class="h-16 w-16 rounded-full bg-indigo-700 text-white flex items-center justify-center font-semibold text-lg">
                                    {{ strtoupper(substr($detailDriver->prenom, 0, 1)) }}{{ strtoupper(substr($detailDriver->nom, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-400">Fiche conducteur</p>
                                <h3 class="text-xl font-semibold text-gray-100">
                                    {{ $detailDriver->prenom }} {{ $detailDriver->nom }} — {{ $detailDriver->matricule }}
                                </h3>
                                <div class="mt-2 inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$detailDriver->statut_actuel] ?? 'bg-gray-800 text-gray-200 border border-gray-700' }}">
                                    {{ $detailDriver->statut_actuel }}
                                </div>
                            </div>
                        </div>
                        @if ($detailDriver->scan_permis_path)
                            <a href="{{ Storage::url($detailDriver->scan_permis_path) }}" target="_blank" class="text-sm text-indigo-300 underline">Voir le permis</a>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        <button type="button" class="px-3 py-2 rounded-md text-sm font-semibold"
                            :class="tab === 'identite' ? 'bg-indigo-900/60 text-indigo-200 border border-indigo-800' : 'bg-gray-800 text-gray-200 border border-gray-700'"
                            @click="tab = 'identite'">
                            Identité
                        </button>
                        <button type="button" class="px-3 py-2 rounded-md text-sm font-semibold"
                            :class="tab === 'contact' ? 'bg-indigo-900/60 text-indigo-200 border border-indigo-800' : 'bg-gray-800 text-gray-200 border border-gray-700'"
                            @click="tab = 'contact'">
                            Contact
                        </button>
                        <button type="button" class="px-3 py-2 rounded-md text-sm font-semibold"
                            :class="tab === 'permis' ? 'bg-indigo-900/60 text-indigo-200 border border-indigo-800' : 'bg-gray-800 text-gray-200 border border-gray-700'"
                            @click="tab = 'permis'">
                            Permis
                        </button>
                    </div>

                    <div class="bg-gray-800 rounded-lg p-4 border border-gray-700" x-show="tab === 'identite'" x-cloak>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                            <div><span class="text-gray-500 text-xs">Nom</span><p class="font-semibold text-gray-100">{{ $detailDriver->nom }}</p></div>
                            <div><span class="text-gray-500 text-xs">Prénom</span><p class="font-semibold text-gray-100">{{ $detailDriver->prenom }}</p></div>
                            <div><span class="text-gray-500 text-xs">Matricule</span><p class="font-semibold text-gray-100">{{ $detailDriver->matricule }}</p></div>
                            <div><span class="text-gray-500 text-xs">CIN</span><p class="font-semibold text-gray-100">{{ $detailDriver->cin }}</p></div>
                            <div><span class="text-gray-500 text-xs">Date de naissance</span><p class="font-semibold text-gray-100">{{ optional($detailDriver->date_naissance)->format('d/m/Y') }}</p></div>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-lg p-4 border border-gray-700" x-show="tab === 'contact'" x-cloak>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                            <div><span class="text-gray-500 text-xs">Service</span><p class="font-semibold text-gray-100">{{ $detailDriver->service_affecte }}</p></div>
                            <div><span class="text-gray-500 text-xs">Responsable</span><p class="font-semibold text-gray-100">{{ $detailDriver->responsable_hierarchique }}</p></div>
                            <div><span class="text-gray-500 text-xs">Poste</span><p class="font-semibold text-gray-100">{{ $detailDriver->poste_occupe }}</p></div>
                            <div><span class="text-gray-500 text-xs">Téléphone</span><p class="font-semibold text-gray-100">{{ $detailDriver->telephone }}</p></div>
                            <div><span class="text-gray-500 text-xs">Email pro</span><p class="font-semibold text-gray-100">{{ $detailDriver->email_pro }}</p></div>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-lg p-4 border border-gray-700" x-show="tab === 'permis'" x-cloak>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                            <div><span class="text-gray-500 text-xs">N° Permis</span><p class="font-semibold text-gray-100">{{ $detailDriver->num_permis }}</p></div>
                            <div><span class="text-gray-500 text-xs">Délivré le</span><p class="font-semibold text-gray-100">{{ optional($detailDriver->date_delivrance)->format('d/m/Y') }}</p></div>
                            <div><span class="text-gray-500 text-xs">Expire le</span><p class="font-semibold text-gray-100">{{ optional($detailDriver->date_expiration)->format('d/m/Y') }}</p></div>
                            <div><span class="text-gray-500 text-xs">Catégories</span><p class="font-semibold text-gray-100">{{ $detailDriver->categories }}</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
