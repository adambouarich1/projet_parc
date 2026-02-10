<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\VehicleController;
use App\Livewire\MissionOrders;
use App\Livewire\FuelEntries;
use App\Livewire\Interventions;
use App\Livewire\Alerts;
use App\Livewire\Insurances;
use App\Livewire\Archives;
use App\Livewire\Vignettes;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('vehicles.index'))->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Lecture : accessible à tous les connectés
    Route::get('vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('drivers', [DriverController::class, 'index'])->name('drivers.index');
    Route::get('missions', MissionOrders::class)->name('missions.index');
    Route::get('carburant', FuelEntries::class)->name('fuel.index');
    Route::get('interventions', Interventions::class)->name('interventions.index');
    Route::get('alertes', Alerts::class)->name('alerts.index');
    Route::get('assurances', Insurances::class)->name('insurances.index');
    Route::get('archives', Archives::class)->name('archives.index');
    Route::get('vignettes', Vignettes::class)->name('vignettes.index');

    // Modification : interdit aux utilisateurs "consultation"
    Route::middleware(['role:admin,responsable_parc,valideur,agent_saisie'])->group(function () {
        Route::post('vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::put('vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
        Route::delete('vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');

        Route::post('drivers', [DriverController::class, 'store'])->name('drivers.store');
        Route::put('drivers/{driver}', [DriverController::class, 'update'])->name('drivers.update');
        Route::delete('drivers/{driver}', [DriverController::class, 'destroy'])->name('drivers.destroy');
    });

    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';