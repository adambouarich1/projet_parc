<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            // Identité
            $table->string('nom');
            $table->string('prenom');
            $table->string('matricule')->unique();
            $table->string('cin')->unique();
            $table->date('date_naissance')->nullable();
            $table->string('photo_path')->nullable();

            // Affectation & contact
            $table->string('service_affecte')->nullable()->index();
            $table->string('responsable_hierarchique')->nullable();
            $table->string('poste_occupe')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email_pro')->nullable();

            // Permis
            $table->string('num_permis')->nullable();
            $table->date('date_delivrance')->nullable();
            $table->date('date_expiration')->nullable();
            $table->string('categories')->nullable();
            $table->string('scan_permis_path')->nullable();

            // Statut
            $table->string('statut_actuel')->default('Disponible')->index();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
