<?php
    $prioriteColors = [
        'haute' => 'border-l-amber-500 bg-amber-900/20',
        'critique' => 'border-l-rose-500 bg-rose-900/20',
    ];
?>

<div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
            🔔 Alertes
            @if($stats['critiques'] > 0)
                <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-gray-900 bg-rose-600 rounded-full animate-pulse">
                    {{ $stats['critiques'] }}
                </span>
            @endif
        </h3>
        <a href="{{ route('alerts.index') }}" class="text-xs text-green-700 hover:text-green-600">
            Voir tout ({{ $stats['actives'] }})
        </a>
    </div>

    @if($alerts->count() > 0)
        <div class="space-y-2">
            @foreach($alerts as $alert)
                <div class="p-3 rounded-lg border-l-4 {{ $prioriteColors[$alert->priorite] ?? 'border-l-gray-500 bg-gray-800' }}">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $alert->titre }}</p>
                    <p class="text-xs text-gray-700 mt-1">
                        {{ $alert->type_label }}
                        @if($alert->jours_restants !== null)
                            • {{ $alert->jours_restants < 0 ? abs($alert->jours_restants) . 'j retard' : $alert->jours_restants . 'j' }}
                        @endif
                    </p>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-gray-700 text-center py-4">
            ✓ Aucune alerte urgente
        </p>
    @endif
</div>