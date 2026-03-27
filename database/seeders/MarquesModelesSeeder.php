<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Marque;
use App\Models\Modele;

class MarquesModelesSeeder extends Seeder
{
    public function run(): void
    {
        // Chemin vers le fichier JSON
        $jsonPath = database_path('seeders/marques_modeles_maroc.json');

        // Vérifier que le fichier existe
        if (!file_exists($jsonPath)) {
            $this->command->error("❌ Fichier JSON introuvable : {$jsonPath}");
            return;
        }

        // Lire et décoder le JSON
        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);

        if (!isset($data['marques'])) {
            $this->command->error("❌ Structure JSON invalide");
            return;
        }

        // Vider les tables avant import (optionnel)
        DB::table('modeles')->delete();
        DB::table('marques')->delete();

        $this->command->info("🚀 Importation des marques et modèles...");

        // Importer chaque marque et ses modèles
        foreach ($data['marques'] as $marqueData) {
            // Créer la marque
            $marque = Marque::create([
                'nom' => $marqueData['nom']
            ]);

            $this->command->info("✅ Marque : {$marque->nom}");

            // Créer les modèles de cette marque
            foreach ($marqueData['modeles'] as $modeleNom) {
                Modele::create([
                    'marque_id' => $marque->id,
                    'nom' => $modeleNom
                ]);
            }

            $this->command->info("   → " . count($marqueData['modeles']) . " modèles importés");
        }

        $this->command->info("🎉 Import terminé !");
    }
}