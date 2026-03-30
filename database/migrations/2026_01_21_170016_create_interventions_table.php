<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interventions', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Type d'intervention
            $table->enum('type', [
                'entretien',
                'reparation',
                'controle_technique',
                'assurance',
                'autre'
            ])->index();
            
            $table->string('titre');
            $table->text('description')->nullable();
            
            // Dates
            $table->date('date_intervention');
            $table->date('date_prochaine')->nullable();
            
            // Kilométrage
            $table->unsignedInteger('kilometrage')->nullable();
            $table->unsignedInteger('km_prochaine')->nullable();
            
            // Coûts
            $table->decimal('cout_pieces', 12, 2)->default(0);
            $table->decimal('cout_main_oeuvre', 12, 2)->default(0);
            $table->decimal('cout_total', 12, 2)->default(0);
            
            // Prestataire
            $table->string('prestataire')->nullable();
            $table->string('numero_facture')->nullable();
            
            // Spécifique assurance
            $table->string('assureur')->nullable();
            $table->string('numero_police')->nullable();
            $table->date('date_expiration_assurance')->nullable();
            
            // Spécifique contrôle technique
            $table->date('date_expiration_ct')->nullable();
            $table->enum('resultat_ct', ['favorable', 'defavorable', 'contre_visite'])->nullable();
            
            // Statut
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'annule', 'archive'])->default('termine')->index();
            
            $table->text('observations')->nullable();
            
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};