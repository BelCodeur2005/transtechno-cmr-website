<?php
namespace Database\Seeders;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $equipment = ['Accouchement et Maternite','Biologie','Bloc Operatoire','Cardiologie','Consultation','Dentaire','Divers','Echographie','Endoscopie Interventionnelle','Endoscopie Rigide','Equipements Hospitaliers','Formation','Gastro-Enterologie','Gynecologie','Instrumentation','Kinesitherapie','Laboratoire','Medecine et Chirurgie Esthetique','Mobilier Medical','Monitoring','Nephrologie','Neurologie','Ophtalmologie','ORL','Orthopedie','Pneumologie','Protection','Reanimation','Soins Intensifs','Sterilisation','Urgence','Urologie','Veterinaire'];
        foreach ($equipment as $i => $name) {
            Category::updateOrCreate(['slug' => Str::slug($name)],['name' => $name,'type' => 'equipement','is_active' => true,'order' => $i + 1]);
        }
        // Réactifs ABM — slug stable (clé), nom affiché accentué + icône + description.
        // Le slug est figé pour préserver les liens produits/URLs même si le libellé change.
        $reagents = [
            ['slug' => 'biologie-moleculaire', 'name' => 'Biologie Moléculaire', 'icon' => 'biotech', 'description' => "Master Mix PCR & qPCR, RT-PCR, polymérases haute-fidélité, kits d'extraction et de purification ADN/ARN."],
            ['slug' => 'microbiologie', 'name' => 'Microbiologie', 'icon' => 'coronavirus', 'description' => "Milieux de culture, kits d'identification bactérienne et tests de sensibilité pour le diagnostic et la recherche."],
            ['slug' => 'culture-cellulaire', 'name' => 'Culture Cellulaire', 'icon' => 'science', 'description' => 'Lignées cellulaires, milieux, sérums et suppléments pour la culture in vitro en conditions contrôlées.'],
            ['slug' => 'genomique-et-sequencage', 'name' => 'Génomique & Séquençage', 'icon' => 'genetics', 'description' => 'Réactifs de préparation de librairies, enzymes de modification et solutions pour le séquençage nouvelle génération.'],
            ['slug' => 'crispr-et-edition-genique', 'name' => 'CRISPR & Édition génique', 'icon' => 'precision_manufacturing', 'description' => "Systèmes CRISPR-Cas9, ARN guides et vecteurs pour l'ingénierie génomique de précision."],
        ];
        foreach ($reagents as $i => $r) {
            Category::updateOrCreate(
                ['slug' => $r['slug']],
                ['name' => $r['name'], 'type' => 'reactif', 'icon' => $r['icon'], 'description' => $r['description'], 'is_active' => true, 'order' => 100 + $i],
            );
        }
    }
}