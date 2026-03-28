<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prix_carburant_unitaire', function (Blueprint $table) {
            $table->id();
            $table->string('type_carburant')->unique(); // 'essence' ou 'diesel'
            $table->decimal('prix', 10, 2);
            $table->timestamps();
        });

        // Insérer les valeurs par défaut
        DB::table('prix_carburant_unitaire')->insert([
            ['type_carburant' => 'essence', 'prix' => 13.50, 'created_at' => now(), 'updated_at' => now()],
            ['type_carburant' => 'diesel', 'prix' => 11.20, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('prix_carburant_unitaire');
    }
};