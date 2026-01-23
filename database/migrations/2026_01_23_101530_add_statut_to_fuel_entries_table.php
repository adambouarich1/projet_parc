<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fuel_entries', function (Blueprint $table) {
            $table->enum('statut', ['en_attente', 'valide', 'refuse', 'archive'])
                  ->default('valide')
                  ->after('observations');
        });
    }

    public function down(): void
    {
        Schema::table('fuel_entries', function (Blueprint $table) {
            $table->dropColumn('statut');
        });
    }
};