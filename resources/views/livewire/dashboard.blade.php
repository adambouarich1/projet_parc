<div class="space-y-6">
    {{-- Header avec date --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Dashboard</h1>
            <p class="text-sm text-gray-400">Vue d'ensemble de votre parc automobile</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-400">{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</p>
        </div>
    </div>

    {{-- Cartes statistiques principales --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        {{-- Total Véhicules --}}
        <div class="bg-gray-900 rounded-2xl p-5 border border-gray-800 hover:border-indigo-500/50 transition-all">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-indigo-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-3xl font-bold text-white">{{ $totalVehicles }}</p>
                <p class="text-sm text-gray-400 mt-1">Total Véhicules</p>
            </div>
        </div>



        {{-- Véhicules Disponibles --}}
        <div class="bg-gray-900 rounded-2xl p-5 border border-gray-800 hover:border-emerald-500/50 transition-all">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-3xl font-bold text-emerald-400">{{ $vehiculesDisponibles }}</p>
                <p class="text-sm text-gray-400 mt-1">Véhicules Dispo</p>
            </div>
        </div>
                {{-- Véhicules Indisponibles --}}
        <div class="bg-gray-900 rounded-2xl p-5 border border-gray-800 hover:border-rose-500/50 transition-all">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-rose-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-3xl font-bold text-rose-400">{{ $vehiculesIndisponibles }}</p>
                <p class="text-sm text-gray-400 mt-1">Véhicules Indispo</p>
            </div>
        </div>

                        {{-- Total Conducteurs --}}
        <div class="bg-gray-900 rounded-2xl p-5 border border-gray-800 hover:border-purple-500/50 transition-all">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-3xl font-bold text-white">{{ $totalDrivers }}</p>
                <p class="text-sm text-gray-400 mt-1">Total Conducteurs</p>
            </div>
        </div>

        {{-- Conducteurs Disponibles --}}
        <div class="bg-gray-900 rounded-2xl p-5 border border-gray-800 hover:border-teal-500/50 transition-all">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-teal-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-3xl font-bold text-teal-400">{{ $conducteursDisponibles }}</p>
                <p class="text-sm text-gray-400 mt-1">Conducteurs Dispo</p>
            </div>
        </div>




        {{-- Conducteurs Indisponibles --}}
        <div class="bg-gray-900 rounded-2xl p-5 border border-gray-800 hover:border-orange-500/50 transition-all">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-orange-500/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-3xl font-bold text-orange-400">{{ $conducteursIndisponibles }}</p>
                <p class="text-sm text-gray-400 mt-1">Conducteurs Indispo</p>
            </div>
        </div>
    </div>

    {{-- Section Alertes et Missions --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Alertes Récentes --}}
        <div class="bg-gray-900 rounded-2xl border border-gray-800 overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-white">Alertes Récentes</h2>
                        @if($alertesCritiques > 0)
                            <p class="text-xs text-rose-400">{{ $alertesCritiques }} alerte(s) critique(s)</p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('alerts.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300 transition">
                    Voir tout →
                </a>
            </div>
            <div class="p-5">
                @if($alertesRecentes->count() > 0)
                    <div class="space-y-3">
                        @foreach($alertesRecentes as $alerte)
                            @php
                                $prioriteStyles = [
                                    'critique' => 'bg-rose-500/20 text-rose-400 border-rose-500/30',
                                    'haute' => 'bg-orange-500/20 text-orange-400 border-orange-500/30',
                                    'moyenne' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                    'basse' => 'bg-gray-500/20 text-gray-400 border-gray-500/30',
                                ];
                                $style = $prioriteStyles[$alerte->priorite] ?? $prioriteStyles['basse'];
                            @endphp
                            <div class="flex items-center gap-4 p-3 rounded-xl bg-gray-800/50 border border-gray-700/50 hover:bg-gray-800 transition">
                                <div class="w-10 h-10 rounded-lg {{ $style }} border flex items-center justify-center flex-shrink-0">
                                    @if($alerte->priorite === 'critique')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate">{{ $alerte->titre }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $alerte->type_label }}
                                        @if($alerte->jours_restants !== null)
                                            <span class="mx-1">•</span>
                                            <span class="{{ $alerte->jours_restants < 0 ? 'text-rose-400' : 'text-amber-400' }}">
                                                {{ $alerte->jours_restants < 0 ? abs($alerte->jours_restants) . 'j retard' : $alerte->jours_restants . 'j restants' }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <span class="px-2.5 py-1 text-xs font-medium rounded-lg {{ $style }} border flex-shrink-0">
                                    {{ ucfirst($alerte->priorite) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="w-16 h-16 bg-emerald-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-gray-400">Aucune alerte en cours</p>
                        <p class="text-sm text-gray-500 mt-1">Tout est en ordre !</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Missions en cours --}}
        <div class="bg-gray-900 rounded-2xl border border-gray-800 overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-white">Missions du Jour</h2>
                        <p class="text-xs text-gray-400">Missions en cours ou validées</p>
                    </div>
                </div>
                <a href="{{ route('missions.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300 transition">
                    Voir tout →
                </a>
            </div>
            <div class="p-5">
                @if($missionsJour->count() > 0)
                    <div class="space-y-3">
                        @foreach($missionsJour as $mission)
                            @php
                                $statutStyles = [
                                    'en_cours' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                    'valide' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                ];
                                $style = $statutStyles[$mission->statut] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                            @endphp
                            <div class="flex items-center gap-4 p-3 rounded-xl bg-gray-800/50 border border-gray-700/50 hover:bg-gray-800 transition">
                                <div class="w-10 h-10 rounded-lg {{ $style }} border flex items-center justify-center flex-shrink-0">
                                    @if($mission->statut === 'en_cours')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate">{{ $mission->objet }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $mission->driver ? $mission->driver->nom . ' ' . $mission->driver->prenom : 'Non assigné' }}
                                        <span class="mx-1">•</span>
                                        {{ $mission->vehicle ? $mission->vehicle->immatriculation : 'Non assigné' }}
                                    </p>
                                </div>
                                <span class="px-2.5 py-1 text-xs font-medium rounded-lg {{ $style }} border flex-shrink-0">
                                    {{ $mission->statut_label }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="w-16 h-16 bg-gray-700/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-gray-400">Aucune mission aujourd'hui</p>
                        <p class="text-sm text-gray-500 mt-1">Pas de mission en cours</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>