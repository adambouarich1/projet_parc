<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Intervention;
use Carbon\Carbon;
use App\Models\Insurance;

class AlertService
{
    protected int $seuilPermis = 30;
    protected int $seuilAssurance = 30;
    protected int $seuilCT = 30;
    protected int $seuilVidangeKm = 500;

   public function generateAllAlerts(): array
    {
    $stats = [
        'drivers' => $this->checkDriversAlerts(),
        'vehicles' => $this->checkVehiclesAlerts(),
        'interventions' => $this->checkInterventionsAlerts(),
        'insurances' => $this->checkInsurancesAlerts(),
    ];

    return $stats;
            }

    public function checkDriversAlerts(): int
    {
        $count = 0;
        $today = Carbon::today();

        $drivers = Driver::whereNotNull('date_expiration')->get();

        foreach ($drivers as $driver) {
            $expiration = Carbon::parse($driver->date_expiration);
            $joursRestants = $today->diffInDays($expiration, false);

            if ($joursRestants < 0) {
                $count += $this->createOrUpdateAlert([
                    'type' => Alert::TYPE_PERMIS_EXPIRE,
                    'priorite' => Alert::PRIORITE_CRITIQUE,
                    'alertable_type' => Driver::class,
                    'alertable_id' => $driver->id,
                    'titre' => "Permis expiré - {$driver->nom} {$driver->prenom}",
                    'message' => "Le permis a expiré le {$expiration->format('d/m/Y')}.",
                    'date_echeance' => $expiration,
                    'jours_restants' => $joursRestants,
                ]);
            } elseif ($joursRestants <= $this->seuilPermis) {
                $count += $this->createOrUpdateAlert([
                    'type' => Alert::TYPE_PERMIS_BIENTOT,
                    'priorite' => $joursRestants <= 7 ? Alert::PRIORITE_HAUTE : Alert::PRIORITE_MOYENNE,
                    'alertable_type' => Driver::class,
                    'alertable_id' => $driver->id,
                    'titre' => "Permis expire bientôt - {$driver->nom} {$driver->prenom}",
                    'message' => "Le permis expire dans {$joursRestants} jours.",
                    'date_echeance' => $expiration,
                    'jours_restants' => $joursRestants,
                ]);
            }
        }

        return $count;
    }

    public function checkVehiclesAlerts(): int
{
    $count = 0;
    $today = Carbon::today();

    $vehicles = Vehicle::all();

    foreach ($vehicles as $vehicle) {
        // Dernier CT (on garde uniquement cette partie)
        $dernierCT = Intervention::where('vehicle_id', $vehicle->id)
            ->where('type', 'controle_technique')
            ->whereNotNull('date_expiration_ct')
            ->orderBy('date_expiration_ct', 'desc')
            ->first();

        if ($dernierCT) {
            $expiration = Carbon::parse($dernierCT->date_expiration_ct);
            $joursRestants = $today->diffInDays($expiration, false);

            if ($joursRestants < 0) {
                $count += $this->createOrUpdateAlert([
                    'type' => Alert::TYPE_CT_EXPIRE,
                    'priorite' => Alert::PRIORITE_CRITIQUE,
                    'alertable_type' => Vehicle::class,
                    'alertable_id' => $vehicle->id,
                    'titre' => "CT expiré - {$vehicle->immatriculation}",
                    'message' => "Le CT a expiré le {$expiration->format('d/m/Y')}.",
                    'date_echeance' => $expiration,
                    'jours_restants' => $joursRestants,
                ]);
            } elseif ($joursRestants <= $this->seuilCT) {
                $count += $this->createOrUpdateAlert([
                    'type' => Alert::TYPE_CT_BIENTOT,
                    'priorite' => $joursRestants <= 7 ? Alert::PRIORITE_HAUTE : Alert::PRIORITE_MOYENNE,
                    'alertable_type' => Vehicle::class,
                    'alertable_id' => $vehicle->id,
                    'titre' => "CT expire bientôt - {$vehicle->immatriculation}",
                    'message' => "Le CT expire dans {$joursRestants} jours.",
                    'date_echeance' => $expiration,
                    'jours_restants' => $joursRestants,
                ]);
            }
        }
    }

    return $count;
}

