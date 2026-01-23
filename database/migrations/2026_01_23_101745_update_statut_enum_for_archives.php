<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Mission Orders - ajouter 'archive' à l'enum
        DB::statement("ALTER TABLE mission_orders MODIFY COLUMN statut ENUM('brouillon', 'en_attente', 'valide', 'rejete', 'en_cours', 'cloture', 'archive') DEFAULT 'brouillon'");
        
        // Interventions - ajouter 'archive' à l'enum
        DB::statement("ALTER TABLE interventions MODIFY COLUMN statut ENUM('planifie', 'en_cours', 'termine', 'annule', 'archive') DEFAULT 'termine'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE mission_orders MODIFY COLUMN statut ENUM('brouillon', 'en_attente', 'valide', 'rejete', 'en_cours', 'cloture') DEFAULT 'brouillon'");
        DB::statement("ALTER TABLE interventions MODIFY COLUMN statut ENUM('planifie', 'en_cours', 'termine', 'annule') DEFAULT 'termine'");
    }
};