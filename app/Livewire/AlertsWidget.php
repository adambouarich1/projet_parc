<?php

namespace App\Livewire;

use App\Models\Alert;
use Livewire\Component;

class AlertsWidget extends Component
{
    public int $limit = 5;

    public function render()
    {
        $alerts = Alert::active()
            ->haute()
            ->orderByRaw("FIELD(priorite, 'critique', 'haute')")
            ->orderBy('created_at', 'desc')
            ->limit($this->limit)
            ->get();

        $stats = [
            'actives' => Alert::active()->count(),
            'critiques' => Alert::critique()->active()->count(),
        ];

        return view('livewire.alerts-widget', [
            'alerts' => $alerts,
            'stats' => $stats,
        ]);
    }
}