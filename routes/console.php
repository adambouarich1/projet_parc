<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Vérifie toutes les heures les interventions planifiées dans les 48h
// et passe les véhicules concernés en "En réparation"
Schedule::command('app:check-intervention-schedule')->hourly();

// Archive mensuellement les entités terminées des mois passés (1er du mois à minuit)
Schedule::command('app:archive-monthly')->monthlyOn(1, '00:01');

// Démarre automatiquement les missions validées à leur date de départ
Schedule::command('app:auto-start-missions')->everyMinute();

// Passe en "Terminé, attente de clôturation" les missions dont la date de retour est dépassée
Schedule::command('app:auto-close-missions')->everyMinute();
