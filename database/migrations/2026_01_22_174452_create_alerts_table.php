<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            
            // Type d'alerte
            $table->enum('type', [
                'permis_expire',
                'permis_bientot',
                'assurance_expiree',
                'assurance_bientot',
                'ct_expire',
                'ct_bientot',
                'vidange_km',
                'vidange_date',
                'vignette_expiree',
                'vignette_bientot',
                'autre'
            ])->index();
            
            // Priorité
            $table->enum('priorite', ['basse', 'moyenne', 'haute', 'critique'])->default('moyenne')->index();
            
            // Entité concernée (polymorphique)
            $table->nullableMorphs('alertable');
            
            // Contenu
            $table->string('titre');
            $table->text('message')->nullable();
            
            // Dates
            $table->date('date_echeance')->nullable();
            $table->integer('jours_restants')->nullable();
            
            // Statut
            $table->enum('statut', ['active', 'vue', 'traitee', 'ignoree'])->default('active')->index();
            
            // Qui a traité
            $table->foreignId('treated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('treated_at')->nullable();
            $table->text('notes_traitement')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};