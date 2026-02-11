<?php

namespace App\Livewire;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\MissionOrder;
use App\Models\Alert;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    #[\Livewire\Attributes\Layout('layouts.app')]

    public function render(): View
    {
        // Stats véhicules
        $totalVehicles = Vehicle::count();
        $vehiculesDisponibles = Vehicle::where('statut_actuel', 'En service')->count();
        $vehiculesIndisponibles = $totalVehicles - $vehiculesDisponibles;

        // Stats conducteurs
        $totalDrivers = Driver::count();
        $conducteursDisponibles = Driver::where('statut_actuel', 'Disponible')->count();
        $conducteursIndisponibles = $totalDrivers - $conducteursDisponibles;

        // Alertes récentes (non archivées, critiques et hautes en priorité)
        $alertesRecentes = Alert::nonArchive()
            ->with('alertable')
            ->orderByRaw("FIELD(priorite, 'critique', 'haute', 'moyenne', 'basse')")
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Missions du jour
        $missionsJour = MissionOrder::nonArchive()
            ->whereDate('date_depart', '<=', now())
            ->whereDate('date_retour_prevue', '>=', now())
            ->whereIn('statut', ['valide', 'en_cours'])
            ->with(['vehicle', 'driver'])
            ->limit(5)
            ->get();

        // Stats complémentaires
        $alertesCritiques = Alert::nonArchive()->critique()->count();

        return view('livewire.dashboard', [
            'totalVehicles' => $totalVehicles,
            'vehiculesDisponibles' => $vehiculesDisponibles,
            'vehiculesIndisponibles' => $vehiculesIndisponibles,
            'totalDrivers' => $totalDrivers,
            'conducteursDisponibles' => $conducteursDisponibles,
            'conducteursIndisponibles' => $conducteursIndisponibles,
            'alertesRecentes' => $alertesRecentes,
            'missionsJour' => $missionsJour,
            'alertesCritiques' => $alertesCritiques,
        ]);
    }
}