    public function checkInterventionsAlerts(): int
    {
        $count = 0;
        $today = Carbon::today();

        // Vidanges par date
        $vidanges = Intervention::where('type', 'entretien')
            ->whereNotNull('date_prochaine')
            ->where('statut', 'termine')
            ->with('vehicle')
            ->get();

        foreach ($vidanges as $vidange) {
            if (!$vidange->vehicle) continue;
            
            $dateProchaine = Carbon::parse($vidange->date_prochaine);
            $joursRestants = $today->diffInDays($dateProchaine, false);

            if ($joursRestants < 0) {
                $count += $this->createOrUpdateAlert([
                    'type' => Alert::TYPE_VIDANGE_DATE,
                    'priorite' => Alert::PRIORITE_HAUTE,
                    'alertable_type' => Vehicle::class,
                    'alertable_id' => $vidange->vehicle_id,
                    'titre' => "Vidange en retard - {$vidange->vehicle->immatriculation}",
                    'message' => "Vidange prévue le {$dateProchaine->format('d/m/Y')}.",
                    'date_echeance' => $dateProchaine,
                    'jours_restants' => $joursRestants,
                ]);
            } elseif ($joursRestants <= 15) {
                $count += $this->createOrUpdateAlert([
                    'type' => Alert::TYPE_VIDANGE_DATE,
                    'priorite' => Alert::PRIORITE_MOYENNE,
                    'alertable_type' => Vehicle::class,
                    'alertable_id' => $vidange->vehicle_id,
                    'titre' => "Vidange bientôt - {$vidange->vehicle->immatriculation}",
                    'message' => "Vidange prévue dans {$joursRestants} jours.",
                    'date_echeance' => $dateProchaine,
                    'jours_restants' => $joursRestants,
                ]);
            }
        }

        // Vidanges par km
        $vidangesKm = Intervention::where('type', 'entretien')
            ->whereNotNull('km_prochaine')
            ->where('statut', 'termine')
            ->with('vehicle')
            ->get();

        foreach ($vidangesKm as $vidange) {
            if (!$vidange->vehicle || !$vidange->vehicle->kilometrage_actuel) continue;
            
            $kmRestants = $vidange->km_prochaine - $vidange->vehicle->kilometrage_actuel;

            if ($kmRestants <= 0) {
                $count += $this->createOrUpdateAlert([
                    'type' => Alert::TYPE_VIDANGE_KM,
                    'priorite' => Alert::PRIORITE_HAUTE,
                    'alertable_type' => Vehicle::class,
                    'alertable_id' => $vidange->vehicle_id,
                    'titre' => "Vidange km dépassé - {$vidange->vehicle->immatriculation}",
                    'message' => "Vidange prévue à {$vidange->km_prochaine} km (actuel: {$vidange->vehicle->kilometrage_actuel} km).",
                    'date_echeance' => null,
                    'jours_restants' => null,
                ]);
            } elseif ($kmRestants <= $this->seuilVidangeKm) {
                $count += $this->createOrUpdateAlert([
                    'type' => Alert::TYPE_VIDANGE_KM,
                    'priorite' => Alert::PRIORITE_MOYENNE,
                    'alertable_type' => Vehicle::class,
                    'alertable_id' => $vidange->vehicle_id,
                    'titre' => "Vidange bientôt (km) - {$vidange->vehicle->immatriculation}",
                    'message' => "Vidange prévue dans {$kmRestants} km.",
                    'date_echeance' => null,
                    'jours_restants' => null,
                ]);
            }
        }

        return $count;
    }

