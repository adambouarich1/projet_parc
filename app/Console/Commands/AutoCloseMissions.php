<?php

namespace App\Console\Commands;

use App\Models\MissionOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoCloseMissions extends Command
{
    protected $signature = 'app:auto-close-missions';
    protected $description = 'Passe en "Terminé, attente de clôturation" les missions dont la date de retour prévue est dépassée';

    public function handle(): void
    {
        $now = Carbon::now();

        $missions = MissionOrder::whereIn('statut', [
                MissionOrder::STATUT_EN_COURS,
                MissionOrder::STATUT_DEPART_ANTICIPE,
            ])
            ->where('date_retour_prevue', '<=', $now)
            ->with(['vehicle', 'driver'])
            ->get();

        $count = 0;
        foreach ($missions as $mission) {
            $mission->update(['statut' => MissionOrder::STATUT_TERMINE_ATTENTE]);
            $mission->vehicle?->update(['statut_actuel' => 'En service']);
            $mission->driver?->update(['statut_actuel' => 'Disponible']);
            $count++;
        }

        $this->info("{$count} mission(s) passée(s) en \"Terminé, attente de clôturation\".");
    }
}
