<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_entries', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('mission_order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Qui a saisi
            
            $table->date('date_plein');
            $table->decimal('quantite_litres', 10, 2);
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('montant_total', 12, 2);
            
            $table->unsignedInteger('kilometrage');
            $table->string('station')->nullable();
            $table->string('type_carburant')->nullable();
            $table->string('numero_bon')->nullable();
            
            $table->text('observations')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_entries');
    }
};