    protected function createOrUpdateAlert(array $data): int
    {
        $existing = Alert::where('type', $data['type'])
            ->where('alertable_type', $data['alertable_type'])
            ->where('alertable_id', $data['alertable_id'])
            ->whereIn('statut', [Alert::STATUT_ACTIVE, Alert::STATUT_VUE])
            ->first();

        if ($existing) {
            $existing->update([
                'priorite' => $data['priorite'],
                'titre' => $data['titre'],
                'message' => $data['message'],
                'date_echeance' => $data['date_echeance'],
                'jours_restants' => $data['jours_restants'],
            ]);
            return 0;
        }

        Alert::create($data);
        return 1;
    }

    public function markAsTreated(Alert $alert, int $userId, ?string $notes = null): void
    {
        $alert->update([
            'statut' => Alert::STATUT_TRAITEE,
            'treated_by' => $userId,
            'treated_at' => now(),
            'notes_traitement' => $notes,
        ]);
    }

    public function markAsViewed(Alert $alert): void
    {
        if ($alert->statut === Alert::STATUT_ACTIVE) {
            $alert->update(['statut' => Alert::STATUT_VUE]);
        }
    }

    public function ignore(Alert $alert, int $userId, ?string $notes = null): void
    {
        $alert->update([
            'statut' => Alert::STATUT_IGNOREE,
            'treated_by' => $userId,
            'treated_at' => now(),
            'notes_traitement' => $notes,
        ]);
    }

    public function checkInsurancesAlerts(): int
{
    $count = 0;
    $today = Carbon::today();

    // Assurances actives ou expirées (pas archivées)
    $insurances = Insurance::whereIn('statut', ['active', 'expiree'])->with('vehicle')->get();

    foreach ($insurances as $insurance) {
        if (!$insurance->vehicle) continue;

        $expiration = Carbon::parse($insurance->date_expiration);
        $joursRestants = $today->diffInDays($expiration, false);

        if ($joursRestants < 0) {
            $count += $this->createOrUpdateAlert([
                'type' => Alert::TYPE_ASSURANCE_EXPIREE,
                'priorite' => Alert::PRIORITE_CRITIQUE,
                'alertable_type' => Vehicle::class,
                'alertable_id' => $insurance->vehicle_id,
                'titre' => "Assurance expirée - {$insurance->vehicle->immatriculation}",
                'message' => "L'assurance ({$insurance->assureur}) a expiré le {$expiration->format('d/m/Y')}.",
                'date_echeance' => $expiration,
                'jours_restants' => $joursRestants,
            ]);
        } elseif ($joursRestants <= $this->seuilAssurance) {
            $count += $this->createOrUpdateAlert([
                'type' => Alert::TYPE_ASSURANCE_BIENTOT,
                'priorite' => $joursRestants <= 7 ? Alert::PRIORITE_HAUTE : Alert::PRIORITE_MOYENNE,
                'alertable_type' => Vehicle::class,
                'alertable_id' => $insurance->vehicle_id,
                'titre' => "Assurance expire bientôt - {$insurance->vehicle->immatriculation}",
                'message' => "L'assurance ({$insurance->assureur}) expire dans {$joursRestants} jours.",
                'date_echeance' => $expiration,
                'jours_restants' => $joursRestants,
            ]);
        }
    }

    // Véhicules sans assurance active
    $vehiclesSansAssurance = Vehicle::whereDoesntHave('insurances', function($q) {
        $q->where('statut', 'active');
    })->get();

    foreach ($vehiclesSansAssurance as $vehicle) {
        $count += $this->createOrUpdateAlert([
            'type' => Alert::TYPE_ASSURANCE_EXPIREE,
            'priorite' => Alert::PRIORITE_CRITIQUE,
            'alertable_type' => Vehicle::class,
            'alertable_id' => $vehicle->id,
            'titre' => "Pas d'assurance - {$vehicle->immatriculation}",
            'message' => "Ce véhicule n'a aucune assurance active.",
            'date_echeance' => null,
            'jours_restants' => null,
        ]);
    }

    return $count;
}
}