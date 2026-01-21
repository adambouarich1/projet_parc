<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mission_orders', function (Blueprint $table) {
            $table->id();
            
            // Référence unique de l'OM (ex: OM-2026-00001)
            $table->string('reference')->unique();
            
            // Qui demande et qui valide
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Demandeur
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null'); // Valideur
            
            // Véhicule et conducteur assignés
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            
            // Détails de la mission
            $table->string('objet'); // Objet/motif de la mission
            $table->text('description')->nullable();
            $table->string('destination');
            $table->string('lieu_depart')->default('Siège');
            
            // Dates et heures
            $table->datetime('date_depart');
            $table->datetime('date_retour_prevue');
            $table->datetime('date_retour_effective')->nullable();
            
            // Kilométrage
            $table->unsignedInteger('km_depart')->nullable();
            $table->unsignedInteger('km_retour')->nullable();
            
            // Statut du workflow
            $table->enum('statut', [
                'brouillon',
                'en_attente',
                'valide',
                'rejete',
                'en_cours',
                'cloture',
                'annule'
            ])->default('brouillon')->index();
            
            // Commentaires
            $table->text('motif_rejet')->nullable();
            $table->text('observations')->nullable();
            
            // Dates de validation
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mission_orders');
    }
};