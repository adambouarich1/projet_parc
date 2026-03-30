<?php

namespace App\Console\Commands;

use App\Models\MissionOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoStartMissions extends Command
{
    protected $signature = 'app:auto-start-missions';
    protected $description = 'Démarre automatiquement les missions validées dont la date de départ est atteinte';

    public function handle(): void
    {
        $now = Carbon::now();

        $missions = MissionOrder::where('statut', MissionOrder::STATUT_VALIDE)
            ->where('date_depart', '<=', $now)
            ->with(['vehicle', 'driver'])
            ->get();

        $count = 0;
        foreach ($missions as $mission) {
            $mission->update([
                'statut'     => MissionOrder::STATUT_EN_COURS,
                'started_at' => $mission->date_depart,
                'km_depart'  => $mission->vehicle?->kilometrage_actuel,
            ]);

            $mission->vehicle?->update(['statut_actuel' => 'En mission']);
            $mission->driver?->update(['statut_actuel' => 'En mission']);

            $count++;
        }

        $this->info("{$count} mission(s) démarrée(s) automatiquement.");
    }
}
