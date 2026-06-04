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
            Category::firstOrCreate(['slug' => Str::slug($name)],['name' => $name,'is_active' => true,'order' => $i + 1]);
        }
        $reagents = ['Biologie Moleculaire','Microbiologie','Culture Cellulaire','Genomique et Sequencage','CRISPR et Edition genique'];
        foreach ($reagents as $i => $name) {
            Category::firstOrCreate(['slug' => Str::slug($name)],['name' => $name,'is_active' => true,'order' => 100 + $i]);
        }
    }
}