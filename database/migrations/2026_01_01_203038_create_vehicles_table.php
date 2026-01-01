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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            // Administratif
            $table->string('immatriculation')->unique();
            $table->string('vin')->nullable();
            $table->string('type_carte_grise')->nullable();
            $table->string('proprietaire_legal')->nullable();
            $table->string('service_affecte')->nullable()->index();
            $table->string('responsable_service')->nullable();
            $table->string('lieu_stationnement')->nullable();
            $table->date('date_mise_circulation')->nullable();
            $table->date('date_acquisition')->nullable();
            $table->string('mode_acquisition')->nullable();
            $table->decimal('puissance_fiscale', 8, 2)->nullable();
            $table->string('categorie_fiscale')->nullable();

            // Technique
            $table->string('marque');
            $table->string('modele');
            $table->string('version')->nullable();
            $table->string('categorie_vehicule')->nullable()->index();
            $table->string('carburant')->nullable()->index();
            $table->string('couleur')->nullable();
            $table->decimal('poids_vide', 10, 2)->nullable();
            $table->decimal('ptac', 10, 2)->nullable();
            $table->string('num_moteur')->nullable();
            $table->decimal('cylindree', 10, 2)->nullable();
            $table->decimal('puissance_din', 10, 2)->nullable();
            $table->decimal('capacite_reservoir', 10, 2)->nullable();
            $table->decimal('capacite_charge', 10, 2)->nullable();
            $table->unsignedInteger('nombre_places')->nullable();
            $table->unsignedInteger('kilometrage_initial')->default(0);
            $table->unsignedInteger('kilometrage_actuel')->default(0);
            $table->unsignedInteger('heures_moteur')->nullable();

            // Financier
            $table->decimal('prix_achat', 12, 2)->nullable();
            $table->string('fournisseur_nom')->nullable();
            $table->string('fournisseur_ice')->nullable();
            $table->string('bon_commande_ref')->nullable();
            $table->string('article_budgetaire')->nullable();
            $table->unsignedInteger('duree_amortissement')->nullable();
            $table->string('mode_amortissement')->nullable();
            $table->decimal('valeur_nette_comptable', 12, 2)->nullable();

            // Statut & médias
            $table->string('statut_actuel')->default('En service')->index();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
