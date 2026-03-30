<?php

namespace App\Console\Commands;

use App\Models\Intervention;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckInterventionSchedule extends Command
{
    protected $signature = 'app:check-intervention-schedule';
    protected $description = 'Passe les véhicules en "En réparation" 48h avant une intervention planifiée';

    public function handle(): void
    {
        $deadline = Carbon::now()->addHours(48)->toDateString();

        $interventions = Intervention::where('statut', Intervention::STATUT_PLANIFIE)
            ->whereNotNull('date_intervention')
            ->where('date_intervention', '<=', $deadline)
            ->with('vehicle')
            ->get();

        $count = 0;
        foreach ($interventions as $intervention) {
            if ($intervention->vehicle && $intervention->vehicle->statut_actuel !== 'En réparation') {
                $intervention->vehicle->update(['statut_actuel' => 'En réparation']);
                $count++;
            }
        }

        $this->info("{$count} véhicule(s) passé(s) en \"En réparation\".");
    }
}
