<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('vehicles.index'))->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('vehicles', VehicleController::class)->except(['create', 'edit', 'show']);
    Route::resource('drivers', DriverController::class)->except(['create', 'edit', 'show']);

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
