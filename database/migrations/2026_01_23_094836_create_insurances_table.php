<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Infos assureur
            $table->string('assureur');
            $table->string('numero_police')->nullable();
            
            // Dates
            $table->date('date_debut');
            $table->unsignedInteger('duree_mois')->default(12);
            $table->date('date_expiration');
            
            // Montant
            $table->decimal('montant', 12, 2)->default(0);
            
            // Statut
            $table->enum('statut', ['active', 'expiree', 'archivee'])->default('active')->index();
            
            $table->text('observations')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurances');
    }
};