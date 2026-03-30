<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\FuelEntry;
use App\Models\Intervention;
use App\Models\MissionOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ArchiveMonthly extends Command
{
    protected $signature = 'app:archive-monthly';
    protected $description = 'Archive mensuellement les entités terminées des mois passés';

    public function handle(): void
    {
        $startOfCurrentMonth = now()->startOfMonth();

        // ── Missions clôturées des mois passés ─────────────────────────────
        $missions = MissionOrder::where('statut', MissionOrder::STATUT_CLOTURE)
            ->where('created_at', '<', $startOfCurrentMonth)
            ->get();

        foreach ($missions as $m) {
            $m->update(['statut' => MissionOrder::STATUT_ARCHIVE, 'archived_at' => now()]);
        }
        $this->info("{$missions->count()} mission(s) archivée(s).");

        // ── Interventions terminées ou annulées des mois passés ────────────
        $interventions = Intervention::whereIn('statut', [
                Intervention::STATUT_TERMINE,
                Intervention::STATUT_ANNULE,
            ])
            ->where('created_at', '<', $startOfCurrentMonth)
            ->get();

        foreach ($interventions as $i) {
            $i->update(['statut' => Intervention::STATUT_ARCHIVE, 'archived_at' => now()]);
        }
        $this->info("{$interventions->count()} intervention(s) archivée(s).");

        // ── Carburant : toutes les entrées actives des mois passés ─────────
        $fuels = FuelEntry::nonArchive()
            ->where('created_at', '<', $startOfCurrentMonth)
            ->get();

        foreach ($fuels as $f) {
            $f->update(['statut' => FuelEntry::STATUT_ARCHIVE, 'archived_at' => now()]);
        }
        $this->info("{$fuels->count()} entrée(s) carburant archivée(s).");

        // ── Alertes traitées ou vues des mois passés ───────────────────────
        $alerts = Alert::whereIn('statut', [Alert::STATUT_TRAITEE, Alert::STATUT_VUE])
            ->where('created_at', '<', $startOfCurrentMonth)
            ->get();

        foreach ($alerts as $a) {
            $a->update(['statut' => Alert::STATUT_ARCHIVEE, 'archived_at' => now()]);
        }
        $this->info("{$alerts->count()} alerte(s) archivée(s).");
    }
}
